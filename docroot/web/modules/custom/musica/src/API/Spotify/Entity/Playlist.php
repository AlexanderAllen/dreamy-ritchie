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
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get(), new Put()])]
class Playlist {

  /**
   * `true` if the owner allows other users to modify the playlist.
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $collaborative;

  /**
   * The playlist description. \_Only returned for modified, verified playlists, otherwise\_ `null`.
   */
  #[ORM\Column(type: 'text', nullable: TRUE)]
  #[ApiProperty]
  public ?string $description = NULL;

  /**
   * Known external URLs for this playlist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_urls;

  /**
   * Information about the followers of the playlist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $followers;

  /**
   * A link to the Web API endpoint providing full details of the playlist.
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
   * @var array Images for the playlist. The array may be empty or contain up to three images. The images are returned by size in descending order. See \[Working with Playlists\](/documentation/web-api/concepts/playlists). \_\*\*Note\*\*: If returned, the source URL for the image (`url`) is temporary and will expire in less than a day.\_
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $images = [];

  /**
   * The name of the playlist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $name;

  /**
   * The user who owns the playlist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $owner;

  /**
   * The playlist's public/private status: `true` the playlist is public, `false` the playlist is private, `null` the playlist status is not relevant. For more about public/private status, see \[Working with Playlists\](/documentation/web-api/concepts/playlists).
   */
  #[ORM\Column(type: 'boolean', name: '`public`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $public;

  /**
   * The version identifier for the current playlist. Can be supplied in other requests to target a specific playlist version.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $snapshot_id;

  /**
   * The tracks of the playlist.
   */
  #[ORM\OneToOne(targetEntity: 'Drupal\musica\API\Spotify\Entity\Track')]
  #[ORM\JoinColumn(nullable: FALSE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Track $tracks;

  /**
   * The object type: "playlist".
   */
  #[ORM\Column(type: 'text', name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the playlist.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

}
