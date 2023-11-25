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
   * Stub.
   */
  public function parametersClass(): array {
    $spec = $this->spec();
    $namespace = $this->namespace;

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

}
