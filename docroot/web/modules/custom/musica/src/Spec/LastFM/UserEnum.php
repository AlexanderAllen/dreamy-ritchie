<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Enumerates user API methods available, with parameters.
 */
enum UserEnum {
  use YamlParametersTrait;

  case getFriends;
  case getInfo;
  case getLovedTracks;
  case getPersonalTags;
  case getRecentTracks;
  case getTopAlbums;
  case getTopArtists;
  case getTopTags;
  case getTopTracks;
  case getWeeklyAlbumChart;
  case getWeeklyArtistChart;
  case getWeeklyChartList;
  case getWeeklyTrackChart;
}
