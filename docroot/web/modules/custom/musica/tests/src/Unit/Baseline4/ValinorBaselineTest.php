<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen

namespace Drupal\Tests\musica\Unit\Baseline4;

use Cuyz\Valinor\Mapper\MappingError;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * Basic Valinor DTO hydration test.
 *
 * @group musica
 * @group ignore
 *
 * @see https://github.com/CuyZ/Valinor#example
 * @see https://valinor.cuyz.io/1.7/how-to/use-custom-object-constructors
 * @see https://valinor.cuyz.io/1.7/usage/type-strictness-and-flexibility/
 *
 */
class ValinorBaselineTest extends TestCase {

  public function testSimilarArtistsWithoutAttr() {
    $file = 'similarartists-full.json';
    // $file = 'object.json';
    $sauce = Source::file(new \SplFileObject('/app/docroot/web/modules/custom/musica/tests/src/Unit/Baseline4/' . $file));

    try {

      $dto = (new MapperBuilder())
        ->allowPermissiveTypes()
        ->registerConstructor(
          function ($values): Simpleton {
            ['similarartists' => $similarartists] = $values;
            $name = $similarartists['@attr']['artist'];
            return new Simpleton($name, new Attribute($name));
          }
        )
        ->mapper()
        // ->map('object{similarartists: object{artist: list<Artist>, "@attr": object{artist: string}}}', $sauce);
        // ->map('array{name: string}', $sauce);
        ->map(Simpleton::class, $sauce);



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
  ) {}
}

/**
 * @phpstan-type SimilarSauce object{'name': string, "artist": list<Artist>}
 */
final class Similar {

  public function __construct(
    /** @var SimilarSauce */
    public readonly mixed $similarartists,
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

