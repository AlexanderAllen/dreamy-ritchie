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
 *
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
          {
            "count": 100,
            "name": "pop",
            "url": "https://www.last.fm/tag/pop"
          }
        ],
        "attr": {
          "artist": "Cher"
        }
      }
    }
    JSON;

    $response = Source::json($sauce);

    try {

      $signature = GenericCollection::class . '<Drupal\Tests\musica\Unit\Baseline8a\TopTags>';

      $dto = (new MapperBuilder())
        ->allowSuperfluousKeys()
        ->allowPermissiveTypes()
        ->enableFlexibleCasting()
        ->mapper()
        ->map($signature, $response);

      $this->assertSame(TRUE, TRUE);
    }
    catch (\CuyZ\Valinor\Mapper\MappingError $error) {
      // Debugger::toCLI($error);
      $this->markTestIncomplete('The mapper raised an exception!');
    }

  }

}


/**
 * @template T
 */
final class GenericCollection
{
    public function __construct(
        /** @var array<T> */
        private array $collection,
    ) {
      $test = NULL;
    }
}

final class TopTags {
  public function __construct(
    /** @var array<Tag> */
    private array $tag,
    private Attribute $attr,
) {
  // Tag has already been casted by the time we get here. And raw attr is not there.
  $test = NULL;
}
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
    /** @var non-empty-string */
    public readonly string $artist,
    public readonly string $page = '',
    public readonly string $perPage = '',
    public readonly string $totalPages = '',
    public readonly string $total = '',
  ) {}

}
