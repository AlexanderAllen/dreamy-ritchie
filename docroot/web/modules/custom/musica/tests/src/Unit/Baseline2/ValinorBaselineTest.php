<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen

namespace Drupal\Tests\musica\Unit\Baseline2;

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
    // $response = Source::json($json);
    $sauce = Source::file(new \SplFileObject('/app/docroot/web/modules/custom/musica/tests/src/Unit/Baseline2/similarartists.json'));

    try {
      $country = (new MapperBuilder())
        ->mapper()
        ->map(Similar::class, $sauce);

      $this->assertSame('France', 'France');

    }
    // @phpstan-ignore-next-line
    catch (MappingError $error) {
      $e = $error;
    }

  }

}


final class Similar {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var list<Artist> */
    public readonly array $artists,
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
