<?php

declare(strict_types = 1);

/**
 * @file
 * Data Transfer Object for Spotify Web API.
 *
 * @see https://api-platform.com/docs/schema-generator/
 *
 * @phpcs:disable Drupal.Classes.FullyQualifiedNamespace.UseStatementMissing,Drupal.Commenting.DataTypeNamespace.DataTypeNamespace,Drupal.Commenting.DocComment.ShortSingleLine
 */

namespace Drupal\musica\API\Spotify\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get(), new GetCollection()])]
class Artist {

  /**
   * Known external URLs for this artist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $external_urls;

  /**
   * Information about the followers of the artist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $followers;

  /**
   * @var string[] A list of the genres the artist is associated with. If not yet classified, the array is empty.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $genres = [];

  /**
   * A link to the Web API endpoint providing full details of the artist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $href;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'AUTO')]
  #[ORM\Column(type: 'integer')]
  public ?string $id = NULL;

  /**
   * @var array images of the artist in various sizes, widest first
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $images = [];

  /**
   * The name of the artist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $name;

  /**
   * The popularity of the artist. The value will be between 0 and 100, with 100 being the most popular. The artist's popularity is calculated from the popularity of all the artist's tracks.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $popularity;

  /**
   * The object type.
   *
   * @TODO Using external Enum Throws LogicException:
   * "/app/docroot/vendor/cuyz/valinor/src/Type/Types/UnresolvableType.php"
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  #[Assert\Choice(callback: [ArtistType::class, 'toArray'])]
  public ArtistType $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the artist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

}

/**
 * Using MyCLabs\Enum\Enum throws unresolved type.
 * Native Enums are supported, though.
 * @see https://valinor.cuyz.io/1.7/usage/type-reference/#enums
 */
enum ArtistType: string {
  case ARTIST = 'artist';
}
