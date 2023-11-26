<?php

namespace Drupal\musica\Service;

use stdClass;

/**
 * Stub for custom Service interface.
 */
interface ServiceInterface {

  /**
   * Build and send a namespaced service request for a namespaced call.
   *
   * @param string $namespace
   *   Namespace of the API request. For example: "artist", "track", etc.
   * @param string $call
   *   Name of the API call. For example: "getInfo".
   * @param array $request
   *   Arbitrary non-associative array of request parameters.
   */
  public function request(string $namespace, string $call, array $request): stdClass;

}
