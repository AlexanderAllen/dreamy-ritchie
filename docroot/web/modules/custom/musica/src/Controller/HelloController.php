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


    // second attempt.
    // $i2 = ServiceContainer::entity(new Entity('Cher'))->getInfo('bio');

    // second iteration, using custom objects, Entity in this case, instead of native scalars.
    // $i = EntityContainer::create(new ArtistEntity('<h1> Cher </h1>'))->map('htmlspecialchars')->map('strtolower') ;



    $container = EntityContainer::create(new ArtistBehaviors(), new EntityState('Cher'))->getInfo();
    $container instanceof EntityContainer;

    // Deref'd container.
    // [$a, $b] = $o();
    $all = $container();
    $entity = $container(Dereferenced::ENTITY);
    $state = $container(Dereferenced::STATE);




    // $s = new EntityState('Cherokee');
    // $o2 = EntityContainer::create(new ArtistEntity(), $s)->map('htmlspecialchars');

    // 3rd iteration - wrap map with magic __call method.
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
  public static function create(BehaviorsInterface $entity, EntityState $state) {
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
  public function map($b, $a = []) {
    $new_state_ref = call_user_func([$this->entity, $b], $this->state, $a);
    return self::create($this->entity, $new_state_ref);
  }

  // Print out the container
  public function __toString(): string {
     return "Container [ {$this->state->name} ]";
  }

  // Deference container
  public function __invoke(Dereferenced $case = NULL) {
    return match ($case) {
       Dereferenced::ENTITY => $this->entity,
       Dereferenced::STATE => $this->state,
       default => [$this->entity, $this->state],
    };
  }

  // @todo return safe object if method does not exist.
  // @todo map is returning a container so it can be chained, but call is not.
  public function __call($f, $args = []) {
    return $this->map($f, $args);
  }
}

interface BehaviorsInterface {}

// Think of Entity as a string... they're both objects, this one is custom.
// Again, these entities are good for defining behavior not storing state.
// The behavior offered can be then used to alter any state passed down to the entity.
readonly class Behaviors implements BehaviorsInterface {

  // in the original example the transormed object (a string) is alwways returned to the caller
  // as opposed to doing modifications by refrence &.
  public function htmlspecialchars(EntityState $state) {
    return new EntityState(htmlspecialchars($state->name));
  }

  public function strtolower(EntityState $state) {
    // $this->name = strtolower($this->name); // this is a reference modification, don't do this.
    return new EntityState(strtolower($state->name));
  }
}

// Aim to make the state immutable.
class EntityState {
  // Entity name, e.g. 'Cher'.
  public readonly string $name;

  // making this internal public is an anti-pattern, avoid it
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

  // DO NOT PASS STATE TO THE ENTITY CONSTRUCTOR.
  // Otherwise the entity gets coupled to state.
  public function __construct() {
    $this->namespace = 'artist';
    $this->bar = 'foo';
  }

  public function foo() {
    // violate read-only for testing
    $this->bar = 'foo';
  }


  // Behavioral objects can and shoul modify data, but never by reference.
  // transformations should be pure, no internal or external references.
  // it's ok to modify objects (strings are objects), but you have to return the object.
  // that way the caller explicetly can see the transformation on the subject
  public function getInfo(EntityState $state) {

    $data['description'] = "<h1>{$state->name} is really cool.</h1>";

    return new EntityState($state->name, [...$state->data, ...$data]);
  }

}