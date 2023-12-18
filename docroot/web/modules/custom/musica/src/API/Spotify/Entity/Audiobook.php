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
class Audiobook {

  /**
   * @var array the author(s) for the audiobook
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $authors = [];

  /**
   * @var string[] A list of the countries in which the audiobook can be played, identified by their \[ISO 3166-1 alpha-2\](http://en.wikipedia.org/wiki/ISO\_3166-1\_alpha-2) code.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $available_markets = [];

  /**
   * The chapters of the audiobook.
   */
  #[ORM\OneToOne(targetEntity: 'Drupal\musica\API\Spotify\Entity\Chapter')]
  #[ORM\JoinColumn(nullable: FALSE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Chapter $chapters;

  /**
   * @var array the copyright statements of the audiobook
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $copyrights = [];

  /**
   * A description of the audiobook. HTML tags are stripped away from this field, use `html\_description` field in case HTML tags are needed.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $description;

  /**
   * The edition of the audiobook.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $edition;

  /**
   * Whether or not the audiobook has explicit content (true = yes it does; false = no it does not OR unknown).
   */
  #[ORM\Column(type: 'boolean')]
  #[ApiProperty]
  #[Assert\NotNull]
  public bool $explicit;

  /**
   * External URLs for this audiobook.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_urls;

  /**
   * A link to the Web API endpoint providing full details of the audiobook.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $href;

  /**
   * A description of the audiobook. This field may contain HTML tags.
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
   * @var array the cover art for the audiobook in various sizes, widest first
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $images = [];

  /**
   * @var string[] A list of the languages used in the audiobook, identified by their \[ISO 639\](https://en.wikipedia.org/wiki/ISO\_639) code.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $languages = [];

  /**
   * The media type of the audiobook.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $media_type;

  /**
   * The name of the audiobook.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $name;

  /**
   * @var array the narrator(s) for the audiobook
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $narrators = [];

  /**
   * The publisher of the audiobook.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $publisher;

  /**
   * The number of chapters in this audiobook.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $total_chapters;

  /**
   * The object type.
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for the audiobook.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

}
