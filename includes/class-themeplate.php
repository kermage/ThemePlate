<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate {

	private static $instance;

	public $key, $title, $slug, $pages;


	public static function instance( $key, $pages ) {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( $key, $pages );
		}

		return self::$instance;

	}


	private function __construct( $key, $pages ) {

		if ( function_exists( 'spl_autoload_register' ) ) {
			spl_autoload_register( array( $this, 'autoload' ) );
		}

		$defaults = array(
			'title' => 'ThemePlate Options',
			'key'   => 'tp',
			'pages' => array(),
			'slug'  => 'options',
		);
		$config   = $this->prepare( $key, $pages );
		$config   = ThemePlate_Helpers::fool_proof( $defaults, $config );

		$this->setup( $config );

		add_filter( 'edit_form_after_title', array( $this, 'after_title' ), 11 );
		add_action( 'after_setup_theme', array( 'ThemePlate_Cleaner', 'instance' ) );

	}


	private function autoload( $class ) {

		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

		if ( ! class_exists( $class ) && file_exists( $path ) ) {
			require_once $path;
		}

	}


	private function prepare( $key, $pages ) {

		$config = array();

		if ( ! empty( $key ) ) {
			if ( is_array( $key ) ) {
				$config['title'] = array_shift( $key );
				$config['key']   = array_shift( $key );
			} else {
				$config['title'] = $key;
				$config['key']   = sanitize_title( $key );
				$this->stall_update();
			}
		}

		if ( ! empty( $pages ) ) {
			$config['pages'] = $pages;
			$config['slug']  = key( $pages );
		}

		return $config;

	}


	private function setup( $config ) {

		$this->key  = $config['key'];
		$this->slug = $config['slug'];

		//
		$this->title = $config['title'];
		$this->pages = $config['pages'];
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );
		//

	}


	private function stall_update() {

		add_filter( 'site_transient_update_plugins', function( $value ) {
			unset( $value->response[ plugin_basename( TP_FILE ) ] );
			return $value;
		} );

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

		register_setting( $this->key . '-' . $this->slug, $this->key . '-' . $this->slug, array( 'ThemePlate_Settings', 'save' ) );

		if ( $this->pages ) {
			foreach ( $this->pages as $id => $title ) {
				register_setting( $this->key . '-' . $id, $this->key . '-' . $id, array( 'ThemePlate_Settings', 'save' ) );
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

		$screen = get_current_screen();
		$wanted_base = array( 'post', 'edit-tags', 'term', 'user', 'user-edit', 'profile' );
		$wanted_id = array_map(
			function( $value ) {
				return sanitize_title( $this->title ) . '_page_' . $this->key . '-' . $value;
			}, array_keys( $this->pages )
		);
		array_push( $wanted_id, 'toplevel_page_' . $this->key . '-' . $this->slug );

		if ( ! in_array( $screen->base, $wanted_base ) && ! in_array( $screen->id, $wanted_id ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'themeplate-style', TP_URL . 'assets/themeplate.css', array(), TP_VERSION, 'all' );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'assets/themeplate.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-show-hide', TP_URL . 'assets/show-hide.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-repeater', TP_URL . 'assets/repeater.js', array(), TP_VERSION, true );
		wp_enqueue_style( 'themeplate-select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5', 'all' );
		wp_enqueue_script( 'themeplate-select2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js', array(), '4.0.5', true );

	}


	public function after_title() {

		global $post, $wp_meta_boxes;

		do_meta_boxes( get_current_screen(), 'after_title', $post );

		unset( $wp_meta_boxes['post']['after_title'] );

	}


	public function menu( $id, $title ) {

		_deprecated_function( __METHOD__, '2.11.0', 'ThemePlate()->page( $args ) to add an options page' );

		$args = array(
			'id'    => $id,
			'title' => $title,
		);

		$this->page( $args );
		$this->stall_update();

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


	public function page( $args ) {

		//
		if ( ! $this->pages ) {
			$this->pages = array( $this->key . '-' . $this->slug => $this->title );
		}

		$this->pages = array_merge( $this->pages, array( $args['id'] => $args['title'] ) );
		//

	}

}


function ThemePlate( $key = null, $pages = null ) {

	if ( ! empty( $key ) && ! is_array( $key ) ) {
		_deprecated_argument( __FUNCTION__, '2.11.0', 'Use the newer way to initialize by passing <b>array( \'Options Title\', \'prefixed_key\' )</b>.' );
	}

	return ThemePlate::instance( $key, $pages );

}
