<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen

namespace Drupal\musica\DTO\LFM;

final class EntityList {

  public function __construct(
    /** @var list<Artist> */
    public readonly array $list,
  ) {}

}
