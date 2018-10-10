<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate {

	private static $instance;

	public $key, $slug;


	public static function instance( $key, $pages ) {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( $key, $pages );
		}

		return self::$instance;

	}


	private function __construct( $key, $pages ) {

		spl_autoload_register( array( $this, 'autoload' ) );

		$defaults = array(
			'title' => 'ThemePlate Options',
			'key'   => 'tp',
			'pages' => array(),
			'slug'  => 'options',
		);
		$config   = $this->prepare( $key, $pages );
		$config   = ThemePlate_Helper_Main::fool_proof( $defaults, $config );

		$this->setup( $config );

		add_filter( 'edit_form_after_title', array( $this, 'after_title' ), 11 );
		add_action( 'after_setup_theme', array( 'ThemePlate_Cleaner', 'instance' ) );

	}


	private function autoload( $class ) {

		if ( 0 !== strpos( $class, 'ThemePlate' ) ) {
			return;
		}

		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes';
		$base = strtolower( str_replace( 'ThemePlate', '', $class ) );
		$name = strtr( $base, '_', DIRECTORY_SEPARATOR );
		$file = $path . $name . '.php';

		if ( ! class_exists( $class ) && file_exists( $file ) ) {
			require_once $file;
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


	private function stall_update() {

		add_filter( 'site_transient_update_plugins', function( $value ) {
			unset( $value->response[ plugin_basename( TP_FILE ) ] );
			return $value;
		} );

	}


	private function setup( $config ) {

		$this->key  = $config['key'];
		$this->slug = $config['slug'];

		$args = array(
			'id'    => $config['slug'],
			'title' => $config['title'],
		);

		if ( $config['pages'] ) {
			$args['title']  = array_shift( $config['pages'] );
			$args['parent'] = $config['slug'];
			$args['menu']   = $config['title'];
		}

		$this->page( $args );

		if ( ! $config['pages'] ) {
			return;
		}

		foreach ( $config['pages'] as $id => $title ) {
			$args = array(
				'id'     => $id,
				'title'  => $title,
				'parent' => $this->slug,
			);

			$this->page( $args );
		}

	}


	public function after_title() {

		global $post, $wp_meta_boxes;

		do_meta_boxes( get_current_screen(), 'after_title', $post );

		unset( $wp_meta_boxes['post']['after_title'] );

	}


	public function menu( $id, $title ) {

		_deprecated_function( __METHOD__, '2.11.0', 'ThemePlate()->page( $args ) to add an options page' );

		$args = array(
			'id'     => $id,
			'title'  => $title,
			'parent' => $this->slug,
		);

		$this->page( $args );
		$this->stall_update();

	}


	public function post_type( $args ) {

		try {
			return new ThemePlate_CPT( 'post_type', $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function taxonomy( $args ) {

		try {
			return new ThemePlate_CPT( 'taxonomy', $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function post_meta( $args ) {

		try {
			return new ThemePlate_Meta_Post( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function settings( $args ) {

		try {
			return new ThemePlate_Settings( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function term_meta( $args ) {

		try {
			return new ThemePlate_Meta_Term( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function user_meta( $args ) {

		try {
			return new ThemePlate_Meta_User( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function page( $args ) {

		try {
			return new ThemePlate_Page( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}

}


function ThemePlate( $key = null, $pages = null ) {

	if ( ! empty( $pages ) ) {
		_deprecated_argument( __FUNCTION__, '3.0.0', 'Use ThemePlate()->page( $args ) to create options pages instead.' );
	}

	if ( ! empty( $key ) && ! is_array( $key ) ) {
		_deprecated_argument( __FUNCTION__, '2.11.0', 'Use the newer way to initialize by passing <b>array( \'Options Title\', \'prefixed_key\' )</b>.' );
	}

	return ThemePlate::instance( $key, $pages );

}