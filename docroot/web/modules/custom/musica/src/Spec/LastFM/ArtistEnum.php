<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Enumerates artist API methods available, with parameters.
 */
enum ArtistEnum {
  use YamlParametersTrait;

  case addTags;
  case getCorrection;
  case getInfo;
  case getSimilar;
  case getTags;
  case getTopAlbums;
  case getTopTags;
  case getTopTracks;
  case removeTag;
  case search;
}
