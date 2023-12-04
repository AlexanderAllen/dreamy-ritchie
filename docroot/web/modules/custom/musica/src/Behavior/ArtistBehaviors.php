<?php

// phpcs:disable Drupal.Commenting.DataTypeNamespace.DataTypeNamespace

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
   * Hyrdrates and returns a new EntityState.
   */
  public static function hydrateState(EntityState $state, string $dataKey): EntityState {
    $sauce = Source::json($state->data[$dataKey]);
    /** @var array */
    $dto = (new MapperBuilder())
      // ->allowSuperfluousKeys()
      ->allowPermissiveTypes()
      // ->enableFlexibleCasting()
      ->mapper()
      ->map(self::$shapes[$dataKey], $sauce);

    return self::mergeStateSilo('dto', $state, $dto);
  }

  /**
   * Merges new data into the specified state silo.
   *
   * @param string $silo
   *   The name (array key) of a new or existing data silo in EntityState.
   * @param EntityState $currentState
   *   Existing state instance onto which the new data is going to be added.
   * @param array $newData
   *   New data to insert and merge into the state silo.
   *
   * @return EntityState
   *   New EntityState instance containing $newData.
   *
   * @todo Would be nice to make this method a state trait.
   */
  public static function mergeStateSilo(string $silo, EntityState $currentState, array $newData): EntityState {
    $old_silo = is_null($currentState?->data[$silo]) ? [] : $currentState->data[$silo];
    $new_state = EntityState::create($currentState->name, $currentState, [
      $silo => [
        ...$old_silo,
        ...$newData,
      ],
    ]);
    return $new_state;
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
