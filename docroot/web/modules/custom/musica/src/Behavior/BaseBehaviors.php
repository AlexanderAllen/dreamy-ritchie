<?php

namespace Drupal\musica\Behavior;

use Drupal\musica\Behavior\BehaviorInterface;
use Drupal\musica\Controller\EntityState;
use Drupal\musica\Service\ServiceInterface;

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
   *
   * This method is just a unit test.
   */
  public function readOnlyViolation(EntityState $state): EntityState {
    $state->data['mic_check'] = "<p>{$state->name} has some pipes.</p>";
    return $state;
  }

  /**
   * {@inheritdoc}
   */
  public function getBehavior(string $b): callable {
    $prop_exists = array_key_exists($b, $this->behaviors);
    if ($prop_exists && is_callable($this->behaviors[$b])) {
      return $this->behaviors[$b];
    } else {
      return $this->dummyBehavior();
    }
  }

  /**
   * Populates the class behaviors property with behavioral closures.
   */
  protected function assignBehaviors(array $behaviors): void {
    array_walk($behaviors,
      fn ($behavior) => $this->behaviors[$behavior->name] = $this->createBehaviorHOF($behavior->name)
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

  // @todo additiona arbitrary call parameters need to be supported as needed
  protected function createBehaviorHOF(string $behavior_name): callable {
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
