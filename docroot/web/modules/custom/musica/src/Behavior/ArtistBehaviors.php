<?php

// phpcs:disable Drupal.Commenting.DataTypeNamespace.DataTypeNamespace

namespace Drupal\musica\Behavior;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
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
 * @todo ONLY implementing GET methods ATM, and not any POST/AUTH methods.
 */
enum ArtistDTOMap: string {

  case addTags = 'addTags';
  case getCorrection = 'getCorrection';
  case getInfo = GenericCollection::class . '<Drupal\musica\Behavior\Info>';
  case getSimilar = GenericCollection::class . '<Drupal\musica\Behavior\SimilarArtists>';
  case getTags = 'getTags';
  case getTopAlbums = GenericCollection::class . '<Drupal\musica\Behavior\TopAlbums>';
  case getTopTags = GenericCollection::class . '<Drupal\musica\Behavior\TopTags>';
  case getTopTracks = GenericCollection::class . '<Drupal\musica\Behavior\TopTracks>';
  case removeTag = 'removeTag';
  case search = GenericCollection::class . '<Drupal\musica\Behavior\Search>';
}

/**
 * Data transfer object for artist.getSimilar.
 *
 * @phpstan-type RootValue array{'artist': list<Artist>, "@attr"?: Attribute}
 *
 * @see https://www.last.fm/api/show/artist.getInfo
 */
final class Info {

  public function __construct(
    public readonly Artist $artist,
  ) {}

}

/**
 * Data transfer object for artist.getSimilar.
 *
 * @phpstan-type RootValue array{'artist': list<Artist>, "@attr"?: Attribute}
 *
 * @see https://www.last.fm/api/show/artist.getSimilar
 */
final class SimilarArtists {

  public function __construct(
    /** @var RootValue $similarartists */
    public readonly array $similarartists,
  ) {}

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

/**
 * @phpstan-type taglist array{tag?: list<Tag>}
 * @phpstan-type getInfoSimilar array{artist?: list<Artist>}
 */
final class Artist {

  public function __construct(
    public readonly string $name = '',
    public readonly string $mbid = '',
    public readonly string $match = '',
    public readonly string $url = '',
    public readonly string $streamable = '',
    public readonly int $listeners = 0,
    public readonly int $ontour = 0,
    public readonly array $stats = [],
    /** @var taglist */
    public readonly array $tags = [],
    /** @var getInfoSimilar */
    public readonly array $similar = [],
    public readonly Images $image = new Images(),
    public readonly Bio $bio = new Bio(),
  ) {}

}

/**
 * @phpstan-type linkList array{link?: array{"#text"?: string, rel?: string, href?: string}}
 */
final class Bio {

  public function __construct(
    public readonly string $content = '',
    public readonly string $summary = '',
    public readonly string $published = '',
    /** @var linkList */
    public readonly array $links = [],
  ) {}

}

final class Images {
  public array $index = [];

  public function __construct(
    /** @var list<ImageProps> */
    public readonly array $images = [],
  ) {
    array_walk(
      $images,
      fn (ImageProps $image) => $this->index[empty($image->size) ? 'default' : $image->size] = $image->text
    );
  }

}
