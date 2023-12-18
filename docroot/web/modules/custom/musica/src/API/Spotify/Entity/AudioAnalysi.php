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
class AudioAnalysi {

  /**
   * @var array The time intervals of the bars throughout the track. A bar (or measure) is a segment of time defined as a given number of beats.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $bars = [];

  /**
   * @var array The time intervals of beats throughout the track. A beat is the basic time unit of a piece of music; for example, each tick of a metronome. Beats are typically multiples of tatums.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $beats = [];

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'AUTO')]
  #[ORM\Column(type: 'integer')]
  public ?int $id = NULL;

  #[ApiProperty]
  #[Assert\NotNull]
  public string $meta;

  /**
   * @var array Sections are defined by large variations in rhythm or timbre, e.g. chorus, verse, bridge, guitar solo, etc. Each section contains its own descriptions of tempo, key, mode, time\_signature, and loudness.
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $sections = [];

  /**
   * @var array each segment contains a roughly conisistent sound throughout its duration
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $segments = [];

  /**
   * @var array a tatum represents the lowest regular pulse train that a listener intuitively infers from the timing of perceived musical events (segments)
   */
  #[ORM\Column(type: 'json')]
  #[ApiProperty]
  #[Assert\NotNull]
  public array $tatums = [];

  #[ORM\OneToOne(targetEntity: 'Drupal\musica\API\Spotify\Entity\Track')]
  #[ORM\JoinColumn(nullable: FALSE)]
  #[ApiProperty]
  #[Assert\NotNull]
  public Track $track;

}
