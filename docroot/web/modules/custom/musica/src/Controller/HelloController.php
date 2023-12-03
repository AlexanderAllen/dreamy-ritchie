<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use CuyZ\Valinor\Mapper\Source\Source;
use Cuyz\Valinor\Mapper\MappingError;
use CuyZ\Valinor\MapperBuilder;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Behavior\BaseBehaviors;
use Drupal\musica\DTO\LFM\Attribute;
use Drupal\musica\DTO\LFM\EntityList;
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
    ->map('getSimilar', $this->lastfm, ['limit' => 10]);

    $behavior = $container->getBehaviorEntity();
    $state = $container->getStateEntity();

    $sig = 'array{similarartists: array{artist: ' . EntityList::class . ', "@attr": ' . Attribute::class . '} }';
    $dto = ArtistBehaviors::hydrateState($sig, $state, 'getSimilar');


    // 4.2 iteration - populate/transform entity with information from VARIOUS api calls.
    // ...
    // 5th iteration - standarization - make one state entity to use across services, MUST USE INTERFACE
    // ...
    // 6th-8th iterations - pass and manipulate the entity between various containers.
    //
    // 10th iteration - implement default render array in behaviors.

    // $dto = DTOMapper::map($state->data['getSimilar'], SimilarArtists:class);




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
 */
class ArtistBehaviors extends BaseBehaviors {

  public function __construct() {
    $this->namespace = 'artist';
    $this->assignBehaviors(ArtistEnum::cases());
  }

  /**
   * Reduce raw API state into a data transfer object.
   */
  public static function hydrateState(string $mapSignature, EntityState $state, string $dataKey) {
    $sauce = Source::json($state->data[$dataKey]);
    return (new MapperBuilder())
      ->mapper()
      ->map($mapSignature, $sauce);
  }

}
