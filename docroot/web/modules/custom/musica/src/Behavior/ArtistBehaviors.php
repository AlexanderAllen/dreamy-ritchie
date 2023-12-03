<?php

namespace Drupal\musica\Behavior;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Drupal\musica\DTO\LFM\Artist as SimilarArtist;
use Drupal\musica\DTO\LFM\Attribute as SimilarAttribute;
use Drupal\musica\DTO\LFM\EntityList;
use Drupal\musica\Spec\LastFM\ArtistEnum;
use Drupal\musica\State\EntityState;

/**
 * Behavioral class for Artist entity.
 */
class ArtistBehaviors extends BaseBehaviors {

  public function __construct() {
    parent::__construct('artist', ArtistEnum::class, ArtisDTOShapesEnum::class);
  }

  /**
   * Reduce raw API state into a data transfer object.
   */
  public static function hydrateState(EntityState $state, string $dataKey): array {
    $sauce = Source::json($state->data[$dataKey]);
    /** @var array */
    $ret = (new MapperBuilder())
      // ->allowSuperfluousKeys()
      ->allowPermissiveTypes()
      // ->enableFlexibleCasting()
      ->mapper()
      ->map(self::$shapes[$dataKey], $sauce);
    return $ret;
  }

}

/**
 * Provides DTO shapes for hydrating raw Artist data.
 */
enum ArtisDTOShapesEnum: string {

  case addTags = 'addTags';
  case getCorrection = 'getCorrection';
  case getInfo = 'getInfo';
  case getSimilar = 'array{similarartists: array{artist: ' . EntityList::class . ', "@attr": ' . Attribute::class . '} }';
  case getTags = 'getTags';
  case getTopAlbums = 'array{topalbums: array{album: ' . EntityListAlbum::class . ', "@attr": ' . Attribute::class . '} }';
  case getTopTags = 'getTopTags';
  case getTopTracks = 'getTopTracks';
  case removeTag = 'removeTag';
  case search = 'search';
}


class ImageProps {
  public readonly string $text;
  public readonly string $size;

  public function __construct(...$args) {
    [$this->text, $this->size] = $args;
  }

}


final class EntityListAlbum {

  public function __construct(
    /** @var list<Album> */
    public readonly array $album,
  ) {}

}

/**
 * @phpstan-type ImageProps3 array{"#text": string, size: string}
 */
final class Album {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var non-negative-int */
    public readonly int $playcount,
    /** @var string */
    public readonly string $url,
    /** @var Artist */
    public readonly Artist $artist,
    /** @var list<ImageProps> */
    public readonly array $image = [],
    /** @var string */
    public readonly string $mbid = '',
  ) {}

}


class Attribute {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $artist,
    public readonly string $page = '',
    public readonly string $perPage = '',
    public readonly string $totalPages = '',
    public readonly string $total = '',
  ) {}

}

/**
 * @see https://phpstan.org/writing-php-code/phpdoc-types#local-type-aliases
 */
final class Artist {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var non-empty-string */
    public readonly string $mbid,
    /** @var non-empty-string */
    public readonly string $url,
  ) {}

}
