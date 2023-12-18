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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get()])]
class Album {

  /**
   * The type of the album.
   */
  #[ORM\Column]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $album_type;

  /**
   * @var Collection<Artist> The artists of the album. Each artist object includes a link in `href` to more detailed information about the artist.
   */
  #[ORM\ManyToMany(targetEntity: 'Drupal\musica\API\Spotify\Entity\Artist')]
  #[ORM\JoinTable(name: 'album_artist_artists')]
  #[ORM\InverseJoinColumn(nullable: FALSE, unique: TRUE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Collection $artists;

  /**
   * @var string[] The markets in which the album is available: \[ISO 3166-1 alpha-2 country codes\](http://en.wikipedia.org/wiki/ISO\_3166-1\_alpha-2). \_\*\*NOTE\*\*: an album is considered available in a market when at least 1 of its tracks is available in that market.\_
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $available_markets = [];

  /**
   * @var array the copyright statements of the album
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $copyrights = [];

  /**
   * Known external IDs for the album.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_ids;

  /**
   * Known external URLs for this album.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_urls;

  /**
   * @var string[] A list of the genres the album is associated with. If not yet classified, the array is empty.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $genres = [];

  /**
   * A link to the Web API endpoint providing full details of the album.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $href;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'AUTO')]
  #[ORM\Column(type: 'integer')]
  public ?int $id = NULL;

  /**
   * @var array the cover art for the album in various sizes, widest first
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $images = [];

  /**
   * The label associated with the album.
   */
  #[ORM\Column(type: 'text', name: '`label`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $label;

  /**
   * The name of the album. In case of an album takedown, the value may be an empty string.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $name;

  /**
   * The popularity of the album. The value will be between 0 and 100, with 100 being the most popular.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $popularity;

  /**
   * The date the album was first released.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $release_date;

  /**
   * The precision with which `release\_date` value is known.
   */
  #[ORM\Column]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $release_date_precision;

  /**
   * Included in the response when a content restriction is applied.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $restrictions;

  /**
   * The number of tracks in the album.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $total_tracks;

  /**
   * The tracks of the album.
   */
  #[ORM\OneToOne(targetEntity: 'Drupal\musica\API\Spotify\Entity\Track')]
  #[ORM\JoinColumn(nullable: FALSE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Track $tracks;

  /**
   * The object type.
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the album.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

  public function __construct() {
    $this->artists = new ArrayCollection();
  }

}
