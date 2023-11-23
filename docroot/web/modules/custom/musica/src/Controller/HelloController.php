<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Service\LastFM;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Drupal\musica\Controller\FooEnum;

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

    $getInfo = 'getInfo';
    $spec = SpecArtistEnum::getInfo->parameters();


    $merged_request = array_merge($spec, $request);
    $cleaned_request = array_filter($merged_request, function ($value) {
      return ($value !== '') ? TRUE : FALSE;
    });
    $response = $this->lastfm->request($cleaned_request);


    $render_array = [];
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "hello world sample page",
    ];

    return $render_array;
  }

}

trait YamlSpecParameters {
  public function parameters(): array {

    try {
      $parsed_spec = Yaml::parseFile('/app/file.yaml', Yaml::PARSE_CONSTANT);
    } catch (ParseException $exception) {
      printf('Unable to parse the YAML string: %s', $exception->getMessage());
    }
    return $parsed_spec[$this->name] ??= NULL;
  }
}

enum SpecArtistEnum
{
  case getInfo;
  case getSimilar;

  use YamlSpecParameters;
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

// enum Status: string
// {
//     case DRAFT = 'draft';
//     case PUBLISHED = 'published';
//     case ARCHIVED = 'archived';
// }

