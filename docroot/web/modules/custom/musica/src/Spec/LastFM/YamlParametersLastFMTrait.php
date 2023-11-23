<?php

namespace Drupal\musica\Spec\LastFM;

use Drupal\musica\Spec\YamlParametersTrait;

/**
 * Trait for matching object names to yaml file keys.
 */
trait YamlParametersLastFMTrait {
  use YamlParametersTrait;

  /**
   * Provides method parameters defined in yaml files.
   *
   * @return array
   *   Array of method parameters.
   */
  public function parameters($namespace = ''): array {
    return $this->parametersBase('LastFM', $namespace);
  }

}
