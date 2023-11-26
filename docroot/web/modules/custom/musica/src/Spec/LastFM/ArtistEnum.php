<?php

namespace Drupal\musica\Spec\LastFM;

/**
 * Enumerates artist API methods available, with parameters.
 */
enum ArtistEnum {

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
