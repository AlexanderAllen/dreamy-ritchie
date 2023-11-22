<?php

namespace Drupal\music_api\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Hello world.
 *
 * @package Drupal\music_api\Controller
 */
class HelloController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Render array.
   */
  public function content() {
    $render_array = [];
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => "hello world sample page",
    ];

    return $render_array;
  }

}
