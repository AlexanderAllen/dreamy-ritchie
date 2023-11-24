<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\LastFM\YamlParametersLastFMTrait;
use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Enumerates artist API methods available, with parameters.
 */
enum ArtistEnum {
  use YamlParametersTrait;
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

  /**
   * Return API namespace name.
   */
  public function __invoke() {
    return 'artist';
  }

}
