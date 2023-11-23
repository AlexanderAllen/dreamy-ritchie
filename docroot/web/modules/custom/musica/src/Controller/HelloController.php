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
      'method' => 'artist.getInfo', // @todo can this be added to the spec? the method name is already in the enum invoked.
      'artist' => 'Cher'
    ];

    // Retrieve API call parameters from spec file.
    $spec = artist::getInfo->parameters('artist');

    // Merge the spec with the user request.
    $merged_request = [...$spec, ...$request];
    $cleaned_request = array_filter($merged_request, function ($value) {
      return ($value !== '');
    });

    // @todo can the response be mapped to a typed native object instead of stdClass?
    $response = $this->lastfm->request($cleaned_request);

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
