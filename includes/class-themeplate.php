<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate {

	private static $instance;

	public $key, $title, $pages;


	public static function instance( $key = NULL, $pages = NULL ) {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( $key, $pages );
		}

		return self::$instance;

	}


	private function __construct( $key, $pages ) {

		if ( function_exists( 'spl_autoload_register' ) ) {
			spl_autoload_register( array( $this, 'autoload' ) );
		}

		$this->title = isset( $key ) ? $key : 'ThemePlate';
		$this->key = strtolower( $this->title );
		$this->pages = isset( $pages ) ? $pages : '';

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );
		add_filter( 'wp_nav_menu_args', array( $this, 'clean_walker' ) );
		add_action( 'save_post', array( ThemePlate_PostMeta::instance(), 'save' ) );
		add_action( 'after_setup_theme', array( ThemePlate_Cleaner::instance(), '__construct' ) );

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
			$this->title . ' Options',
			// Menu Title
			$this->title . ' Options',
			// Capability
			'edit_theme_options',
			// Menu Slug
			$this->key . '-options',
			// Content Function
			array( ThemePlate_Settings::instance(), 'page' )
		);

		if ( $this->pages ) {
			$title = array_shift( $this->pages );
			add_submenu_page( $this->key . '-options', $title, $title, 'edit_theme_options', $this->key . '-options', array( ThemePlate_Settings::instance(), 'page' ) );

			foreach ( $this->pages as $id => $title ) {
				add_submenu_page( $this->key . '-options', $title, $title, 'edit_theme_options', $this->key . '-' . $id, array( ThemePlate_Settings::instance(), 'page' ) );
			}
		}

	}


	public function admin_init() {

		register_setting( $this->key . '-options', $this->key . '-options' );

		if ( $this->pages ) {
			foreach ( $this->pages as $id => $title ) {
				register_setting( $this->key . '-' . $id, $this->key . '-' . $id );
			}
		}

	}


	public function admin_notices() {

		$page = str_replace( ThemePlate()->key . '-', '', $_REQUEST['page'] );

		if ( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] === $this->key . '-options' || array_key_exists( $page, $this->pages ) ) &&
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


	function clean_walker( $args ) {

		if ( empty( $args['container_class'] ) && empty( $args['container_id'] ) ) {
			$args['container'] = false;
		}

		if ( empty( $args['walker'] ) ) {
			$args['walker'] = new ThemePlate_NavWalker();
		}

		$args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';

		return $args;

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


	public function user_meta( $args ) {

		ThemePlate_UserMeta::instance()->add( $args );

	}

}


function ThemePlate( $key = NULL, $pages = NULL ) {

	return ThemePlate::instance( $key, $pages );

}
