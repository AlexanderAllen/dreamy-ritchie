<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Spec\LastFM\ArtistEnum as artist;

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
    // $i = ServiceContainer::entity(new Entity('Cher'))->map('getInfo', ['tracks']);

    // $i2 = ServiceContainer::entity(new Entity('Cher'))->getInfo('bio');

    $i = ServiceContainer::entity(new Entity('<h1> Cher </h1>'))->map('htmlspecialchars')->map('strtolower') ;

    // Retrieve API call parameters from spec file.
    $spec = artist::getInfo->parameters();

    // Merge the spec with the user request and drop any empty parameters.
    $merged_request = array_filter([...$spec, ...$request], fn ($value) => $value !== '');

    // @todo can the response be mapped to a typed native object instead of stdClass?
    $response = $this->lastfm->request($merged_request);

    $render_array = [];
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "hello world sample page",
    ];

    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "<p>{$response->artist->bio->summary}</p>",
    ];

    return $render_array;
  }

}

// Think of Entity as a string... they're both objects, this one is custom.
class Entity {
  private $name;

  public function __construct($name) {
    $this->name = $name;
  }

  public function getInfo($args) {
    $this->name = "Here is some info for {$this->name}" . $args[0];
  }

  public function htmlspecialchars() {
    $this->name = htmlspecialchars($this->name);
  }

  public function strtolower() {
    $this->name = strtolower($this->name);
  }

  public function __invoke() {
    return new static($this->name);
  }
}

// i guess we could still transform the entity by populating it or performing actions on it
// but always returning the same object so it remains chainable.
// $i = ServiceContainer::entity(new Entity('Cher'))->map('getInfo');

// instead of map, __call the api method directly, chainable too?
class ServiceContainer {
  private Entity $object;

  private function __construct(Entity $object) {
     $this->object = $object;
  }

  // Unit function
  public static function entity(Entity $obj) {
     return new static($obj);
  }

  // this map is also meant to trasnform the value and return it.
  public function map($f, $args = []) {
    // $this->_value->getInfo() // works
    // wont' work bc entity method is expecting a value

    // this part is fine as hell
    call_user_func([$this->object, $f], $args);

    // This line does expect a result ... eh
    return static::entity($this->object);
  }

  // Print out the container
  public function __toString(): string {
     return "Container[ {$this->object} ]";
  }

  // Deference container
  public function __invoke() {
     return $this->object;
  }

  public function __call($f, $args = []) {
    $this->map($f, $args);
  }
}
