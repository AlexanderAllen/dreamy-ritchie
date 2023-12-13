<?php

// phpcs:disable

namespace Drupal\Tests\musica\Unit\Baseline8II;

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
    $json = <<<JSON
    {
      "tag":
        {
          "count": 100,
          "name": "pop",
          "url": "https://www.last.fm/tag/pop"
        }
    }
    JSON;

    $response = Source::json($json);

    try {

      $suffix = ', "@attr": ' . Attribute::class . '} }';
      // $signature = 'array{topalbums: array{album: ' . EntityListAlbum::class . $suffix;

      /**
       * Notes, Usage, Description.
       *
       * For Valinor to work there needs to be a 1-to-1 source to target
       * variable name correlation. The props in the source map to the ones in
       * the class constructor.
       *
       * This introduces some amount of coupling in the generic wrapper.
       *
       * Update 5:19AM 12/4: FQ namespaces are found by the mapper.
       * This opens the door to the signature to be decoupled.
       *
       * @todo However the generic wrapper prop names are still coupled to
       * the source.
       */
      $dto = (new MapperBuilder())
        // ->allowSuperfluousKeys()
        ->allowPermissiveTypes()
        // ->enableFlexibleCasting()
        ->mapper()
        // This workes as long as the target prop name is mappable to SRC.
        // ->map(MyGenericWrapper::class . '<Drupal\Tests\musica\Unit\Baseline8\Tag>', $response);

        // somehoe working?
        // ->map(SomeCollection::class . '<array>', $response);

        // THIS ITERATION WORKS 100%
        //->map(SomeCollection::class . '<Drupal\Tests\musica\Unit\Baseline8\Tag>', $response);

        ->map(SomeCollection::class . '<Drupal\Tests\musica\Unit\Baseline8II\Tag>', $response);



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
 * @template T of object
 */
final class SomeCollection
{
    public function __construct(
        /** @var array<T> */
        private array $objects,
    ) {}
}

/**
 * @template T of object
 */
final class TopTags
{
    public function __construct(
        /** @var array<T> */
        private array $object,
    ) {}
}

/**
 * @template T of mixed
 * …or…
 * @template T // mixed is the default type
 *
 * You can also use advanced types:
 * @template T of int|float
 * @template T of SomeClass&SomeOtherClass
 * @template T of string|SomeClass
 * etc…
 */
class MyGenericWrapperOG
{
    /** @var T */
    public mixed $value;
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
