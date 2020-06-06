<?php

/**
 * Plugin Name: ThemePlate
 * Plugin URI:  https://github.com/kermage/ThemePlate
 * Author:      Gene Alyson Fortunado Torcende
 * Author URI:  mailto:genealyson.torcende@gmail.com
 * Description: A toolkit to handle everything related in developing a full-featured WordPress theme.
 * Version:     3.16.1
 * License:     GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package ThemePlate
 * @since 0.1.0
 */

// Accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==================================================
Global constants
================================================== */

if ( ! defined( 'TP_FILE' ) ) {
	define( 'TP_FILE', __FILE__ );
}

if ( ! defined( 'TP_PATH' ) ) {
	define( 'TP_PATH', plugin_dir_path( TP_FILE ) );
}

// Autoload classes with Composer
require_once TP_PATH . 'vendor/autoload.php';

// Instantiate the ThemePlate updater
EUM_Handler::run( TP_FILE, 'https://raw.githubusercontent.com/kermage/ThemePlate/master/update-data.json' );


add_filter( 'pre_update_option_active_plugins', array( ThemePlate::class, 'force_load_first' ) );


if ( ! function_exists( 'ThemePlate' ) ) {
	function ThemePlate( $key = null, $pages = null ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName

		if ( ! empty( $pages ) ) {
			_deprecated_argument( __FUNCTION__, '3.0.0', 'Use ThemePlate()->page( $args ) to create options pages instead.' );
		}

		return ThemePlate::instance( $key, $pages );

	}
}
