<?php

namespace Drupal\music_api\Controller;

/**
 * Enum doc.
 */
enum FooEnum: string
{
    case Foo = 'foo';
    case Bar = 'bar';
}

/**
 * Enum doc. PSR1 wants one file per file. yeah ok.
 */
enum BarEnum: string
{
    case Foo = 'foo';
    case Bar = 'bar';
}
