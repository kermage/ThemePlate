<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate {

	private static $instance;


	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	private function __construct() {

		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class-themeplate-cpt.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class-themeplate-fields.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class-themeplate-postmeta.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class-themeplate-settings.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class-themeplate-termmeta.php' );

		ThemePlate_CPT::instance();
		ThemePlate_Fields::instance();
		ThemePlate_PostMeta::instance();
		ThemePlate_Settings::instance();
		ThemePlate_TermMeta::instance();

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );
		add_action( 'save_post', array( ThemePlate_PostMeta::instance(), 'save' ) );

	}
	

	public function menu() {

		add_menu_page(
			// Page Title
			'Theme Options',
			// Menu Title
			'Theme Options',
			// Capability
			'edit_theme_options',
			// Menu Slug
			'theme-options',
			// Content Function
			array( ThemePlate_Settings::instance(), 'page' )
		);

	}
	

	public function init() {

		register_setting( 'themeplate', 'themeplate' );

	}


	public function scripts_styles() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_style( 'themeplate-style', TP_URL . 'themeplate.css', array(), TP_VERSION, false );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'themeplate.js', array(), TP_VERSION, true );

	}

}


function ThemePlate() {

	return ThemePlate::instance();

}

ThemePlate();
