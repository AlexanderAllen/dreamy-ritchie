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

    //// Returning a function from a function
    function concatWith(string $a): callable {
      return function (string $b) use ($a): string {
        return $a . $b;
      };
    }
    $helloWith = concatWith('Hello');
    $helloWith('World'); //-> 'Hello World'

    // Supplying functions as parameters
    $add = function (float $a, float $b): float {
      return $a + $b;
    };

    function apply(callable $operator, $a, $b) {
     return $operator($a, $b);
    }
    apply($add, 5, 5);

    function applyMoreExpressive(callable $operator): callable {
      return function($a, $b) use ($operator) {
        return $operator($a, $b);
      };
    }
    $function = applyMoreExpressive($add);
    $result = $function(5, 5);
    // Or inline.
    $result = applyMoreExpressive($add)(5, 5);





    return $render_array;
  }

  public function output($text = '') {
    return [
      '#type' => 'markup',
      '#markup' => "<p>{$text}</p>",
    ];
  }

}
