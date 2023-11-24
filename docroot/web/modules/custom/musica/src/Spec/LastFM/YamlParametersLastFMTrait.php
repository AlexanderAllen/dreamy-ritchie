<?php

namespace Drupal\musica\Spec\LastFM;

/**
 * Trait for matching object names to yaml file keys.
 */
trait YamlParametersLastFMTrait {

  /**
   * Return the current API spec.
   */
  public function spec() {
    return 'LastFM';
  }

}
