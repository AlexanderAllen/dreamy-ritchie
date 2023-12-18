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

namespace Drupal\musica\API\Spotify\Enum;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use MyCLabs\Enum\Enum;

#[ORM\Entity]
#[ApiResource]
class ArtistType extends Enum {

  /**
   * @var string
   */
  public const ARTIST = 'artist';

}
