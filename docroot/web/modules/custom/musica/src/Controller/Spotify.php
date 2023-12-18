<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\musica\Service\Spotify as SpotifyService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;


/**
 * Dedicated Controller for Spotify API actions.
 *
 * @package Drupal\music_api\Controller
 */
class Spotify extends ControllerBase {


  protected SpotifyService $spotify;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->spotify = $container->get('musica.spotify');
    return $instance;
  }

  /**
   * Serves Spotify's access token for external apps.
   */
  public function serveToken() {
    $token = $this->spotify->getToken();
    if ($token !== FALSE) {
      return new Response("Bearer {$token}", 200, []);
    } else {
      return new Response('Resource not available', 400, []);
    }
  }

  /**
   * Serves Spotify's access token for external apps.
   */
  public function serveTokenJson() {
    $token = $this->spotify->getToken();
    if ($token !== FALSE) {
      return new Response('{"access_token": "' . $token . '"}', 200, []);
    } else {
      return new Response('Resource not available', 400, []);
    }
  }

  /**
   * Authorize route.
   */
  public function authorize() {

    $this->spotify->authorize();

    $render_array = [];
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "Authorization successful",
    ];

    return $render_array;
  }


}
