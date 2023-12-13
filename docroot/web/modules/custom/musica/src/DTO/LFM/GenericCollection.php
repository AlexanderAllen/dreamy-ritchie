<?php

namespace Drupal\musica\DTO\LFM;

/**
 * @template T
 */
final class GenericCollection {

  public function __construct(
      /** @var T */
      public readonly mixed $collection,
  ) {}

}
