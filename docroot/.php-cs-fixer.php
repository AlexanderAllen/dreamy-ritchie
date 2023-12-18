<?php

/**
 * @file
 * Custom PHPCS Fixer config file for Drupal.
 *
 * @see https://cs.symfony.com/
 * @see https://github.com/drupol/phpcsfixer-configs-drupal
 * @see https://packagist.org/packages/drupol/phpcsfixer-configs-drupal
 */

use drupol\PhpCsFixerConfigsDrupal\Config\Drupal8;

$finder = PhpCsFixer\Finder::create()
  ->in(['web/modules/custom'])
  ->name('*.module')
  ->name('*.inc')
  ->name('*.install')
    ->name('*.test')
  ->name('*.profile')
  ->name('*.theme')
  ->notPath('*.md')
  ->notPath('*.info.yml')
;

$config = new Drupal8();
$config->setFinder($finder);

$rules = $config->getRules();

$config->setRules($rules);
return $config;
