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

  /**
   * Merges new data into the specified state silo.
   *
   * @param string $silo
   *   The name (array key) of a new or existing data silo in EntityState.
   * @param EntityState $currentState
   *   Existing state instance onto which the new data is going to be added.
   * @param array $newData
   *   New data to insert and merge into the state silo.
   *
   * @return EntityState
   *   New EntityState instance containing $newData.
   */
  public static function mergeStateSilo(string $silo, EntityState $currentState, array $newData): EntityState {
    $old_silo = array_key_exists($silo, $currentState->data) ? $currentState->data[$silo] : [];
    $new_state = self::create($currentState->name, $currentState, [
      $silo => [
        ...$old_silo,
        ...$newData,
      ],
    ]);
    return $new_state;
  }

}
