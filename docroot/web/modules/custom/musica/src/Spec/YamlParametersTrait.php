<?php

namespace Drupal\musica\Spec;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Trait for matching object names to yaml file keys.
 */
trait YamlParametersTrait {

  /**
   * Provides method parameters defined in yaml files.
   *
   * @return array
   *   Array of method parameters.
   */
  public function parameters(): array {

    try {
      // @todo use drupal filesystem services for relative file resolution.
      $parsed_spec = Yaml::parseFile('/app/file.yaml', Yaml::PARSE_CONSTANT);
    } catch (ParseException $exception) {
      printf('Unable to parse the YAML string: %s', $exception->getMessage());
    }
    return $parsed_spec[$this->name] ??= NULL;
  }

}
