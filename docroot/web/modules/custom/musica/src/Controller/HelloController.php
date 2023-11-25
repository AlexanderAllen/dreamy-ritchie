<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Spec\LastFM\ArtistEnum as artist;

use Drupal\musica\Spec\LastFM\YamlParametersLastFMTrait;
use Drupal\musica\Spec\YamlParametersTrait;
use PhpParser\Node\Expr\Instanceof_;

/**
 * Hello world.
 *
 * @package Drupal\music_api\Controller
 */
class HelloController extends ControllerBase {

  protected LastFM $lastfm;

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

    $api_key = $this->lastfm->apiKey;

    // Do an example artist getInfo request for prototyping.
    $request = [
      'api_key' => $this->lastfm->apiKey,
      'artist' => 'Cher'
    ];

    $container = EntityContainer::create(new ArtistBehaviors(), new EntityState('Cher'))->map('getInfo')->map('doesntexist')->map('anotherTest');

    // Deref'd container.
    // [$a, $b] = $o();
    $all = $container();
    $entity = $container(Dereferenced::ENTITY);
    $state = $container(Dereferenced::STATE);


    // 3.1 iteration - safe map wrap - returns a safe and sound entity if the method is not found,
    //    allows the chain to continue execution, maybe log into an internal object array errors.
    // 4th iteration - populate/transform entity with information from various api calls.
    // ...
    // 5th iteration - standarization - make one state entity to use across services, MUST USE INTERFACE
    // ...
    // 6th-8th iterations - pass and manipulate the entity between various containers.

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



    // Retrieve API call parameters from spec file.
    // $spec = artist::getInfo->parameters();

    // // Merge the spec with the user request and drop any empty parameters.
    // $merged_request = array_filter([...$spec, ...$request], fn ($value) => $value !== '');

    // // @todo can the response be mapped to a typed native object instead of stdClass?
    // $response = $this->lastfm->request($merged_request);

    $render_array = [];
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "hello world sample page",
    ];

    // $render_array[] = [
    //   '#type' => 'markup',
    //   '#markup' => "<p>{$response->artist->bio->summary}</p>",
    // ];

    return $render_array;
  }

}

enum Dereferenced {
  case ENTITY;
  case STATE;
}

// The Container provides the fluid chainable interface.
// the container does not implement behaviors, neither it stores or describes state.
class EntityContainer {
  private BehaviorsInterface $entity;

  // IF the container takes care of passing state around, the interface is easier to use for the user.
  // Otherwise the user has to initialize and manually pass around state.
  private EntityState $state;

  private function __construct(BehaviorsInterface $entity, EntityState $state) {
     $this->entity = $entity;
     $this->state = $state;
  }

  // Unit function
  // The user specifies which kind of entity they want to use.
  // The state is abstracted by the container.
  public static function create(BehaviorsInterface $entity, EntityState $state): EntityContainer {
    return new self($entity, $state);
  }

  /**
   * Executes a behavior on EntityState data, returning a container instance.
   *
   * @param mixed $b
   *   Method (behavior) to execute.
   * @param array $a
   *   Optional. Method arguments.
   *
   * @return EntityContainer
   */
  public function map($b, $a = []): EntityContainer {

    // IF the behavior does not exist in the target class, the shows go on.
    $new_state_ref = method_exists($this->entity, $b) ?
      call_user_func([$this->entity, $b], $this->state, $a) :
      $this->state;

    return self::create($this->entity, $new_state_ref);
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

  // @todo return safe object if method does not exist.
  public function __call($f, $args = []): EntityContainer {
    return $this->map($f, $args);
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

  // in the original example the transormed object (a string) is alwways returned to the caller
  // as opposed to doing modifications by refrence &.
  public function htmlspecialchars(EntityState $state) {
    return new EntityState(htmlspecialchars($state->name));
  }

  public function strtolower(EntityState $state) {
    // $this->name = strtolower($this->name); // this is a reference modification, don't do this.
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
    // this leads to a cannot modify readonly property, hooray!
    $state->data['mic_check'] = "<p>{$state->name} has some pipes.</p>";
    return $state;
  }
}

// Aim to make the state immutable.
class EntityState {
  // Entity name, e.g. 'Cher'.
  public readonly string $name;

  // @todo making this internal public is an anti-pattern, avoid it.
  // data as an arbitrary array is cool for prototyping, but...
  // the goal is to use a standard state interface for all entities.
  public readonly array $data;

  public function __construct(string $name, array $state = []) {
    $this->name = $name;
    $this->data = $state;
  }
}

readonly class ArtistBehaviors extends Behaviors {
  use YamlParametersTrait;
  use YamlParametersLastFMTrait;

  // The namespace is a description of the object itself and not a state.
  protected readonly string $namespace;
  public readonly string $bar;

  public function __construct() {
    $this->namespace = 'artist';
  }

  public function getInfo(EntityState $state): EntityState {
    $data['description'] = "<h1>{$state->name} is really cool.</h1>";
    return new EntityState($state->name, [...$state->data, ...$data]);
  }

}