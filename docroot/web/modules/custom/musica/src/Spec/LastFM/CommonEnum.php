<?php

namespace Drupal\musica\Spec\LastFM;

/**
 * Enumerates common API methods shared by album, artist, and track.
 */
enum CommonEnum {

  case addTags;
  case getInfo;
  case getTags;
  case getTopTags;
  case removeTag;
  case search;
}
