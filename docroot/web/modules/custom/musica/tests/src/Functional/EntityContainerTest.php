<?php

namespace Drupal\Tests\musica\Functional;

use Drupal\musica\State\EntityState;
use PHPUnit\Framework\TestCase;
use Drupal\musica\Controller\EntityContainer;
use Drupal\musica\Behavior\BaseBehaviors;
use Drupal\musica\Spec\LastFM\ArtistEnum;

/**
 * Stress test entity container functionality.
 *
 * @group musica
 */
class EntityContainerTest extends TestCase {

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
   * Basic state test for container.
   *
   * @todo move to unit test.
   */
  public function testInfo(EntityState $state): EntityState {
    $data['description'] = "<h1>{$state->name} is really cool.</h1>";
    return new EntityState($state->name, [...$state->data, ...$data]);
  }

}
