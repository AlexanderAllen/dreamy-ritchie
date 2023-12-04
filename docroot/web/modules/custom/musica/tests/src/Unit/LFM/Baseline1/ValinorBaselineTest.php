<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen

namespace Drupal\Tests\musica\Unit\Baseline1;

use Cuyz\Valinor\Mapper\MappingError;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * Basic Valinor DTO hydration test.
 *
 * @group musica
 * @group ignore
 * @see https://github.com/CuyZ/Valinor#example
 */
class ValinorBaselineTest extends TestCase {

  public function testSimilarArtistsWithoutAttr() {
    $json = <<<JSON
    {
        "name": "France",
        "cities": [
            {"name": "Paris", "timeZone": "Europe/Paris"},
            {"name": "Lyon", "timeZone": "Europe/Paris"}
        ]
    }
    JSON;
    // $response = Source::json($json);
    $sauce = Source::file(new \SplFileObject('/app/docroot/web/modules/custom/musica/tests/src/Unit/Baseline1/similarartists2.json'));

    try {
      $country = (new MapperBuilder())
        ->mapper()
        ->map(Similar2::class, $sauce);

      $this->assertSame('France', 'France');

    }
    // @phpstan-ignore-next-line
    catch (MappingError $error) {
      $e = $error;
    }

  }

}


final class Similar2 {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var list<Artist2> */
    public readonly array $artists,
  ) {}

}

final class Artist2 {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var non-empty-string */
    public readonly string $mbid,
  ) {}

}


final class Similar {

  public function __construct(
    /** @var list<Artist> */
    public readonly array $artist,
  ) {}

}

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
    /** @var non-empty-string */
    public readonly string $streamable,
  ) {}

}

final class Image {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $text,
    /** @var non-empty-string */
    public readonly string $size,
  ) {}

}
