<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Enumerates chart API methods available, with parameters.
 */
enum ChartEnum {
  use YamlParametersTrait;

  case getTopArtists;
  case getTopTags;
  case getTopTracks;
}
