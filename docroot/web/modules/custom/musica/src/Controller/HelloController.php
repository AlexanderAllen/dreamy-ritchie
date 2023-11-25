<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Spec\LastFM\ArtistEnum as artist;

use Drupal\musica\Spec\LastFM\YamlParametersLastFMTrait;
use Drupal\musica\Spec\YamlParametersTrait;
use Entity;

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
    ->map('getBio', $this->lastfm);

    // Deref'd container.
    // [$a, $b] = $o();
    $all = $container();
    $entity = $container(Dereferenced::ENTITY);
    $state = $container(Dereferenced::STATE);

    // 4th iteration - populate/transform entity with information from various api calls.
    // ...
    // 5th iteration - standarization - make one state entity to use across services, MUST USE INTERFACE
    // ...
    // 6th-8th iterations - pass and manipulate the entity between various containers.
    //
    // 10th iteration - implement default render array in behaviors.

    // some other ideas for further iteration
    // how about ServiceContainer::artist('Cher) -> creates artist entity out of a base shareable entity
    // so ServiceContainer::artist('Cher')->getInfo()->getBio() // getBio could also call the first if data not populated.
    // Also this container is more like an entity container
    // perhaps is best to have a separate service container, that can contain the entity container
    // so...
    // ServiceContainerLastFM::EntityContainer::entity->method1()->method2;
    // LastFM (implements ServiceContainer)::Artist (EntityContainer) :: entity->method1
    // LastFM::Artist::method1
    // or
    //

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
  case ENTITY;
  case STATE;
}

/**
 * Fluid design pattern container.
 *
 * This container provides an interface for chaining calls provided by
 * behavioral entities implementing the BehaviorsInterface interface.
 *
 * The state is stored inside a single immutable EntityState instance,
 * which this container stores a reference to, along with the current
 * behavioral instance.
 *
 * You can directly invoke the container at any time to get a dereferenced copy
 * of the container's current behavior and state.
 */
class EntityContainer {
  private BehaviorsInterface $entity;
  private EntityState $state;

  /**
   * Private constructor.
   */
  private function __construct(BehaviorsInterface $entity, EntityState $state) {
     $this->entity = $entity;
     $this->state = $state;
  }

  /**
   * Creates and returns a container with specified behavior and a empty state.
   *
   * Behaviors can still alter the state as they like.
   */
  public static function create(BehaviorsInterface $entity): EntityContainer {
    return new self($entity, new EntityState());
  }

  /**
   * Creates and returns a container linked to the specified behavior and state.
   */
  public static function createFromState(BehaviorsInterface $entity, EntityState $state): EntityContainer {
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
  public function map($b, $a = []): EntityContainer {
    $new_state_ref = method_exists($this->entity, $b) ?
      call_user_func([$this->entity, $b], $this->state, $a) :
      $this->state;

    return self::createFromState($this->entity, $new_state_ref);
  }

  /**
   * Magic call implementation calls methods on the current behavior instance.
   *
   * @param mixed $b Behavior to be called.
   * @param array $a Optional. Method arguments.
   *
   * @return EntityContainer
   *   Always returns an instance of EntityContainer.
   */
  public function __call($b, $a = []): EntityContainer {
    return $this->map($b, $a);
  }

  // Print out the container
  public function __toString(): string {
     return "Container [ {$this->state->name} ]";
  }

  // Deference container
  public function __invoke(Dereferenced $case = NULL): BehaviorsInterface|EntityState|array {
    return match ($case) {
       Dereferenced::ENTITY => $this->entity,
       Dereferenced::STATE => $this->state,
       default => [$this->entity, $this->state],
    };
  }
}

/**
 * Interface for behavioral entities.
 *
 * Behavioral entities CAN and SHOULD alter state, but they SHOULD NOT own,
 * store, or otherwise reference any state of any kind in any way.
 *
 * The behavior methods should always receive and return an instance of
 * EntityState back to the caller, or in other words, aim to behave more like
 * pure functions and less like OOP class methods.
 */
interface BehaviorsInterface {

  /**
   * Stateless behavior constructor.
   *
   * DO NOT pass any kind of state to behavior constructors.
   * Otherwise the state entities becaome coupled to the behavioral entities.
   */
  public function __construct();
}

readonly class Behaviors implements BehaviorsInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct() {}

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
  // Entity name, e.g. 'Cher'.
  public readonly string $name;

  // data as an arbitrary array is cool for prototyping, but...
  // the goal is to use a standard state interface for all entities.
  public readonly array $data;

  public function __construct(string $name = '', array $state = []) {
    $this->name = $name;
    $this->data = $state;
  }

  public static function create(string $name = '', EntityState $current_state, array $new_state = []) {
    return new self($name, [...$current_state->data, ...$new_state]);
  }
}

readonly class ArtistBehaviors extends Behaviors {
  use YamlParametersTrait;
  use YamlParametersLastFMTrait;

  /**
   * The kind of behavioral object, used for API calls.
   */
  protected readonly string $namespace;

  public function __construct() {
    $this->namespace = 'artist';
  }

  public function testInfo(EntityState $state): EntityState {
    $data['description'] = "<h1>{$state->name} is really cool.</h1>";
    return new EntityState($state->name, [...$state->data, ...$data]);
  }

  /**
   * Initial test integration method.
   *
   * Has to integrate with the specified API.
   * The Service (API) cannot be hardcoded or linked to the behavior.
   * The behavior should remain service / API agnostic.
   *
   * Currently the router controller is being linked to a specific service by
   * Drupal's dependency injection facilities.
   *
   * As an option, the service could easily become part of EntityState.
   * Continue to keep the behavior state and relationship agnostic at all costs.
   *
   * If we put business logic tied to a particular service in the behavior,
   * it would make the behavior service-specific, requiring more behaviors to be written.
   *
   * Maybe there should be a service-specific facility from which
   * a service-agnostic behavior can grab data to in an abstracted manner.
   *
   * The name or instance of the service can then be specified in the state
   * being given to the behavior to work on.
   *
   * The behaviors would then populate a standardized state, indpendendent of
   * service.
   *
   * This would allow the logic to read state indpendently of which service
   * last created or modified the state.
   *
   * In addition to the ALWAYS PRESENT state, behaviors can also accept
   * optional arguments, which could be a place to insert things like which
   * service to act on, although this would introduce coupling as well.
   *
   * At some point biz logic needs to be encapsulated somewhere, and the behavior
   * classes are the specialists and candidate entities best places to do this atm.
   *
   * The entitiy controller is already good as it is tracking the behavior and state entities
   * in one place.
   */
  public function getBio(EntityState $state, LastFM $service): EntityState {

    $api_key = $service->apiKey;

    // Do an example artist getInfo request for prototyping.
    $request = [
      'api_key' => $api_key,
      'artist' =>  $state->name,
    ];

    // @todo: all the service logic could also just stay in the service,
    // including api key, request building, etc.
    // Here the rquest parameters are grabbed from a service-specific object (an enum!
    $spec = artist::getInfo->parameters();

    // // Merge the spec with the user request and drop any empty parameters.
    $merged_request = array_filter([...$spec, ...$request], fn ($value) => $value !== '');

    // // @todo can the response be mapped to a typed native object instead of stdClass?
    $response = $service->request($merged_request);

    $new = EntityState::create($state->name, $state, [
      'info' => $response?->artist?->bio?->summary,
    ]);
    return $new;

  }

}