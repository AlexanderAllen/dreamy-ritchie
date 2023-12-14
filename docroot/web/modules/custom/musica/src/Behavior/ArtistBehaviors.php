<?php

// phpcs:disable Drupal.Commenting.DataTypeNamespace.DataTypeNamespace

namespace Drupal\musica\Behavior;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Drupal\musica\DTO\LFM\EntityList;
use Drupal\musica\DTO\LFM\GenericCollection;
use Drupal\musica\Spec\LastFM\ArtistEnum;
use Drupal\musica\State\EntityState;

/**
 * Behavioral class for Artist entity.
 */
class ArtistBehaviors extends BaseBehaviors {

  public function __construct() {
    parent::__construct('artist', ArtistEnum::class, ArtistDTOMap::class);
  }

  /**
   * Hyrdrates and returns a new EntityState.
   */
  public static function hydrateState(EntityState $state, string $dataKey): EntityState {
    $sauce = Source::json($state->data[$dataKey]);
    /** @var array */
    $dto = (new MapperBuilder())
      ->allowSuperfluousKeys()
      ->allowPermissiveTypes()
      ->enableFlexibleCasting()
      ->mapper()
      ->map(self::$shapes[$dataKey], $sauce);

    return $state::mergeStateSilo('dto', $state, [$dataKey => $dto]);
  }

}

/**
 * Provides DTO map for hydrating incoming Artist data.
 *
 * @todo shapes enum can be merged with behaviors enum.
 */
enum ArtistDTOMap: string {

  case addTags = 'addTags';
  case getCorrection = 'getCorrection';
  case getInfo = 'getInfo';
  case getSimilar = 'array{similarartists: array{artist: ' . EntityList::class . ', "@attr": ' . Attribute::class . '} }';
  case getTags = 'getTags';
  case getTopAlbums = GenericCollection::class . '<Drupal\musica\Behavior\TopAlbums>';
  case getTopTags = GenericCollection::class . '<Drupal\musica\Behavior\TopTags>';
  case getTopTracks = GenericCollection::class . '<Drupal\musica\Behavior\TopTracks>';
  case removeTag = 'removeTag';
  case search = GenericCollection::class . '<Drupal\musica\Behavior\Search>';
}

/**
 * Data transfer object for artist.search.
 *
 * @phpstan-type RootValue array{
 *  artistmatches: array{artist: list<Artist>},
 *  "opensearch:Query": array,
 *  "opensearch:totalResults": int,
 *  "opensearch:startIndex": int,
 *  "opensearch:itemsPerPage": int,
 *  "@attr"?: Attribute
 * }
 *
 * @see https://www.last.fm/api/show/artist.search
 */
final class Search {

  public function __construct(
    /** @var RootValue $results */
    public readonly mixed $results,
  ) {}

}

/**
 * Data transfer object for artist.getTopTracks.
 *
 * @phpstan-type RootValue array{'track': list<Track>, "@attr"?: Attribute}
 *
 * @see https://www.last.fm/api/show/artist.getTopTracks
 * @todo inner Track @attr (ranking) is lost because of Valinor limiations.
 */
final class TopTracks {

  public function __construct(
    /** @var RootValue $toptracks */
    public readonly mixed $toptracks,
  ) {}

}

final class Track {

  public function __construct(
    public readonly Artist $artist,
    public readonly string $name = '',
    public readonly string $mbid = '',
    public readonly int $playcount = 0,
    public readonly int $listeners = 0,
    public readonly string $url = '',
    /** @var list<ImageProps> */
    public readonly array $image = [],
  ) {}

}


/**
 * @phpstan-type tags array{'tag': list<Tag>, "@attr"?: Attribute}
 */
final class TopTags {

  public function __construct(
    /** @var tags $toptags */
    public readonly mixed $toptags,
  ) {}

}

final class Tag {

  public function __construct(
    public readonly int $count = 0,
    public readonly string $name = '',
    public readonly string $url = '',
  ) {}

}


class ImageProps {
  public readonly string $text;
  public readonly string $size;

  public function __construct(...$args) {
    [$this->text, $this->size] = $args;
  }

}

/**
 * Data transfer object for artist.getTopAlbums.
 *
 * @phpstan-type RootValue array{'album': list<Album>, "@attr"?: Attribute}
 *
 * @see https://www.last.fm/api/show/artist.getTopAlbums
 */
final class TopAlbums {

  public function __construct(
    /** @var RootValue $topalbums */
    public readonly array $topalbums,
  ) {}

}

final class Album {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var non-negative-int */
    public readonly int $playcount,
    public readonly string $url,
    public readonly Artist $artist,
    /** @var list<ImageProps> */
    public readonly array $image = [],
    public readonly string $mbid = '',
  ) {}

}


class Attribute {

  public function __construct(
    public readonly string $artist = '',
    public readonly string $page = '',
    public readonly string $perPage = '',
    public readonly string $totalPages = '',
    public readonly string $total = '',
    public readonly string $for = '',
  ) {}

}


final class Artist {

  public function __construct(
    public readonly string $name = '',
    public readonly string $mbid = '',
    public readonly string $match = '',
    public readonly string $url = '',
    /** @var list<ImageProps> */
    public readonly array $image = [],
    /** @var string */
    public readonly string $streamable = '',
    public readonly int $listeners = 0,
  ) {}

}
