<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate {

	private static $instance;

	public $key, $title, $slug, $pages;


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

		if ( is_array( $key ) && ! empty( $key ) ) {
			$this->title = array_shift( $key );
			$this->key = array_shift( $key );
		} else {
			$this->title = $key;
			$this->key = $this->title;
		}

		$this->title = ! empty( $this->title ) ? $this->title : 'ThemePlate Options';
		$this->key = sanitize_title( ! empty( $this->key ) ? $this->key : $this->title );
		$this->pages = isset( $pages ) ? $pages : array();
		$this->slug = isset( $pages ) ? key( $pages ) : 'options';

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );
		add_filter( 'wp_nav_menu_args', array( $this, 'clean_walker' ) );
		add_action( 'after_setup_theme', array( 'ThemePlate_Cleaner', 'instance' ) );

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
			$this->title,
			// Menu Title
			$this->title,
			// Capability
			'edit_theme_options',
			// Menu Slug
			$this->key . '-' . $this->slug,
			// Content Function
			array( 'ThemePlate_Settings', 'page' )
		);

		if ( $this->pages ) {
			$title = array_shift( $this->pages );
			add_submenu_page( $this->key . '-' . $this->slug, $title, $title, 'edit_theme_options', $this->key . '-' . $this->slug, array( 'ThemePlate_Settings', 'page' ) );

			foreach ( $this->pages as $id => $title ) {
				add_submenu_page( $this->key . '-' . $this->slug, $title, $title, 'edit_theme_options', $this->key . '-' . $id, array( 'ThemePlate_Settings', 'page' ) );
			}
		}

	}


	public function admin_init() {

		register_setting( $this->key . '-' . $this->slug, $this->key . '-' . $this->slug );

		if ( $this->pages ) {
			foreach ( $this->pages as $id => $title ) {
				register_setting( $this->key . '-' . $id, $this->key . '-' . $id );
			}
		}

	}


	public function admin_notices() {

		if ( ! isset( $_REQUEST['page'] ) || ! isset( $_REQUEST['settings-updated'] ) ) {
			return;
		}

		$page = str_replace( ThemePlate()->key . '-', '', $_REQUEST['page'] );

		if ( ( $_REQUEST['page'] === $this->key . '-' . $this->slug || array_key_exists( $page, $this->pages ) ) && $_REQUEST['settings-updated'] == true ) {
			echo '<div id="themeplate-message" class="updated"><p><strong>Settings updated.</strong></p></div>';
		}

	}


	public function scripts_styles() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_style( 'themeplate-style', TP_URL . 'assets/themeplate.css', array(), TP_VERSION, false );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'assets/themeplate.js', array(), TP_VERSION, true );
		wp_enqueue_style( 'themeplate-select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5', false );
		wp_enqueue_script( 'themeplate-select2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js', array(), '4.0.5', true );

	}


	public function clean_walker( $args ) {

		if ( empty( $args['container_class'] ) && empty( $args['container_id'] ) ) {
			$args['container'] = false;
		}

		if ( empty( $args['walker'] ) ) {
			$args['walker'] = new ThemePlate_NavWalker();
		}

		$args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';

		return $args;

	}


	public function menu( $id, $title ) {

		if ( ! $this->pages ) {
			$this->pages = array( $this->key . '-' . $this->slug => $this->title );
		}

		$this->pages = array_merge( $this->pages, array( $id => $title ) );

	}


	public function post_type( $args ) {

		new ThemePlate_CPT( 'post_type', $args );

	}


	public function taxonomy( $args ) {

		new ThemePlate_CPT( 'taxonomy', $args );

	}


	public function post_meta( $args ) {

		new ThemePlate_PostMeta( $args );

	}


	public function settings( $args ) {

		new ThemePlate_Settings( $args );

	}


	public function term_meta( $args ) {

		new ThemePlate_TermMeta( $args );

	}


	public function user_meta( $args ) {

		new ThemePlate_UserMeta( $args );

	}

}


function ThemePlate( $key = NULL, $pages = NULL ) {

	return ThemePlate::instance( $key, $pages );

}
