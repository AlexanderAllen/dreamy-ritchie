<?php

namespace Drupal\musica\State;

/**
 * Immutable state entity.
 */
class EntityState {

  /**
   * Human-readable name of the state entity.
   *
   * Can be used by behavioral entities to make API calls.
   */
  public readonly string $name;

  /**
   * Private member containing the current state.
   *
   * @todo data as an arbitrary array is cool for prototyping, but...
   */
  public readonly array $data;

  /**
   * Class constructor.
   */
  public function __construct(string $name = '', array $state = []) {
    $this->name = $name;
    $this->data = $state;
  }

  /**
   * Merge and return an existing state instance with a new state instance.
   *
   * @param string $name
   *   The name for the new state instance.
   * @param EntityState $current_state
   *   The current state instance.
   * @param array $new_state
   *   Optional. Array of data to incorporate into the new state.
   *
   * @return EntityState
   *   A new state instance merged with the previous state.
   */
  public static function create(string $name, EntityState $current_state, array $new_state = []) {
    return new self($name, [...$current_state->data, ...$new_state]);
  }

}
