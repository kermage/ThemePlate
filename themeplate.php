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

// Load the main ThemePlate class
require_once TP_PATH . 'class-' . basename( TP_FILE );

add_action( 'plugins_loaded', array( ThemePlate::class, 'force_load_first' ) );

if ( ! function_exists( 'ThemePlate' ) ) {
	function ThemePlate( $key = null, $pages = null ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName

		if ( ! empty( $pages ) ) {
			_deprecated_argument( __FUNCTION__, '3.0.0', 'Use ThemePlate()->page( $args ) to create options pages instead.' );
		}

		if ( ! empty( $key ) && ! is_array( $key ) ) {
			_deprecated_argument( __FUNCTION__, '2.11.0', 'Use the newer way to initialize by passing <b>array( \'Options Title\', \'prefixed_key\' )</b>.' );
		}

		return ThemePlate::instance( $key, $pages );

	}
}

// Instantiate the ThemePlate updater
require_once TP_PATH . 'class-external-update-manager.php';
EUM_Handler::run( TP_FILE, 'https://raw.githubusercontent.com/kermage/ThemePlate/master/update-data.json' );
