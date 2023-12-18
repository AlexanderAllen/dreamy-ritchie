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
#[ORM\Table(name: '`show`')]
#[ApiResource(operations: [new Get(), new GetCollection()])]
class Show {

  /**
   * @var string[] A list of the countries in which the show can be played, identified by their \[ISO 3166-1 alpha-2\](http://en.wikipedia.org/wiki/ISO\_3166-1\_alpha-2) code.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $available_markets = [];

  /**
   * @var array the copyright statements of the show
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $copyrights = [];

  /**
   * A description of the show. HTML tags are stripped away from this field, use `html\_description` field in case HTML tags are needed.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $description;

  /**
   * The episodes of the show.
   */
  #[ORM\OneToOne(targetEntity: 'Drupal\musica\API\Spotify\Entity\Episod')]
  #[ORM\JoinColumn(nullable: FALSE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Episod $episodes;

  /**
   * Whether or not the show has explicit content (true = yes it does; false = no it does not OR unknown).
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $explicit;

  /**
   * External URLs for this show.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_urls;

  /**
   * A link to the Web API endpoint providing full details of the show.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $href;

  /**
   * A description of the show. This field may contain HTML tags.
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
   * @var array the cover art for the show in various sizes, widest first
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $images = [];

  /**
   * True if all of the shows episodes are hosted outside of Spotify's CDN. This field might be `null` in some cases.
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $is_externally_hosted;

  /**
   * @var string[] A list of the languages used in the show, identified by their \[ISO 639\](https://en.wikipedia.org/wiki/ISO\_639) code.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $languages = [];

  /**
   * The media type of the show.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $media_type;

  /**
   * The name of the episode.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $name;

  /**
   * The publisher of the show.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $publisher;

  /**
   * The total number of episodes in the show.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $total_episodes;

  /**
   * The object type.
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the show.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

}
