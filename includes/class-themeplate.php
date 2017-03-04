<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate {

	private static $instance;

	public $key;


	public static function instance( $key = NULL ) {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( $key );
		}

		return self::$instance;

	}


	private function __construct( $key ) {

		if ( function_exists( 'spl_autoload_register' ) ) {
			spl_autoload_register( array( $this, 'autoload' ) );
		}

		$this->key = isset( $key ) ? $key : 'themeplate';

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );
		add_action( 'save_post', array( ThemePlate_PostMeta::instance(), 'save' ) );

	}


	private function autoload( $class ) {

		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

		if ( ! class_exists( $class ) && file_exists( $path ) ) {
			require_once( $path );
		}

	}


	public function admin_menu() {

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


	public function admin_init() {

		register_setting( $this->key, $this->key );

	}


	public function admin_notices() {
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'theme-options' &&
			isset( $_REQUEST['settings-updated'] ) &&  $_REQUEST['settings-updated'] == true ) {
			echo '<div id="themeplate-message" class="updated"><p><strong>Settings updated.</strong></p></div>';
		}
	}


	public function scripts_styles() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_style( 'themeplate-style', TP_URL . 'assets/themeplate.css', array(), TP_VERSION, false );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'assets/themeplate.js', array(), TP_VERSION, true );

	}


	public function post_type( $args ) {

		ThemePlate_CPT::instance()->add_type( $args );

	}


	public function taxonomy( $args ) {

		ThemePlate_CPT::instance()->add_tax( $args );

	}


	public function post_meta( $args ) {

		ThemePlate_PostMeta::instance()->add( $args );

	}


	public function settings( $args ) {

		ThemePlate_Settings::instance()->add( $args );

	}


	public function term_meta( $args ) {

		ThemePlate_TermMeta::instance()->add( $args );

	}

}


function ThemePlate( $key = NULL ) {

	return ThemePlate::instance( $key );

}