<?php

/**
 * Plugin Name: ThemePlate
 * Plugin URI:  https://github.com/kermage/ThemePlate
 * Author:      Gene Alyson Fortunado Torcende
 * Author URI:  mailto:genealyson.torcende@gmail.com
 * Description: A toolkit to handle everything related in developing a full-featured WordPress theme.
 * Version:     3.19.3
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

use ThemePlate\Legacy\Core\Helper\Main;

/* ==================================================
Global constants
================================================== */

if ( ! defined( 'TP_FILE' ) ) {
	define( 'TP_FILE', __FILE__ );
}

// Autoload classes with Composer
require_once plugin_dir_path( TP_FILE ) . 'vendor/autoload.php';

// Instantiate the ThemePlate updater
EUM_Handler::run( TP_FILE, 'https://raw.githubusercontent.com/kermage/ThemePlate/master/update-data.json' );


add_filter( 'pre_update_option_active_plugins', array( ThemePlate::class, 'force_load_first' ) );


if ( ! function_exists( 'ThemePlate' ) ) {
	function ThemePlate( $key = null, $pages = null ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName

		if ( ! empty( $key ) ) {
			if ( ! is_array( $key ) ) {
				_deprecated_argument( __FUNCTION__, '2.11.0', 'Initialize by passing <b>array( \'Options Title\', \'prefixed_key\' )</b>.' );
			} elseif ( Main::is_sequential( $key ) ) {
				_deprecated_argument( __FUNCTION__, '3.0.0', 'Initialize by passing <b>array( \'title\' => \'Options Title\', \'key\' => \'prefixed_key\' )</b>.' );
			}
		}

		if ( ! empty( $pages ) ) {
			_deprecated_argument( __FUNCTION__, '3.0.0', 'Use ThemePlate()->page( $args ) to create options pages instead.' );
		}

		return ThemePlate::instance( $key, $pages );

	}
}
