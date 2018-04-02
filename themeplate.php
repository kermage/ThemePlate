<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

/**
 * Plugin Name: ThemePlate
 * Plugin URI:  https://github.com/kermage/ThemePlate
 * Author:      Gene Alyson Fortunado Torcende
 * Author URI:  mailto:genealyson.torcende@gmail.com
 * Description: A toolkit to handle everything related in developing a full-featured WordPress theme.
 * Version:     2.10.4
 * License:     GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==================================================
Global constants
================================================== */

if ( ! defined( 'TP_VERSION' ) ) {
	define( 'TP_VERSION', '2.10.4' );
}

if ( ! defined( 'TP_URL' ) ) {
	define( 'TP_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'TP_PATH' ) ) {
	define( 'TP_PATH', plugin_dir_path( __FILE__ ) );
}

// Load the ThemePlate plugin
require_once TP_PATH . 'includes/class-' . basename( __FILE__ );

function ThemePlate( $key = null, $pages = null ) {
	return ThemePlate::instance( $key, $pages );
}

// Instantiate the ThemePlate updater
require_once TP_PATH . 'includes/class-external-update-manager.php';
new External_Update_Manager( __FILE__, 'https://raw.githubusercontent.com/kermage/ThemePlate/wp-update/data.json' );
