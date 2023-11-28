<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Behavior\BaseBehaviors;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Spec\LastFM\ArtistEnum;
use Drupal\musica\State\EntityState;

/**
 * Hello world.
 *
 * @package Drupal\music_api\Controller
 */
class HelloController extends ControllerBase {

  /**
   * LFM Service located at the route controller.
   *
   * Created via dependency injection by Drupal's service container.
   */
  protected LastFM $lastfm;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->lastfm = $container->get('musica.lastfm');
    return $instance;
  }

  /**
   * Display the markup.
   *
   * @return array
   *   Render array.
   */
  public function content() {

    $container = EntityContainer::createFromState(new ArtistBehaviors(), new EntityState('Cher'))
    ->map('testInfo')
    ->map('doesntexist')
    ->map('getSimilar', $this->lastfm, ['limit' => 10]);


    // route controller initial candidate for cache.
    // entity level cache should be lower in the stack.

    $behavior = $container->getBehaviorEntity();
    $state = $container->getStateEntity();

    // 4.2 iteration - populate/transform entity with information from VARIOUS api calls.
    // ...
    // 5th iteration - standarization - make one state entity to use across services, MUST USE INTERFACE
    // ...
    // 6th-8th iterations - pass and manipulate the entity between various containers.
    //
    // 10th iteration - implement default render array in behaviors.

    $render_array = [];
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "hello world sample page",
    ];

    // $render_array[] = [
    //   '#type' => 'markup',
    //   '#markup' => "<p>Bio: {$state?->data['info']}</p>",
    // ];

    return $render_array;
  }

}

/**
 * Behavioral class for Artist entity.
 *
 * @todo look up data/json hydration patterns.
 */
class ArtistBehaviors extends BaseBehaviors {

  public function __construct() {
    $this->namespace = 'artist';
    $this->assignBehaviors(ArtistEnum::cases());
  }

}
