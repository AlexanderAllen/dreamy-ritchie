<?php

namespace Drupal\musica\Behavior;

/**
 * Interface for behavioral entities.
 *
 * Behavioral entities CAN and SHOULD alter state, but they SHOULD NOT own,
 * store, or otherwise reference any state of any kind in any way.
 *
 * The behavior methods should always receive and return an instance of
 * EntityState back to the caller, or in other words, aim to behave more like
 * pure functions and less like OOP class methods.
 */
interface BehaviorInterface {

  /**
   * Returns the behavior callable if found, or a dummy callable otherwise.
   *
   * This method will always return a callable.
   */
  public function getBehavior(string $behavior): callable;

  /**
   * Provide information about behaviors available to this entity.
   */
  public static function behaviors();

}
