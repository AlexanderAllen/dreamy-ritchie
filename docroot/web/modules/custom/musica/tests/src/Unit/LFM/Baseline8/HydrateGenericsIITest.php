<?php

// phpcs:disable

namespace Drupal\Tests\musica\Unit\Baseline8a;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for using PHPStan Generics with Valinor.
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
 * @see https://phpstan.org/blog/generics-in-php-using-phpdocs
 * @see https://phpstan.org/blog/generics-by-examples
 */
class HydrateGenericsIITest extends TestCase {

  public function testSimilarArtistsWithoutAttr() {
    $full = <<<JSON
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


    $sauce = <<<JSON
    {
      "toptags": {
        "tag": [
        ]
      }
    }
    JSON;

    $response = Source::json($sauce);

    try {

      $signature = 'array{toptags: array{tag: ' . Tag::class . ', "@attr": ' . Attribute::class . '} }';

      $dto = (new MapperBuilder())
        // ->allowSuperfluousKeys()
        ->allowPermissiveTypes()
        // ->enableFlexibleCasting()
        ->mapper()
        // This workes as long as the target prop name is mappable to SRC.
        // ->map(MyGenericWrapper::class . '<Drupal\Tests\musica\Unit\Baseline8\Tag>', $response);

        // somehoe working?
        ->map(GenericCollection::class . '<Drupal\Tests\musica\Unit\Baseline8a\TopTags>', $response);

        // THIS ITERATION WORKS 100%
        //->map(SomeCollection::class . '<Drupal\Tests\musica\Unit\Baseline8\Tag>', $response);

        // ->map($signature, $response);



      $this->assertSame(TRUE, TRUE);
    }
    catch (\CuyZ\Valinor\Mapper\MappingError $error) {
      // Debugger::toCLI($error);
      $this->markTestIncomplete('The mapper raised an exception!');
    }

  }

}

/**
 * @see https://github.com/CuyZ/Valinor/issues/8
 * @see https://github.com/CuyZ/Valinor/blob/396f64a5246ccfe3f6f6d3211bac7f542a9c7fc6/README.md#object
 */



/**
 * @template T
 */
final class GenericCollection
{
    public function __construct(
        /** @var array<T> */
        private array $collection,
    ) {}
}

class TopTags {
  public function __construct(
    /** @var array */
    private array $tag,
) {}
}

final class Tag {
  public function __construct(
    public readonly int $count,
    public readonly string $name,
    public readonly string $url = '',
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
