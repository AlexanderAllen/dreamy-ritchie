<?php

namespace Drupal\Tests\musica\Unit\LFM\Artist\GetTopAlbums;

use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\Mapper\Tree\Message\NodeMessage;
use CuyZ\Valinor\Mapper\MappingError;
use Kint\Kint;

/**
 * Custom utlity class for debugging Valinator during Unit Testing (W.I.P.)
 *
 * @package Drupal\Tests\musica\Unit\LFM\Artist\GetTopAlbums
 *
 * @see https://valinor.cuyz.io/1.7/usage/validation-and-error-handling/
 */
class Debugger {

  public function __construct() {}

  public static function toCLI(MappingError $error) {
    Kint::$enabled_mode = Kint::MODE_CLI;
    Kint::$expanded = FALSE;
    Kint::$depth_limit = 1;
    // $kint = Kint::createFromStatics(Kint::getStatics());

    $messages = Messages::flattenFromNode(
      $error->node()
    );

    // Formatters can be added and will be applied on all messages
    // $messages = $messages->formatWith(
    //   new \CuyZ\Valinor\Mapper\Tree\Message\Formatter\MessageMapFormatter([
    //     'some_code' => 'New content / code: {message_code}',
    //     '1655449641' => function (NodeMessage $message) {
    //       $o = $message->originalMessage();
    //       return $o->body();
    //     }
    //   ]),
    // );

    // If only errors are wanted, they can be filtered
    $errorMessages = $messages->errors();
    foreach ($errorMessages as $message) {
      // d($message);
      // Kint::dump($message);
      $b = $message->body();

      /** @var \CuyZ\Valinor\Mapper\Tree\Message\Message */
      $om = $message->originalMessage();
      // @phpstan-ignore-next-line
      $oms = $om->getMessage();

      $node = $message->node();
      $path = $node->path();
      $type = $node->type();
      $valid = $node->isValid();

      printf("Original Message: %s, PATH: %s, TYPE: %s, VALID: %b \n", $oms, $path, $type, $valid);

      // Kint::dump($om);
      flush();
      ob_flush();
    }

  }

}
