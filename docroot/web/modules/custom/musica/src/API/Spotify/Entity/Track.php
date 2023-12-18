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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drupal\musica\API\Spotify\Enum\TrackType;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get(), new GetCollection()])]
class Track {

  /**
   * The album on which the track appears. The album object includes a link in `href` to full information about the album.
   */
  #[ORM\OneToOne(targetEntity: 'Drupal\musica\API\Spotify\Entity\Album')]
  #[ORM\JoinColumn(nullable: FALSE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Album $album;

  /**
   * @var Collection<Artist> The artists who performed the track. Each artist object includes a link in `href` to more detailed information about the artist.
   */
  #[ORM\ManyToMany(targetEntity: 'Drupal\musica\API\Spotify\Entity\Artist')]
  #[ORM\JoinTable(name: 'track_artist_artists')]
  #[ORM\InverseJoinColumn(nullable: FALSE, unique: TRUE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Collection $artists;

  /**
   * @var string[] A list of the countries in which the track can be played, identified by their \[ISO 3166-1 alpha-2\](http://en.wikipedia.org/wiki/ISO\_3166-1\_alpha-2) code.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $available_markets = [];

  /**
   * The disc number (usually `1` unless the album consists of more than one disc).
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $disc_number;

  /**
   * The track length in milliseconds.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $duration_ms;

  /**
   * Whether or not the track has explicit lyrics ( `true` = yes it does; `false` = no it does not OR unknown).
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $explicit;

  /**
   * Known external IDs for the track.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_ids;

  /**
   * Known external URLs for this track.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_urls;

  /**
   * A link to the Web API endpoint providing full details of the track.
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
   * Whether or not the track is from a local file.
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $is_local;

  /**
   * Part of the response when \[Track Relinking\](/documentation/web-api/concepts/track-relinking) is applied. If `true`, the track is playable in the given market. Otherwise `false`.
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $is_playable;

  /**
   * Part of the response when \[Track Relinking\](/documentation/web-api/concepts/track-relinking) is applied, and the requested track has been replaced with different track. The track in the `linked\_from` object contains information about the originally requested track.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $linked_from;

  /**
   * The name of the track.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $name;

  /**
   * The popularity of the track. The value will be between 0 and 100, with 100 being the most popular.
   *    The popularity of a track is a value between 0 and 100, with 100 being the most popular. The popularity is calculated by algorithm and is based, in the most part, on the total number of plays the track has had and how recent those plays are.
   *    Generally speaking, songs that are being played a lot now will have a higher popularity than songs that were played a lot in the past. Duplicate tracks (e.g. the same track from a single and an album) are rated independently. Artist and album popularity is derived mathematically from track popularity. \_\*\*Note\*\*: the popularity value may lag actual popularity by a few days: the value is not updated in real time.\_.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $popularity;

  /**
   * A link to a 30 second preview (MP3 format) of the track. Can be `null`.
   */
  #[ORM\Column(type: 'text', nullable: TRUE)]
  #[ApiProperty]
  public ?string $preview_url = NULL;

  /**
   * Included in the response when a content restriction is applied.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $restrictions;

  /**
   * The number of the track. If an album has several discs, the track number is the number on the specified disc.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $track_number;

  /**
   * The object type: "track".
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  #[Assert\Choice(callback: [TrackType::class, 'toArray'])]
  public TrackType $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the track.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

  public function __construct() {
    $this->artists = new ArrayCollection();
  }

}
