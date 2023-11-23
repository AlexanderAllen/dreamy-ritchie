<?php

// phpcs:disable

namespace Drupal\music_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\music_api\Service\LastFM;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Drupal\music_api\Controller\FooEnum;

/**
 * Hello world.
 *
 * @package Drupal\music_api\Controller
 */
class HelloController extends ControllerBase {

  protected LastFM $lastfm;

  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->lastfm = $container->get('music_api.lastfm');
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
      'method' => 'artist.getInfo',
      'artist' => 'Cher'
    ];



    try {
      $status = FooEnum::Foo;
      // $status2 = Drupal\music_api\Controller\FooEnum::Bar; // relative dont work
      $status2 = \Drupal\music_api\Controller\FooEnum::Bar;


      // $value = Yaml::parseFile('/app/file.yaml', Yaml::PARSE_CONSTANT);
      $yaml = '{ bar: !php/enum \Drupal\music_api\Controller\FooEnum::Foo->value }';
      $parameters = Yaml::parse($yaml, Yaml::PARSE_CONSTANT);
      $foo = null;
    } catch (ParseException $exception) {
      printf('Unable to parse the YAML string: %s', $exception->getMessage());
    }

    // thing is...guzzle expects request params to be an associative array.
    // so whatever fancy interface shit we do, need to be converted back to a plain-jane assoc array.

    $response = $this->lastfm->request($request);


    // $params = Artist::getInfo();
    // $params->artist;


    $render_array = [];
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "hello world sample page",
    ];

    return $render_array;
  }

}

enum BazEnum: string
{
    case Foo = 'foo';
    case Bar = 'bar';
}

/**
 * Tester class.
 */
class Artist {

  public static function getInfo() {
    return new class () {
      public $artist = '';
      public $mbid = '';
      public $lang = '';
      public bool $autocorrect;
      public $username = '';
    };
  }

}

/**
 * Atist interface.
 */
interface ArtistInterface {
  public static function getInfo();
}

// we could have a single interface method for artist, such as
// public static build('getInfo')

// or one method for each api call.

// option 3 - use symfony yaml to read call configuration parameters, and dynamically
// return anonymous objects based on that, goes with option 1 above.

enum Suit: string {
  case Hearts = 'H';
  case Diamonds = 'D';
  case Clubs = 'C';
  case Spades = 'S';
}

enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}

