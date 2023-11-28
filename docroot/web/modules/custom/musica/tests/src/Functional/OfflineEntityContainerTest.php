<?php

namespace Drupal\Tests\musica\Functional;

use Drupal\musica\State\EntityState;
use PHPUnit\Framework\TestCase;
use Drupal\musica\Controller\EntityContainer;
use Drupal\musica\Behavior\BaseBehaviors;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\Spec\LastFM\ArtistEnum;

/**
 * Stress test entity container functionality.
 *
 * @group musica
 */
class OfflineEntityContainerTest extends TestCase {

  public function testInvalidChainedBehaviors() {
    $container = EntityContainer::createFromState(new BasicBehavior(), new EntityState('Cher'))
      ->map('testInfo')
      ->map('doesntexist');

    $behavior = $container->getBehaviorEntity();
    $state = $container->getStateEntity();

    $this->assertSame('Cher', $state->name, 'State name is present');
    $this->assertSame('<h1>Cher is really cool.</h1>', $state->data['description'],
      'State entity data is not overriden by invalid behaviors');
  }

}

/**
 * Provides basic behaviors w/ minimal dependencies.
 */
class BasicBehavior extends BaseBehaviors {

  public function __construct() {
    $this->namespace = 'artist';
    $this->assignBehaviors(ArtistEnum::cases());
  }

  /**
   * Working example method - escape entity name.
   */
  public function htmlspecialchars(EntityState $state) {
    return new EntityState(htmlspecialchars($state->name));
  }

  /**
   * Working example method - lowercase entity name.
   */
  public function strtolower(EntityState $state) {
    return new EntityState(strtolower($state->name));
  }

  /**
   * Calling this method throws an uncaught readonly prop violation error.
   *
   * This violation is great because it prevents the implementation from
   * causing side effects by reference inside the state.
   */
  public function readOnlyViolation(EntityState $state): EntityState {
    $state->data['mic_check'] = "<p>{$state->name} has some pipes.</p>";
    return $state;
  }

  /**
   * Basic state test for container.
   */
  public function testInfo(EntityState $state): EntityState {
    $data['description'] = "<h1>{$state->name} is really cool.</h1>";
    return new EntityState($state->name, [...$state->data, ...$data]);
  }

  /**
   * Get the bio summary for an artist.
   *
   * Maybe there should be a service-specific facility from which
   * a service-agnostic behavior can grab data to in an abstracted manner.
   *
   * The behaviors would then populate a standardized state, indpendendent of
   * service.
   */
  public function getBio(EntityState $state, ServiceInterface $service): EntityState {
    $response = $service->request($this->namespace, 'getInfo', [
      'artist' =>  $state->name,
    ]);

    $new = EntityState::create($state->name, $state, [
      'info' => $response?->artist?->bio?->summary,
    ]);
    return $new;
  }

  /**
   * Implements live artist.getSimilar API call.
   *
   * Limits the live response to 10 results while still allowing full
   * integration testing to help avoid rate-limit impacts.
   *
   * @todo should be implementing throwables at the service for API errors.
   */
  public function getSimilarTest(EntityState $state, ServiceInterface $service): EntityState {
    $response = $service->request($this->namespace, 'getSimilar', [
      'artist' =>  $state->name,
      'limit' => 10,
    ]);
    $new = EntityState::create($state->name, $state, [
      'getSimilar' => $response,
    ]);
    return $new;
  }

}
