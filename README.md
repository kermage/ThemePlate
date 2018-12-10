# ThemePlate -- ![Scrutinizer Build Status](https://scrutinizer-ci.com/g/kermage/ThemePlate/badges/build.png) ![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kermage/ThemePlate/badges/quality-score.png)
> *"A toolkit to handle everything related in developing a full-featured WordPress theme"*

- Add custom meta boxes to posts, terms, and users
- Register custom post types and taxonomies
- Create options pages and add custom columns
- With a markup cleaner and a clean navwalker

## Features
- Fully compatible with the new block editor: Gutenberg
- Work similarly to native WordPress function/methods
- Look seamlessly beautiful to WordPress pages/panels
- Easy, simple, and straightforward as much as possible

## Getting Started
#### 1. Install the toolkit
- As a theme required plugin: Refer [here](http://tgmpluginactivation.com/installation/)
- As a must-use plugin: Refer [here](https://codex.wordpress.org/Must_Use_Plugins)

#### 2. Add to theme's `functions.php` file
```php
if ( class_exists( 'ThemePlate' ) ) :
	ThemePlate( array(
		'title' => 'Theme Name',
		'key' => 'theme_prefix'
	) );
	require_once( 'post-types.php' );
	require_once( 'settings.php' );
	require_once( 'meta-boxes.php' );
endif;
```
- Initialize with an array consisting of a ***title*** and a ***key*** to be used as:
	- page and menu title in the pre-created theme options page
	- prefix to the registered option names and in every meta key
- Require files containing the definition of ***`ThemePlate-d`*** items

#### 3. Define items to be *`ThemePlate-d`*
- `ThemePlate()->post_type( $args );`
- `ThemePlate()->taxonomy( $args );`
- `ThemePlate()->settings( $args );`
- `ThemePlate()->post_meta( $args );`
- `ThemePlate()->term_meta( $args );`
- `ThemePlate()->user_meta( $args );`
- `ThemePlate()->page( $args );`
- `ThemePlate()->column( $args );`

---
### Yeoman Generator
Check [generator-themeplate](https://www.npmjs.com/package/generator-themeplate) to kickstart a **ThemePlate** powered WP Theme project.
