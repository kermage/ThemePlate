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
 * Description: A ThemePlate framework.
 * Version:     0.1.0
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tp
 */

// Accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* ==================================================
Global constants
================================================== */
define( 'TP_VERSION', '0.1.0' );
define( 'TP_FILE',    __FILE__ );
define( 'TP_URL',     plugin_dir_url( TP_FILE ) );
define( 'TP_PATH',    plugin_dir_path( TP_FILE ) );

// Load the ThemePlate plugin
require_once TP_PATH . 'class.' . basename( TP_FILE );
