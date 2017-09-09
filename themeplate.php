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
 * Description: A framework to handle everything related in developing a full-featured WordPress theme.
 * Version:     0.1.0
 * License:     GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* ==================================================
Global constants
================================================== */
define( 'TP_VERSION', '0.1.0' );
define( 'TP_URL',     plugin_dir_url( __FILE__ ) );
define( 'TP_PATH',    plugin_dir_path( __FILE__ ) );

// Load the ThemePlate plugin
require_once( TP_PATH . 'includes/class-' . basename( __FILE__ ) );
