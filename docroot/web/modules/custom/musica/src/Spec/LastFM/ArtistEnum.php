<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\LastFM\YamlParametersLastFMTrait;

/**
 * Enumerates artist API methods available, with parameters.
 */
enum ArtistEnum {
  use YamlParametersLastFMTrait;

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
