<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Enumerates tag API methods available, with parameters.
 */
enum TagEnum {
  use YamlParametersTrait;

  case getInfo;
  case getSimilar;
  case getTopAlbums;
  case getTopArtists;
  case getTopTags;
  case getTopTracks;
  case getWeeklyChartList;
}
