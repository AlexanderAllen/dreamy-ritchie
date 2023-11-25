<?php

namespace Drupal\musica\Spec;

use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\File\FileSystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Trait for matching object names to yaml file keys.
 */
trait YamlParametersTrait {

  /**
   * Static ExtensionList service.
   */
  public function extensionList(): ExtensionList {
    return \Drupal::service('extension.list.module');
  }

  /**
   * Static Filesystem service.
   */
  public function filesystem(): FileSystem {
    return \Drupal::service('file_system');
  }

  /**
   * Provides method parameters defined in yaml files.
   *
   * @return array
   *   Array of method parameters.
   */
  public function parameters(): array {
    $spec = $this->spec();
    $namespace = $this();

    $module_path = $this->extensionList()->getPath('musica');
    $real_path = $this->filesystem()->realpath($module_path);
    $spec_file = "{$real_path}/src/Spec/{$spec}/spec.yaml";

    try {
      $parsed_spec = Yaml::parseFile($spec_file, Yaml::PARSE_CONSTANT);
    } catch (ParseException $exception) {
      printf('Unable to parse the YAML string: %s', $exception->getMessage());
    }
    $method = "{$namespace}.{$this->name}";
    $spec = $parsed_spec[$method] ??= [];
    $spec = [...$spec, 'method' => $method];
    return $spec;
  }

  /**
   * Returns an array of request parameters for a given service and namespace.
   *
   * @param string $service
   *   Name of the service specification.
   * @param string $namespace
   *   Namespace of the API call.
   * @param string $call
   *   Name of the API call.
   *
   * @return array
   *   Array containing the request parameters.
   */
  public function serviceNsRequestParameters(string $service, string $namespace, string $call): array {

    $module_path = $this->extensionList()->getPath('musica');
    $real_path = $this->filesystem()->realpath($module_path);
    $spec_file = "{$real_path}/src/Spec/{$service}/spec.yaml";

    try {
      $parsed_spec = Yaml::parseFile($spec_file, Yaml::PARSE_CONSTANT);
    } catch (ParseException $exception) {
      printf('Unable to parse the YAML string: %s', $exception->getMessage());
    }
    $method = "{$namespace}.{$call}";
    $spec_parameters = $parsed_spec[$method] ??= [];
    $merged_spec = [...$spec_parameters, 'method' => $method];
    return $merged_spec;
  }

}
