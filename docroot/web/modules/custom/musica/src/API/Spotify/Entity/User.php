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
use Drupal\musica\API\Spotify\Enum\UserType;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
#[ApiResource(operations: [new Get()])]
class User {

  /**
   * The name displayed on the user's profile. `null` if not available.
   */
  #[ORM\Column(type: 'text', nullable: TRUE)]
  #[ApiProperty]
  public ?string $display_name = NULL;

  /**
   * Known public external URLs for this user.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $external_urls;

  /**
   * Information about the followers of this user.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $followers;

  /**
   * A link to the Web API endpoint for this user.
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
   * @var array the user's profile image
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $images = [];

  /**
   * The object type.
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  #[Assert\Choice(callback: [UserType::class, 'toArray'])]
  public UserType $type;

  /**
   * The \[Spotify URI\](/documentation/web-api/concepts/spotify-uris-ids) for this user.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

}
