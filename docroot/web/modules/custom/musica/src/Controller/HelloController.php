<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Behavior\BehaviorInterface;
use Drupal\musica\Behavior\BaseBehaviors;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Service\ServiceInterface;
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

    // supress api calls temporarily
    // ->map('getBio', $this->lastfm)
    // ->map('getSimilar', $this->lastfm);

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
 * Fluid design pattern container.
 *
 * This container provides an interface for chaining calls provided by
 * behavioral entities implementing the BehaviorInterface interface.
 *
 * The state is stored inside a single immutable EntityState instance,
 * which this container stores a reference to, along with the current
 * behavioral instance.
 *
 * You can directly invoke the container at any time to get a dereferenced copy
 * of the container's current behavior and state.
 */
class EntityContainer {
  private BehaviorInterface $entity;
  private EntityState $state;

  /**
   * Private constructor.
   */
  private function __construct(BehaviorInterface $entity, EntityState $state) {
     $this->entity = $entity;
     $this->state = $state;
  }

  /**
   * Creates and returns a container with specified behavior and a empty state.
   *
   * Behaviors can still alter the state as they like.
   */
  public static function create(BehaviorInterface $entity): EntityContainer {
    return new self($entity, new EntityState());
  }

  /**
   * Creates and returns a container linked to the specified behavior and state.
   */
  public static function createFromState(BehaviorInterface $entity, EntityState $state): EntityContainer {
    return new self($entity, $state);
  }

  /**
   * Executes a behavior on EntityState data, returning a container instance.
   *
   * This is a safe method. If the call does not exist on the callee an
   * instance of the container is returned so business can resume as usual.
   *
   * @param mixed $b
   *   Method (behavior) to execute.
   * @param array $a
   *   Optional. Method arguments.
   *
   * @return EntityContainer
   *   Always returns an instance of EntityContainer.
   */
  public function map(string $b, ServiceInterface $s = NULL, array $a = []): EntityContainer {
    $new_state_ref = $this->state;

    // For built-in class methods.
    if (method_exists($this->entity, $b)) {
      $new_state_ref = call_user_func([$this->entity, $b], $this->state, $s, $a);
    } else {
      $closure = $this->entity->getBehavior($b);
      $new_state_ref = $closure($this->state, $s, $a);
    }

    return self::createFromState($this->entity, $new_state_ref);
  }

  public function getBehaviorEntity() {
    return $this->entity;
  }
  public function getStateEntity() {
    return $this->state;
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

  /**
   * Basic state test for container.
   *
   * @todo move to unit test.
   */
  public function testInfo(EntityState $state): EntityState {
    $data['description'] = "<h1>{$state->name} is really cool.</h1>";
    return new EntityState($state->name, [...$state->data, ...$data]);
  }

  /**
   * Get the bio summary for an artist.
   *
   * Maybe there should be a service-specific facility from which
   * a service-agnostic behavior can grab data to in an abstracted manner.
   *
   * The behaviors would then populate a standardized state, indpendendent of
   * service.
   *
   * @todo move to unit testing as well.
   */
  public function getBio(EntityState $state, ServiceInterface $service): EntityState {
    $response = $service->request($this->namespace, 'getInfo', [
      'artist' =>  $state->name,
    ]);

    $new = EntityState::create($state->name, $state, [
      'info' => $response?->artist?->bio?->summary,
    ]);
    return $new;
  }

  /**
   * Implements artist.getSimilar API call.
   *
   * @todo should be implementing throwables at the service for API errors.
   */
  public function getSimilarTest(EntityState $state, ServiceInterface $service): EntityState {
    $response = $service->request($this->namespace, 'getSimilar', [
      'artist' =>  $state->name,
      'limit' => 10,
    ]);
    $new = EntityState::create($state->name, $state, [
      'getSimilar' => $response,
    ]);
    return $new;
  }

}
