# ThemePlate Cleaner

> A markup cleaner

```php
add_action( 'after_setup_theme', array( 'ThemePlate\Cleaner', 'instance' ) );

add_theme_support( 'tpc_wp_head' );
add_theme_support( 'tpc_emoji_detection' );
add_theme_support( 'tpc_query_strings' );
add_theme_support( 'tpc_dependency_tag' );
add_theme_support( 'tpc_unnecessary_class' );
add_theme_support( 'tpc_extra_styles' );
add_theme_support( 'tpc_embed_wrap' );
add_theme_support( 'tpc_nav_walker' );
```
