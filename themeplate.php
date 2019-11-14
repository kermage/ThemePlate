<?php

/**
 * Plugin Name: ThemePlate
 * Plugin URI:  https://github.com/kermage/ThemePlate
 * Author:      Gene Alyson Fortunado Torcende
 * Author URI:  mailto:genealyson.torcende@gmail.com
 * Description: A toolkit to handle everything related in developing a full-featured WordPress theme.
 * Version:     3.9.1
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

if ( ! defined( 'TP_VERSION' ) ) {
	define( 'TP_VERSION', '3.9.1' );
}

if ( ! defined( 'TP_FILE' ) ) {
	define( 'TP_FILE', __FILE__ );
}

if ( ! defined( 'TP_URL' ) ) {
	define( 'TP_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'TP_PATH' ) ) {
	define( 'TP_PATH', plugin_dir_path( __FILE__ ) );
}

// Load the main ThemePlate class
require_once TP_PATH . 'class-' . basename( __FILE__ );

// Instantiate the ThemePlate updater
require_once TP_PATH . 'class-external-update-manager.php';
new External_Update_Manager( __FILE__, 'https://raw.githubusercontent.com/kermage/ThemePlate/wp-update/data.json' );
