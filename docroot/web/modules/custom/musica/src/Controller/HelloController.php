<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\musica\Behavior\ArtistBehaviors;
use Drupal\musica\Service\LastFM;
use Drupal\musica\State\EntityState;;
use Symfony\Component\DependencyInjection\ContainerInterface;


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
    ->map('getInfo', $this->lastfm, ['limit' => 3]);
    // ->map('getSimilar', $this->lastfm, ['limit' => 3]); // OK 12/1, redone 12/4
    // ->map('getTags', $this->lastfm); // user not found
    // ->map('getTopAlbums', $this->lastfm, ['limit' => 10]); // OK 12/2, redone 12/4
    // ->map('getTopTags', $this->lastfm, ['limit' => 10]); // OK 12/13
    // ->map('getTopTracks', $this->lastfm, ['limit' => 2]) // OK 12/14
    // ->map('search', $this->lastfm, ['limit' => 3]); // OK 12/14

    // $container->dumpState();
    $container->hydrate();


    $behavior = $container->getBehaviorEntity();
    $state = $container->getStateEntity();

    // d($state->data['getTopAlbums']);

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
