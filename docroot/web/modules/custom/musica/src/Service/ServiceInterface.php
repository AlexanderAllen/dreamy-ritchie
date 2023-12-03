<?php

namespace Drupal\musica\Service;

/**
 * Stub for custom Service interface.
 */
interface ServiceInterface {

  /**
   * Build and send a namespaced API Service request.
   *
   * @param string $namespace
   *   Namespace of the API request. For example: "artist", "track", etc.
   * @param string $call
   *   Name of the API call. For example: "getInfo".
   * @param array $request
   *   Arbitrary non-associative array of request parameters.
   */
  public function request(string $namespace, string $call, array $request);

}
