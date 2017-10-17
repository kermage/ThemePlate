# ThemePlate --
> *"A toolkit to handle everything related in developing a full-featured WordPress theme"*

It includes adding custom metaboxes to posts, terms, and users, registering custom post types and taxonomies, and creating theme settings/options page. Also comes with a markup cleaner and a clean navwalker.

## Features
- Work similarly to native WordPress function/methods
- Look seamlessly beautiful to WordPress pages/panels
- Easy, simple, and straightforward as much as possible

## Getting Started
#### 1. Install the ThemePlate toolkit
- As a theme required plugin: Refer [here](http://tgmpluginactivation.com/installation/)
- As a must-use plugin: Refer [here](https://codex.wordpress.org/Must_Use_Plugins)

#### 2. Add to theme's `functions.php` file
```php
if ( class_exists( 'ThemePlate' ) ) :
	ThemePlate( 'Theme Name' );
	require_once( 'post-types.php' );
	require_once( 'settings.php' );
	require_once( 'post-meta.php' );
	require_once( 'term-meta.php' );
	require_once( 'user-meta.php' );
endif;
```
- Initialize **ThemePlate** with *own theme key* which will be used as the theme options' page/menu title. Its *sanitized value* will be used as the unique options name and is also prefixed in every metabox fields.
- Require files containing the definition of custom post types, settings, and metaboxes.

#### 3. Define CPT, Settings, and Metaboxes
- `ThemePlate()->post_type( $args );`
- `ThemePlate()->taxonomy( $args );`
- `ThemePlate()->settings( $args );`
- `ThemePlate()->post_meta( $args );`
- `ThemePlate()->term_meta( $args );`
- `ThemePlate()->user_meta( $args );`

---
### Yeoman Generator
Check [generator-themeplate](https://www.npmjs.com/package/generator-themeplate) to kickstart a **ThemePlate** powered WP Theme project.
