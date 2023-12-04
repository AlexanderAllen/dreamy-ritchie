<?php

// phpcs:disable Drupal.Classes.UnusedUseStatement.UnusedUse

namespace Drupal\musica\Behavior;

use Drupal\musica\Behavior\BehaviorInterface;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\State\EntityState;

/**
 * Class stub.
 */
abstract class BaseBehaviors implements BehaviorInterface {

  /**
   * Behaviors available for this entity.
   */
  protected array $behaviors;

  /**
   * Holds signature / shapes for DTO hydration.
   */
  public static array $shapes;

  /**
   * Stateless behavior constructor.
   *
   * DO NOT pass any kind of state (data) to behavior constructors, otherwise
   * the behavioral entities get tied (coupled) to the stateful entities.
   *
   * @param non-empty-string $namespace
   *   The kind of behavioral object, used for API calls.
   * @param class-string<\BackedEnum> $enumBehaviors
   *   Reference to object enumerating the available API methods (behaviors).
   * @param class-string<\BackedEnum> $enumShapes
   *   Reference to object enumerating the shapes used for DTO hydration.
   */
  public function __construct(
    public readonly string $namespace,
    public readonly string $enumBehaviors,
    public readonly string $enumShapes,
  ) {
    $this->assignBehaviors($enumBehaviors::cases());
    $this->assignDTOShapes($enumShapes::cases());
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
   * Transfer DTO shapes from enumerator into internal array.
   *
   * @todo shapes and behaviors could be sourced from the same enum.
   */
  protected function assignDTOShapes(array $behaviors): void {
    array_walk(
      $behaviors,
      fn ($behavior) => self::$shapes[$behavior->name] = $behavior->value
    );
  }

  abstract public static function hydrateState(EntityState $state, string $dataKey): EntityState;

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
