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
use Drupal\musica\API\Spotify\Enum\AudioFeaturType;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get()])]
class AudioFeatur {

  /**
   * A confidence measure from 0.0 to 1.0 of whether the track is acoustic. 1.0 represents high confidence the track is acoustic.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $acousticness;

  /**
   * A URL to access the full audio analysis of this track. An access token is required to access this data.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $analysis_url;

  /**
   * Danceability describes how suitable a track is for dancing based on a combination of musical elements including tempo, rhythm stability, beat strength, and overall regularity. A value of 0.0 is least danceable and 1.0 is most danceable.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $danceability;

  /**
   * The duration of the track in milliseconds.
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $duration_ms;

  /**
   * Energy is a measure from 0.0 to 1.0 and represents a perceptual measure of intensity and activity. Typically, energetic tracks feel fast, loud, and noisy. For example, death metal has high energy, while a Bach prelude scores low on the scale. Perceptual features contributing to this attribute include dynamic range, perceived loudness, timbre, onset rate, and general entropy.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $energy;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'AUTO')]
  #[ORM\Column(type: 'integer')]
  public ?int $id = NULL;

  /**
   * Predicts whether a track contains no vocals. "Ooh" and "aah" sounds are treated as instrumental in this context. Rap or spoken word tracks are clearly "vocal". The closer the instrumentalness value is to 1.0, the greater likelihood the track contains no vocal content. Values above 0.5 are intended to represent instrumental tracks, but confidence is higher as the value approaches 1.0.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $instrumentalness;

  /**
   * The key the track is in. Integers map to pitches using standard \[Pitch Class notation\](https://en.wikipedia.org/wiki/Pitch\_class). E.g. 0 = C, 1 = C♯/D♭, 2 = D, and so on. If no key was detected, the value is -1.
   */
  #[ORM\Column(type: 'integer', name: '`key`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $key;

  /**
   * Detects the presence of an audience in the recording. Higher liveness values represent an increased probability that the track was performed live. A value above 0.8 provides strong likelihood that the track is live.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $liveness;

  /**
   * The overall loudness of a track in decibels (dB). Loudness values are averaged across the entire track and are useful for comparing relative loudness of tracks. Loudness is the quality of a sound that is the primary psychological correlate of physical strength (amplitude). Values typically range between -60 and 0 db.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $loudness;

  /**
   * Mode indicates the modality (major or minor) of a track, the type of scale from which its melodic content is derived. Major is represented by 1 and minor is 0.
   */
  #[ORM\Column(type: 'integer', name: '`mode`')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $mode;

  /**
   * Speechiness detects the presence of spoken words in a track. The more exclusively speech-like the recording (e.g. talk show, audio book, poetry), the closer to 1.0 the attribute value. Values above 0.66 describe tracks that are probably made entirely of spoken words. Values between 0.33 and 0.66 describe tracks that may contain both music and speech, either in sections or layered, including such cases as rap music. Values below 0.33 most likely represent music and other non-speech-like tracks.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $speechiness;

  /**
   * The overall estimated tempo of a track in beats per minute (BPM). In musical terminology, tempo is the speed or pace of a given piece and derives directly from the average beat duration.
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $tempo;

  /**
   * An estimated time signature. The time signature (meter) is a notational convention to specify how many beats are in each bar (or measure). The time signature ranges from 3 to 7 indicating time signatures of "3/4", to "7/4".
   */
  #[ORM\Column(type: 'integer')]
  #[ApiProperty]
  #[Assert\NotNull]
  public int $time_signature;

  /**
   * A link to the Web API endpoint providing full details of the track.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $track_href;

  /**
   * The object type.
   */
  #[ORM\Column(name: '`type`')]
  #[ApiProperty]
  #[Assert\NotNull]
  #[Assert\Choice(callback: [AudioFeaturType::class, 'toArray'])]
  public AudioFeaturType $type;

  /**
   * The Spotify URI for the track.
   */
  #[ORM\Column(type: 'text')]
  #[ApiProperty]
  #[Assert\NotNull]
  public string $uri;

  /**
   * A measure from 0.0 to 1.0 describing the musical positiveness conveyed by a track. Tracks with high valence sound more positive (e.g. happy, cheerful, euphoric), while tracks with low valence sound more negative (e.g. sad, depressed, angry).
   */
  #[ORM\Column(type: 'float')]
  #[ApiProperty]
  #[Assert\NotNull]
  public float $valence;

}
