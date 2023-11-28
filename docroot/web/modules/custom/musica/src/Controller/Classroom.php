<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Spec\LastFM\ArtistEnum as artist;
use Drupal\musica\Spec\LastFM\APINamespaceEnum;

/**
 * Hello world.
 *
 * @package Drupal\music_api\Controller
 */
class Classroom extends ControllerBase {

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
    $render_array = [];

    // Assigning functions to variables.
    $concat2 = function (string $s1, string $s2): string {
    	return $s1. ' '. $s2;
     };
    $result = $concat2('Hello', 'World');
    $render_array[] = $this->output($result);


    return $render_array;
  }

  public function output($text = '') {
    return [
      '#type' => 'markup',
      '#markup' => "<p>{$text}</p>",
    ];
  }

}
