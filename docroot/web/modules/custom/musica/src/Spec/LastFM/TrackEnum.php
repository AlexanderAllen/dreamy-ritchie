<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Enumerates track API methods available, with parameters.
 */
enum TrackEnum {
  use YamlParametersTrait;

  case addTags;
  case getCorrection;
  case getInfo;
  case getSimilar;
  case getTags;
  case getTopTags;
  case love;
  case removeTag;
  case scrobble;
  case search;
  case unlove;
  case updateNowPlaying;
}
