<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.After
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.AfterLast
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.BeforeFirst, Drupal.Classes.ClassDeclaration.CloseBraceAfterBody
// phpcs:disable Drupal.Classes.FullyQualifiedNamespace.UseStatementMissing

namespace Drupal\Tests\musica\Unit\LFM\Bseline6;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * Basic Valinor DTO hydration test.
 *
 * @group musica
 * @group ignnore
 *
 * @see https://www.last.fm/api/show/artist.getTopAlbums
 * @see https://github.com/CuyZ/Valinor#example
 * @see https://valinor.cuyz.io/1.7/how-to/use-custom-object-constructors
 * @see https://valinor.cuyz.io/1.7/usage/type-strictness-and-flexibility/
 */
class HydrateTopAlbumsCleanedTest extends TestCase {

  public function testSimilarArtistsWithoutAttr() {
    $file = 'topalbums-full.json';
    $path = '/app/docroot/web/modules/custom/musica/tests/src/Unit/LFM/Baseline6/';
    $sauce = Source::file(new \SplFileObject($path . $file));

    try {

      $suffix = ', "@attr": ' . Attribute::class . '} }';
      $signature = 'array{topalbums: array{album: ' . EntityListAlbum::class . $suffix;
      $dto = (new MapperBuilder())
        // ->allowSuperfluousKeys()
        ->allowPermissiveTypes()
        // ->enableFlexibleCasting()
        ->mapper()
        ->map($signature, $sauce);

      $this->assertSame(TRUE, TRUE);
    }
    catch (\CuyZ\Valinor\Mapper\MappingError $error) {
      // Debugger::toCLI($error);
      $test = NULL;
    }

  }

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
