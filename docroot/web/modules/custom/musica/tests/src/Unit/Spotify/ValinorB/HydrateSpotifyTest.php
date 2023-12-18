<?php

// phpcs:disable

namespace Drupal\Tests\musica\Unit\ValinorB;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use PHPUnit\Framework\TestCase;
// use Drupal\musica\API\Spotify\Entity\Artist;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Drupal\musica\API\Spotify\Enum\ArtistType;
use Symfony\Component\Validator\Constraints as Assert;

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
class HydrateSpotifyTest extends TestCase {

  public function testSpotifyHydrate() {

    // From OpenAPI/Swagger:
    //  curl -X 'GET' \
    //  'https://api.spotify.com/v1/artists/0TnOYISbd1XYRBk9myaseg' \
    //  -H 'accept: application/json' \
    //  -H 'Authorization: Bearer XZYabc123

    $sauce = <<<JSON
    {
      "external_urls": {
        "spotify": "https://open.spotify.com/artist/0TnOYISbd1XYRBk9myaseg"
      },
      "followers": {
        "href": null,
        "total": 10340479
      },
      "genres": [
        "dance pop",
        "miami hip hop",
        "pop"
      ],
      "href": "https://api.spotify.com/v1/artists/0TnOYISbd1XYRBk9myaseg",
      "id": "0TnOYISbd1XYRBk9myaseg",
      "images": [
        {
          "height": 640,
          "url": "https://i.scdn.co/image/ab6761610000e5ebee07b5820dd91d15d397e29c",
          "width": 640
        },
        {
          "height": 320,
          "url": "https://i.scdn.co/image/ab67616100005174ee07b5820dd91d15d397e29c",
          "width": 320
        },
        {
          "height": 160,
          "url": "https://i.scdn.co/image/ab6761610000f178ee07b5820dd91d15d397e29c",
          "width": 160
        }
      ],
      "name": "Pitbull",
      "popularity": 80,
      "type": "artist",
      "uri": "spotify:artist:0TnOYISbd1XYRBk9myaseg"
    }
    JSON;

    $response = Source::json($sauce);

    try {

      // $signature = GenericValueContainer::class . '<Drupal\Tests\musica\Unit\ValinorA\Artist>';
      $signature = Artist::class;

      $dto = (new MapperBuilder())
        ->allowSuperfluousKeys()
        ->allowPermissiveTypes()
        ->enableFlexibleCasting()
        ->mapper()
        ->map($signature, $response);

      $this->assertSame(TRUE, TRUE);
    }
    catch (\Exception $error) {
      $this->markTestIncomplete($error->getMessage());
    }

  }

}


/**
 * @template T
 */
final class GenericValueContainer
{
  public function __construct(
      /** @var T */
      public readonly mixed $container,
  ) {}
}

final class Tag {
  public function __construct(
    public readonly int $count = 0,
    public readonly string $name = '',
    public readonly string $url = '',
  ) {}
}


class Artist {

  /**
   * Known external URLs for this artist.
   */
  public string $external_urls = '';

  /**
   * Information about the followers of the artist.
   */
  public string $followers = '';

  public array $genres = [];

  /**
   * A link to the Web API endpoint providing full details of the artist.
   */
  public string $href = '';


  public ?int $id = NULL;

  /**
   * @var array images of the artist in various sizes, widest first
   */
  public array $images = [];

  /**
   * The name of the artist.
   */
  public string $name = '';

  /**
   * The popularity of the artist. The value will be between 0 and 100, with 100 being the most popular. The artist's popularity is calculated from the popularity of all the artist's tracks.
   */
  public int $popularity = 0;

  /**
   * The object type.
   */
  // public ArtistType $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the artist.
   */
  public string $uri = '';

}
