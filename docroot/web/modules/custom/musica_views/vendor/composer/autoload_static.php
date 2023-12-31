<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1ca9ab82f27428683af761d1fe422ae6
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Drupal\\MusicaViews\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Drupal\\MusicaViews\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1ca9ab82f27428683af761d1fe422ae6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1ca9ab82f27428683af761d1fe422ae6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1ca9ab82f27428683af761d1fe422ae6::$classMap;

        }, null, ClassLoader::class);
    }
}
