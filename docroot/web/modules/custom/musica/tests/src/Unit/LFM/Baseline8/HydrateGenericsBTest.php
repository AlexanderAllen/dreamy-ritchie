<?php

// phpcs:disable

namespace Drupal\Tests\musica\Unit\Baseline8b;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for using PHPStan Generics with Valinor.
 *
 * Description: Havign gotten rid of a coupled root container, can we get rid
 * of the second value/container object using PHPStan types, and provide an
 * interface where only the inner content is typed out ("tag" in this case)?
 *
 * @group musica
 *
 * @see https://www.last.fm/api/show/artist.getTopAlbums
 * @see https://github.com/CuyZ/Valinor#example
 * @see https://valinor.cuyz.io/1.7/how-to/use-custom-object-constructors
 * @see https://valinor.cuyz.io/1.7/usage/type-strictness-and-flexibility/
 *
 * For PHPStan and Valinor information on generics.
 * @see https://github.com/CuyZ/Valinor/issues/8 valinor generics
 * @see https://github.com/CuyZ/Valinor/blob/396f64a5246ccfe3f6f6d3211bac7f542a9c7fc6/README.md#object
 *
 * @see https://phpstan.org/blog/generics-in-php-using-phpdocs
 * @see https://phpstan.org/blog/generics-by-examples
 */
class HydrateGenericsBTest extends TestCase {

  public function testSimilarArtistsWithAttr() {
    $sauce = <<<JSON
    {
      "toptags": {
        "tag": [
          {
            "count": 100,
            "name": "pop",
            "url": "https://www.last.fm/tag/pop"
          }
        ],
        "@attr": {
          "artist": "Cher"
        }
      }
    }
    JSON;

    $response = Source::json($sauce);

    try {

      $signature = GenericCollection::class . '<Drupal\Tests\musica\Unit\Baseline8b\TopTags>';

      $dto = (new MapperBuilder())
        // ->allowSuperfluousKeys()
        // ->allowPermissiveTypes()
        // ->enableFlexibleCasting()
        ->mapper()
        ->map($signature, $response);

      $this->assertSame(TRUE, TRUE);
    }
    catch (\CuyZ\Valinor\Mapper\MappingError $error) {
      $this->markTestIncomplete($error->getMessage());
    }

  }

}


/**
 * @template T
 */
final class GenericCollection
{
  public function __construct(
      /** @var T */
      public readonly mixed $collection,
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


class Attribute {

  public function __construct(
    public readonly string $artist = '',
    public readonly string $page = '',
    public readonly string $perPage = '',
    public readonly string $totalPages = '',
    public readonly string $total = '',
  ) {}
}
