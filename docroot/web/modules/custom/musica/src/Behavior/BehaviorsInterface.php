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
interface BehaviorsInterface {

  /**
   * Stateless behavior constructor.
   *
   * DO NOT pass any kind of state to behavior constructors.
   * Otherwise the state entities becaome coupled to the behavioral entities.
   */
  public function __construct();

  /**
   * Provide information about behaviors available to this entity.
   *
   * @return array
   *   List of behaviors available.
   */
  public static function behaviors();

}
