<?php

namespace Drupal\musica\Behavior;

use Drupal\musica\Behavior\BehaviorInterface;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\State\EntityState;

/**
 * Class stub.
 */
abstract class BaseBehaviors implements BehaviorInterface {

  /**
   * The kind of behavioral object, used for API calls.
   */
  protected string $namespace;

  /**
   * Behaviors available for this entity.
   */
  protected array $behaviors;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public static function behaviors() {
  }

  /**
   * {@inheritdoc}
   */
  public function getBehavior(string $b): callable {
    $prop_exists = array_key_exists($b, $this->behaviors);
    if ($prop_exists && is_callable($this->behaviors[$b])) {
      return $this->behaviors[$b];
    }
    else {
      return $this->dummyBehavior();
    }
  }

  /**
   * Populates the class behaviors property with behavioral closures.
   */
  protected function assignBehaviors(array $behaviors): void {
    array_walk(
      $behaviors,
      fn ($behavior) => $this->behaviors[$behavior->name] = $this->createBehaviorHof($behavior->name)
    );
  }

  /**
   * Dummy behavior that goes nowhere and does mostly nothing.
   *
   * Use for testing and to preserve the functional purity of the state.
   * In case a non-existant behavior is requested, the dummy callable can be
   * subbed in without state violations or side effects.
   */
  protected function dummyBehavior(EntityState $state = NULL, ServiceInterface $service = NULL, array $params = []): callable {
    return fn (EntityState $state, ServiceInterface $service = NULL, array $params = []) => (
      EntityState::create($state->name, $state, [])
    );
  }

  /**
   * Higher Order Function that returns behavioral closures.
   */
  protected function createBehaviorHof(string $behavior_name): callable {
    return fn (EntityState $state, ServiceInterface $service, array $params = []) => (
      EntityState::create($state->name, $state, [
        $behavior_name => $service->request(
          $this->namespace,
          $behavior_name,
          [$this->namespace => $state->name, ...$params]
        ),
      ])
    );
  }

}
