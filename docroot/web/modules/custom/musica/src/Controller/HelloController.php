<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Behavior\BehaviorInterface;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\Spec\LastFM\ArtistEnum;
use AllowDynamicProperties;

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

    $container = EntityContainer::createFromState(new LastFMArtistBehaviors(), new EntityState('Cher'))
    ->map('testInfo')
    ->map('doesntexist')
    ->map('getSimilar', $this->lastfm);

    // supress api calls temporarily
    // ->map('getBio', $this->lastfm)
    // ->map('getSimilar', $this->lastfm);

    // route controller initial candidate for cache.
    // entity level cache should be lower in the stack.

    // Deref'd container.
    // [$a, $b] = $o();
    // @todo I need the dereferences to be strongly typed, I don't want a union.
    $all = $container();
    $behavior = $container->getBehaviorEntity();
    $state = $container->getStateEntity();

    // $b = $behavior->defineBehaviors();
    // $exec = $b['getSimilar']($state);


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

    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "<p>Bio: {$state?->data['info']}</p>",
    ];


    return $render_array;
  }

}

enum Dereferenced {
  case BEHAVIOR;
  case STATE;
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
    $new_state_ref = method_exists($this->entity, $b) ?
      call_user_func([$this->entity, $b], $this->state, $s, $a) :
      $this->state;

    return self::createFromState($this->entity, $new_state_ref);
  }

  // Print out the container
  public function __toString(): string {
     return "Container [ {$this->state->name} ]";
  }

  // Deference container
  public function __invoke(Dereferenced $case = NULL): BehaviorInterface|EntityState|array {
    return match ($case) {
       Dereferenced::BEHAVIOR => $this->entity,
       Dereferenced::STATE => $this->state,
       default => [$this->entity, $this->state],
    };
  }

  public function getBehaviorEntity() {
    return $this->entity;
  }
  public function getStateEntity() {
    return $this->state;
  }
}

abstract class Behaviors implements BehaviorInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct() {}

  /**
   * {@inheritdoc}
   */
  public static function behaviors() {}

  /**
   * Working example method - escape entity name.
   */
  public function htmlspecialchars(EntityState $state) {
    return new EntityState(htmlspecialchars($state->name));
  }

  /**
   * Working example method - lowercase entity name.
   */
  public function strtolower(EntityState $state) {
    return new EntityState(strtolower($state->name));
  }

  /**
   * Calling this method throws an uncaught readonly prop violation error.
   *
   * This violation is great because it prevents the implementation from
   * causing side effects by reference inside the state.
   *
   * This method is just a unit test.
   */
  public function readOnlyViolation(EntityState $state): EntityState {
    $state->data['mic_check'] = "<p>{$state->name} has some pipes.</p>";
    return $state;
  }
}

/**
 * Immutable state entity.
 */
class EntityState {

  /**
   * Human-readable name of the state entity.
   *
   * Can be used by behavioral entities to make API calls.
   */
  public readonly string $name;

  /**
   * Private member containing the current state.
   *
   * @todo data as an arbitrary array is cool for prototyping, but...
   */
  public readonly array $data;

  public function __construct(string $name = '', array $state = []) {
    $this->name = $name;
    $this->data = $state;
  }

  /**
   * Merge and return an existing state instance with a new state instance.
   *
   * @param string $name
   *    The name for the new state instance.
   * @param EntityState $current_state
   *    The current state instance.
   * @param array $new_state
   *    Optional. Array of data to incorporate into the new state.
   *
   * @return EntityState
   *    A new state instance merged with the previous state.
   */
  public static function create(string $name = '', EntityState $current_state, array $new_state = []) {
    return new self($name, [...$current_state->data, ...$new_state]);
  }
}

#[AllowDynamicProperties]
class LastFMArtistBehaviors extends Behaviors {

  /**
   * The kind of behavioral object, used for API calls.
   */
  protected readonly string $namespace;

  public function __construct() {
    $this->namespace = 'artist';
    $this->assignBehaviors();
    // @todo should be parent::assignBehaviors(behavioralEnum);
    $test = null;
  }

  /**
   * Basic state test for container.
   */
  public function testInfo(EntityState $state): EntityState {
    $data['description'] = "<h1>{$state->name} is really cool.</h1>";
    return new EntityState($state->name, [...$state->data, ...$data]);
  }

  /**
   * Get the bio summary for an artist.
   *
   * @todo The behavior should remain service / API agnostic.
   *
   * Maybe there should be a service-specific facility from which
   * a service-agnostic behavior can grab data to in an abstracted manner.
   *
   * The behaviors would then populate a standardized state, indpendendent of
   * service.
   */
  public function getBio(EntityState $state, LastFM $service): EntityState {
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
  public function getSimilarTest(EntityState $state, LastFM $service): EntityState {
    $response = $service->request($this->namespace, 'getSimilar', [
      'artist' =>  $state->name,
      'limit' => 10,
    ]);
    $new = EntityState::create($state->name, $state, [
      'getSimilar' => $response,
    ]);
    return $new;
  }

  protected function assignBehaviors(): void {
    $behaviors = ArtistEnum::cases();
    array_walk($behaviors,
      fn ($behavior) => $this->{$behavior->name} = $this->createBehaviorHOF($behavior->name)
    );
  }

  // @todo additiona arbitrary call parameters need to be supported as needed
  protected function createBehaviorHOF(string $behavior_name): callable {
    return fn (EntityState $state, ServiceInterface $service, array $params = []) => (
      EntityState::create($state->name, $state, [
        $behavior_name => $service->request(
          $this->namespace,
          $behavior_name,
          ['artist' =>  $state->name, ...$params]
        ),
      ])
    );
  }

    // // Merge the spec with the user request and drop any empty parameters.
    // $merged_request = array_filter([...$spec, ...$request], fn ($value) => $value !== '');

  // if we end up doing a behavior factory, it should be in a base factory class.
  // the base churns out the method boilerplate, but the behaviors still hash out
  // the individual behaviors.

  /**
   * Oh hey hey, using closures we could factory out the calls based on the enum methods!
   * Behavioral logic would still need some methods...such as parsing the results.
   *
   * Also look up data/json hydration patterns.
   *
   * Before hashing out the rest of the artist behaviors or coming up w/ a
   * factory... check how to hydrate + standarize the state.
   */

}
