<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit61e6df428cf03b0fbf45466d3905ec43
{
    public static $files = array (
        'e428fd54b1705cbff6b55264cbdf2944' => __DIR__ . '/../..' . '/includes/compatibility.php',
        '721d1736c937f143514d8272923c3222' => __DIR__ . '/../..' . '/includes/Core/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'ThemePlate\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ThemePlate\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
            1 => __DIR__ . '/../..' . '/includes/column',
            2 => __DIR__ . '/../..' . '/includes/page',
            3 => __DIR__ . '/../..' . '/includes/settings',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit61e6df428cf03b0fbf45466d3905ec43::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit61e6df428cf03b0fbf45466d3905ec43::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
