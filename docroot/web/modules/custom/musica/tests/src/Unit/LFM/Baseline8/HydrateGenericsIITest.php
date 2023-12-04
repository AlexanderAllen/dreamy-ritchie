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
 * @group ignore
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
    $file = 'getTopTags-trimmed.json';
    $path = '/app/docroot/web/modules/custom/musica/tests/src/Unit/LFM/Baseline8/';
    $sauce = Source::file(new \SplFileObject($path . $file));

    $json = <<<JSON
    {
      "toptags": {
        "tag": [
          {
            "count": 100,
            "name": "pop",
            "url": "https://www.last.fm/tag/pop"
          }
        ],
      }
    }
    JSON;

    $response = Source::json($json);
    /**
     * @todo what about if we skim the root element name and replace it with
     * a generic name? That way we can utilize the same generic class for
     * everyone!? That wold require some JSON OPS.
     */

    try {

      $suffix = ', "@attr": ' . Attribute::class . '} }';
      $signature = 'array{topalbums: array{album: ' . EntityListAlbum::class . $suffix;

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
        // ->map(SomeCollection::class . '<Drupal\Tests\musica\Unit\Baseline8\Tag>', $response);

        ->map('array{topTags: array{' . SomeCollection::class . '<Drupal\Tests\musica\Unit\Baseline8\Tag>', $response);



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
 * @template T
 */
class MyGenericWrapper
{
    /** @var T */
    public $tag;
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


/**
 * @template T of Tag
 */
final class GenericEntityList {

  public function __construct(
    /** @var T */
    public $list,
  ) {
    $test = null;
  }
}


final class Tag {
  public function __construct(
    public readonly int $count,
    public readonly string $name,
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
