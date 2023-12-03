<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.After
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.AfterLast
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.BeforeFirst, Drupal.Classes.ClassDeclaration.CloseBraceAfterBody

namespace Drupal\Tests\musica\Unit\Baseline6;

use Cuyz\Valinor\Mapper\MappingError;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * Basic Valinor DTO hydration test.
 *
 * @group musica
 *
 * @see https://github.com/CuyZ/Valinor#example
 * @see https://valinor.cuyz.io/1.7/how-to/use-custom-object-constructors
 * @see https://valinor.cuyz.io/1.7/usage/type-strictness-and-flexibility/
 */
class ValinorBaselineTest extends TestCase {

  public function testSimilarArtistsWithoutAttr() {
    $file = 'similarartists-full.json';
    $sauce = Source::file(new \SplFileObject('/app/docroot/web/modules/custom/musica/tests/src/Unit/Baseline6/' . $file));

    try {

      $dto = (new MapperBuilder())
        ->allowPermissiveTypes()
        // Ignore @attr.
        ->allowSuperfluousKeys()
        // ->registerConstructor(
        //   Parser::parseArray(...),
        // )
        ->mapper()
        // ->map('object{similarartists: object{artist: list<Artist>, "@attr": object{artist: string}}}', $sauce);
        // ->map(Simpleton::class, $sauce);
        ->map('array{similarartists: array{artist: ' . EntityList::class . '} }', $sauce);

      // $dto2 = (new MapperBuilder())
      //   ->mapper()
      //   ->map(EntityList::class, $dto->similarartists);

      $this->assertSame(TRUE, TRUE);
    }
    // @phpstan-ignore-next-line
    catch (MappingError $error) {
      $e = $error;
    }

  }

}

class Attribute {

  public function __construct(
    public readonly string $artist,
  ) {}

}

class Simpleton {

  public function __construct(
    public readonly string $name,
    public readonly Attribute $attr,
    /** @var list<Artist> */
    public readonly array $similarartists,
  ) {}

}

final class Parser {

  public static function parse($values): Simpleton {
    ['similarartists' => $similarartists] = $values;
    ['artist' => $artistList, '@attr' => $attributes] = $similarartists;

    $name = $similarartists['@attr']['artist'];
    return new Simpleton($name, new Attribute($attributes['artist']), $artistList);
  }

  public static function parseArray($values): array {
    ['similarartists' => $similarartists] = $values;
    ['artist' => $artistList, '@attr' => $attributes] = $similarartists;

    $name = $similarartists['@attr']['artist'];
    // 'list' => $artistList
    return ['name' => $name];
  }

  public static function parseEntity($values): array {
    ['similarartists' => $similarartists] = $values;
    ['artist' => $artistList, '@attr' => $attributes] = $similarartists;

    $name = $similarartists['@attr']['artist'];
    // 'list' => $artistList
    return ['name' => $name];
  }

}

final class EntityList {
  public function __construct(
    /** @var list<Artist> */
    public readonly array $list,
  ) {}
}

/**
 * @phpstan-type ImageProps array{"#text": string, size: string}
 *
 * @see https://phpstan.org/writing-php-code/phpdoc-types#local-type-aliases
 */
final class Artist {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var non-empty-string */
    public readonly string $mbid,
    /** @var non-empty-string */
    public readonly string $match,
    /** @var non-empty-string */
    public readonly string $url,
    /** @var list<ImageProps> */
    public readonly array $image,
    /** @var non-empty-string */
    public readonly string $streamable,
  ) {}

}
