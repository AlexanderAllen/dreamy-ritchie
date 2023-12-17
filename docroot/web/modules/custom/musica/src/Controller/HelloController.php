<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\musica\Behavior\ArtistBehaviors;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Service\Spotify;
use Drupal\musica\State\EntityState;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;


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

  protected Spotify $spotify;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->lastfm = $container->get('musica.lastfm');
    $instance->spotify = $container->get('musica.spotify');
    return $instance;
  }

  /**
   * Serves Spotify's access token for external apps.
   */
  public function serveToken() {
    $token = $this->spotify->getToken();
    if ($token !== FALSE) {
      return new Response($token, 200, []);
    } else {
      return new Response('Resource not available', 400, []);
    }
  }

  /**
   * Display the markup.
   *
   * @return array
   *   Render array.
   */
  public function content() {

    $this->spotify->authorize();
    $content = $this->spotify->getResource();


    // $container = EntityContainer::createFromState(new ArtistBehaviors(), new EntityState('Cher'))
    // ->map('getInfo', $this->lastfm, ['limit' => 3]);
    // ->map('getSimilar', $this->lastfm, ['limit' => 3]); // OK 12/1, redone 12/4
    // ->map('getTags', $this->lastfm); // user not found
    // ->map('getTopAlbums', $this->lastfm, ['limit' => 10]); // OK 12/2, redone 12/4
    // ->map('getTopTags', $this->lastfm, ['limit' => 10]); // OK 12/13
    // ->map('getTopTracks', $this->lastfm, ['limit' => 2]) // OK 12/14
    // ->map('search', $this->lastfm, ['limit' => 3]); // OK 12/14

    // $container->dumpState();
    // $container->hydrate();


    // $behavior = $container->getBehaviorEntity();
    // $state = $container->getStateEntity();

    // d($state->data['getTopAlbums']);

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

    return $render_array;
  }

  /**
   * Example controller callback for displaying information about an artist.
   *
   * @param string $name
   * @return (string|EntityState)[][]
   */
  public function artist(string $name = '') {
    $state = EntityContainer::createFromState(new ArtistBehaviors(), new EntityState($name))
      ->map('getInfo', $this->lastfm, ['limit' => 3])
      ->hydrate()
      ->getStateEntity();

    $data = $state->getSiloData('dto', 'getInfo');

    $render_array = [];
    $render_array[] = [
      '#theme' => 'artist',
      '#name' => $name,
      '#state' => [
        'bio' => $data->artist->bio->content,
        'bio_short' => $data->artist->bio->summary,
      ],
    ];

    return $render_array;
  }

  public function titleCallback(string $name = '') {
    return $this->t('%name', ['%name'=> $name]);
  }

}
