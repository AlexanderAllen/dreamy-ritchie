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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get()])]
class Episod {

  /**
   * A URL to a 30 second preview (MP3 format) of the episode. `null` if not available.
   */
  #[ORM\Column(type: 'text', nullable: TRUE)]
  #[ApiProperty]
  public ?string $audio_preview_url = NULL;

  /**
   * A description of the episode. HTML tags are stripped away from this field, use `html\_description` field in case HTML tags are needed.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $description;

  /**
   * The episode length in milliseconds.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $duration_ms;

  /**
   * Whether or not the episode has explicit content (true = yes it does; false = no it does not OR unknown).
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $explicit;

  /**
   * External URLs for this episode.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_urls;

  /**
   * A link to the Web API endpoint providing full details of the episode.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $href;

  /**
   * A description of the episode. This field may contain HTML tags.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $html_description;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'AUTO')]
  #[ORM\Column(type: 'integer')]
  public ?int $id = NULL;

  /**
   * @var array the cover art for the episode in various sizes, widest first
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $images = [];

  /**
   * True if the episode is hosted outside of Spotify's CDN.
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $is_externally_hosted;

  /**
   * True if the episode is playable in the given market. Otherwise false.
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $is_playable;

  /**
   * The language used in the episode, identified by a \[ISO 639\](https://en.wikipedia.org/wiki/ISO\_639) code. This field is deprecated and might be removed in the future. Please use the `languages` field instead.
   */
  #[ORM\Column(type: 'text', name: '`language`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $language;

  /**
   * @var string[] A list of the languages used in the episode, identified by their \[ISO 639-1\](https://en.wikipedia.org/wiki/ISO\_639) code.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $languages = [];

  /**
   * The name of the episode.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $name;

  /**
   * The date the episode was first released, for example `"1981-12-15"`. Depending on the precision, it might be shown as `"1981"` or `"1981-12"`.
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
   * The user's most recent position in the episode. Set if the supplied access token is a user token and has the scope 'user-read-playback-position'.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $resume_point;

  /**
   * The show on which the episode belongs.
   */
  #[ORM\OneToOne(targetEntity: 'Drupal\musica\API\Spotify\Entity\Show')]
  #[ORM\JoinColumn(nullable: FALSE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Show $show;

  /**
   * The object type.
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the episode.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

}
