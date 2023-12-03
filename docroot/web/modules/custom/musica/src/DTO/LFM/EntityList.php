<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen
// phpcs:disable Drupal.Classes.UnusedUseStatement.UnusedUse

namespace Drupal\musica\DTO\LFM;

use Drupal\musica\DTO\LFM\Artist;

final class EntityList {

  public function __construct(
    /** @var list<Artist> */
    public readonly array $list,
  ) {}

}
