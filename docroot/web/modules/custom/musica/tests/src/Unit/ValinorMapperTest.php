<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen

namespace Drupal\Tests\musica\Unit;

use Cuyz\Valinor\Mapper\MappingError;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * Basic Valinor DTO hydration test.
 *
 * @group musica
 * @see https://github.com/CuyZ/Valinor#example
 */
class ValinorMapperTest extends TestCase {

  public function testBasicDataTransferObject() {
    $json = <<<JSON
    {
        "name": "France",
        "cities": [
            {"name": "Paris", "timeZone": "Europe/Paris"},
            {"name": "Lyon", "timeZone": "Europe/Paris"}
        ]
    }
    JSON;
    $response = Source::json($json);

    try {
      $country = (new MapperBuilder())
        ->mapper()
        ->map(Country::class, $response);

      $this->assertSame('France', $country->name);
      $this->assertSame('Paris', $country->cities[0]->name);

    }
    catch (\Throwable $error) {
      // Handle the error…
    }

  }

}

final class Country {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var list<City> */
    public readonly array $cities,
  ) {}

}

final class City {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    public readonly \DateTimeZone $timeZone,
  ) {}

}
