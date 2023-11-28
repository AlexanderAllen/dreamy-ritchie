<?php

// phpcs:disable

namespace Drupal\musica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\musica\Behavior\BehaviorInterface;
use Drupal\musica\Behavior\BaseBehaviors;
use Drupal\musica\Service\LastFM;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\Spec\LastFM\ArtistEnum;
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
  private BehaviorInterface $entity;
  private EntityState $state;

  /**
   * Private constructor.
   */
  private function __construct(BehaviorInterface $entity, EntityState $state) {
     $this->entity = $entity;
     $this->state = $state;
  }

  /**
   * Creates and returns a container with specified behavior and a empty state.
   *
   * Behaviors can still alter the state as they like.
   */
  public static function create(BehaviorInterface $entity): EntityContainer {
    return new self($entity, new EntityState());
  }

  /**
   * Creates and returns a container linked to the specified behavior and state.
   */
  public static function createFromState(BehaviorInterface $entity, EntityState $state): EntityContainer {
    return new self($entity, $state);
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
    if (method_exists($this->entity, $b)) {
      $new_state_ref = call_user_func([$this->entity, $b], $this->state, $s, $a);
    } else {
      $closure = $this->entity->getBehavior($b);
      $new_state_ref = $closure($this->state, $s, $a);
    }

    return self::createFromState($this->entity, $new_state_ref);
  }

  public function getBehaviorEntity() {
    return $this->entity;
  }
  public function getStateEntity() {
    return $this->state;
  }
}
