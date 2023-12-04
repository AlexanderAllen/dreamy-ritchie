<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\musica\Behavior\BaseBehaviors;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\State\EntityState;

/**
 * Fluid design pattern container.
 *
 * This container provides an interface for chaining calls provided by
 * behavioral entities implementing the BehaviorInterface interface.
 *
 * The state is stored inside a single immutable EntityState instance,
 * which this container stores a reference to, along with the current
 * behavioral instance.
 *
 * You can directly invoke the container at any time to get a dereferenced copy
 * of the container's current behavior and state.
 */
class EntityContainer {

  /**
   * Private constructor.
   */
  private function __construct(
    private BaseBehaviors $behavior,
    private EntityState $state,
  ) {}

  /**
   * Private behavior getter.
   */
  public function getBehaviorEntity() {
    return $this->behavior;
  }

  /**
   * Private State getter.
   */
  public function getStateEntity() {
    return $this->state;
  }

  public function dumpState() {
    devel_render($this->state->data);
  }

  /**
   * Creates and returns a container with specified behavior and a empty state.
   *
   * Behaviors can still alter the state as they like.
   */
  public static function create(BaseBehaviors $behavior): EntityContainer {
    return new self($behavior, new EntityState());
  }

  /**
   * Creates and returns a container linked to the specified behavior and state.
   */
  public static function createFromState(BaseBehaviors $behavior, EntityState $state): EntityContainer {
    return new self($behavior, $state);
  }

  /**
   * Executes a behavior on EntityState data, returning a container instance.
   *
   * This is a safe method. If the call does not exist on the callee an
   * instance of the container is returned so business can resume as usual.
   *
   * @param string $b
   *   Method (behavior) to execute.
   * @param ServiceInterface $s
   *   Optional. Service entity instance.
   * @param array $a
   *   Optional. Array of optional parameters
   *
   * @return EntityContainer
   *   Always returns an instance of EntityContainer.
   */
  public function map(string $b, ServiceInterface $s = NULL, array $a = []): EntityContainer {
    $new_state_ref = $this->state;

    // For built-in class methods.
    if (method_exists($this->behavior, $b)) {
      $new_state_ref = call_user_func([$this->behavior, $b], $this->state, $s, $a);
    } else {
      $closure = $this->behavior->getBehavior($b);
      $new_state_ref = $closure($this->state, $s, $a);
    }

    return self::createFromState($this->behavior, $new_state_ref);
  }

  /**
   * Transmutes the internal State entity data into Data Transfer Objects.
   *
   * @see Drupal\musica\Behavior\BaseBehaviors::hydrateState()
   *
   * @return EntityContainer
   */
  public function hydrate() {
    $behaviors = $this->behavior->enumBehaviors::cases();
    /** @var array<\BackedEnum> */
    $populated = array_filter($behaviors, fn ($behavior) => array_key_exists($behavior->name, $this->state->data));

    array_walk(
      $populated,
      fn ($_behavior) => $this->state = $this->behavior::hydrateState($this->state, $_behavior->name)
    );

    return self::createFromState($this->behavior, $this->state);
  }

}
