<?php

namespace Drupal\music_api\Client\LastFM;

/**
 * Class for building Artist API requests.
 *
 * Class should be stateless (static) bc state should be stored somewhere else
 * in a controller, plugin, router item, etc.
 */
class Artist {

  public static $apiKey;

  public static $namespace = 'artist';

  /**
   * Get the metadata for an artist.
   *
   * Includes biog`raphy, truncated at 300 characters.
   *
   * @param string $artist
   *   Required (unless mbid)] : The artist name.
   * @param string $mbid
   *   The musicbrainz id for the artist.
   * @param string $lang
   *   The language to return the biography in, expressed as an ISO 639 alpha-2 code.
   * @param bool $autocorrect
   *   Transform misspelled artist names into correct artist names, returning the correct version instead. The corrected artist name will be returned in the response.
   * @param string $username
   *   The username for the context of the request. If supplied, the user's playcount for this artist is included in the response.
   */
  public static function getInfo(
    string $artist = '',
    string $mbid = '',
    string $lang = '',
    bool $autocorrect = FALSE,
    string $username = '',
    ) {
    $params = [
      'api_key' => self::$apiKey,
      'method' => self::$namespace . '.getInfo',
    ];

    // @todo wouldn't it be better to return a default request object, then allow the user to override as needed? more elegant?


    $o = new class (10) {
      public $artist = '';
    };

    return $o;
  }

}

$params = Artist::getInfo();

// option 1 - array, old-school, easy but undocumented (no interface or hinting).
$params['artist'] = 'Cher'; // submit request

// option 2 - better, documented w/ hinting
$params->artist = 'Cher'; // submit request





