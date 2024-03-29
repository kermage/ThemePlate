<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1940a53e868c49be4ea15081feb352d4
{
    public static $files = array (
        'e2fb8214a7589690aae8ec82f7aa8973' => __DIR__ . '/..' . '/kermage/external-update-manager/class-external-update-manager.php',
        'c452be5f6a24c9525e0e66a9e6f91c39' => __DIR__ . '/../..' . '/class-themeplate.php',
        'e428fd54b1705cbff6b55264cbdf2944' => __DIR__ . '/../..' . '/includes/compatibility.php',
        '721d1736c937f143514d8272923c3222' => __DIR__ . '/../..' . '/includes/Core/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'ThemePlate\\Legacy\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ThemePlate\\Legacy\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'ThemePlate\\Legacy\\CPT\\Base' => __DIR__ . '/../..' . '/includes/CPT/Base.php',
        'ThemePlate\\Legacy\\CPT\\PostType' => __DIR__ . '/../..' . '/includes/CPT/PostType.php',
        'ThemePlate\\Legacy\\CPT\\Taxonomy' => __DIR__ . '/../..' . '/includes/CPT/Taxonomy.php',
        'ThemePlate\\Legacy\\Cleaner' => __DIR__ . '/../..' . '/includes/Cleaner.php',
        'ThemePlate\\Legacy\\Column' => __DIR__ . '/../..' . '/includes/Column/Column.php',
        'ThemePlate\\Legacy\\Core\\Data' => __DIR__ . '/../..' . '/includes/Core/Data.php',
        'ThemePlate\\Legacy\\Core\\Field\\Checkbox' => __DIR__ . '/../..' . '/includes/Core/Field/Checkbox.php',
        'ThemePlate\\Legacy\\Core\\Field\\Color' => __DIR__ . '/../..' . '/includes/Core/Field/Color.php',
        'ThemePlate\\Legacy\\Core\\Field\\Date' => __DIR__ . '/../..' . '/includes/Core/Field/Date.php',
        'ThemePlate\\Legacy\\Core\\Field\\Editor' => __DIR__ . '/../..' . '/includes/Core/Field/Editor.php',
        'ThemePlate\\Legacy\\Core\\Field\\File' => __DIR__ . '/../..' . '/includes/Core/Field/File.php',
        'ThemePlate\\Legacy\\Core\\Field\\Html' => __DIR__ . '/../..' . '/includes/Core/Field/Html.php',
        'ThemePlate\\Legacy\\Core\\Field\\Input' => __DIR__ . '/../..' . '/includes/Core/Field/Input.php',
        'ThemePlate\\Legacy\\Core\\Field\\Link' => __DIR__ . '/../..' . '/includes/Core/Field/Link.php',
        'ThemePlate\\Legacy\\Core\\Field\\Number' => __DIR__ . '/../..' . '/includes/Core/Field/Number.php',
        'ThemePlate\\Legacy\\Core\\Field\\Radio' => __DIR__ . '/../..' . '/includes/Core/Field/Radio.php',
        'ThemePlate\\Legacy\\Core\\Field\\Select' => __DIR__ . '/../..' . '/includes/Core/Field/Select.php',
        'ThemePlate\\Legacy\\Core\\Field\\Textarea' => __DIR__ . '/../..' . '/includes/Core/Field/Textarea.php',
        'ThemePlate\\Legacy\\Core\\Field\\Type' => __DIR__ . '/../..' . '/includes/Core/Field/Type.php',
        'ThemePlate\\Legacy\\Core\\Fields' => __DIR__ . '/../..' . '/includes/Core/Fields.php',
        'ThemePlate\\Legacy\\Core\\Form' => __DIR__ . '/../..' . '/includes/Core/Form.php',
        'ThemePlate\\Legacy\\Core\\Helper\\Box' => __DIR__ . '/../..' . '/includes/Core/Helper/Box.php',
        'ThemePlate\\Legacy\\Core\\Helper\\Field' => __DIR__ . '/../..' . '/includes/Core/Helper/Field.php',
        'ThemePlate\\Legacy\\Core\\Helper\\Main' => __DIR__ . '/../..' . '/includes/Core/Helper/Main.php',
        'ThemePlate\\Legacy\\Core\\Helper\\Meta' => __DIR__ . '/../..' . '/includes/Core/Helper/Meta.php',
        'ThemePlate\\Legacy\\Helpers' => __DIR__ . '/../..' . '/includes/Helpers.php',
        'ThemePlate\\Legacy\\Meta\\Base' => __DIR__ . '/../..' . '/includes/Meta/Base.php',
        'ThemePlate\\Legacy\\Meta\\Menu' => __DIR__ . '/../..' . '/includes/Meta/Menu.php',
        'ThemePlate\\Legacy\\Meta\\Post' => __DIR__ . '/../..' . '/includes/Meta/Post.php',
        'ThemePlate\\Legacy\\Meta\\Term' => __DIR__ . '/../..' . '/includes/Meta/Term.php',
        'ThemePlate\\Legacy\\Meta\\User' => __DIR__ . '/../..' . '/includes/Meta/User.php',
        'ThemePlate\\Legacy\\NavWalker' => __DIR__ . '/../..' . '/includes/NavWalker/NavWalker.php',
        'ThemePlate\\Legacy\\Page' => __DIR__ . '/../..' . '/includes/Page/Page.php',
        'ThemePlate\\Legacy\\Settings' => __DIR__ . '/../..' . '/includes/Settings/Settings.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1940a53e868c49be4ea15081feb352d4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1940a53e868c49be4ea15081feb352d4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1940a53e868c49be4ea15081feb352d4::$classMap;

        }, null, ClassLoader::class);
    }
}
