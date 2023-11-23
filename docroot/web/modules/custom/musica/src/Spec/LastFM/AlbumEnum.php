<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Enumerates album API methods available, with parameters.
 */
enum AlbumEnum {
  use YamlParametersTrait;

  case addTags;
  case getInfo;
  case getTags;
  case getTopTags;
  case removeTag;
  case search;
}
