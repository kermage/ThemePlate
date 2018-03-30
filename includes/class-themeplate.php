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

		if ( function_exists( 'spl_autoload_register' ) ) {
			spl_autoload_register( array( $this, 'autoload' ) );
		}

		$defaults = array(
			'title' => 'ThemePlate Options',
			'key' => 'tp',
			'pages' => array(),
			'slug' => 'options'
		);
		$config = $this->prepare( $key, $pages );
		$config = ThemePlate_Helpers::fool_proof( $defaults, $config );

		$this->setup( $config );

		add_filter( 'wp_nav_menu_args', array( $this, 'clean_walker' ) );
		add_filter( 'edit_form_after_title', array( $this, 'after_title' ), 11 );
		add_action( 'after_setup_theme', array( 'ThemePlate_Cleaner', 'instance' ) );

	}


	private function autoload( $class ) {

		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

		if ( ! class_exists( $class ) && file_exists( $path ) ) {
			require_once( $path );
		}

	}


	private function prepare( $key, $pages ) {

		$config = array();

		if ( ! empty( $key ) ) {
			if ( is_array( $key ) ) {
				$config['title'] = array_shift( $key );
				$config['key'] = array_shift( $key );
			} else {
				$config['title'] = $key;
				$config['key'] = sanitize_title( $key );
			}
		}

		if ( ! empty( $pages ) ) {
			$config['pages'] = $pages;
			$config['slug'] = key( $pages );
		}

		return $config;

	}


	private function setup( $config ) {

		$this->key = $config['key'];
		$this->slug = $config['slug'];

		$args = array(
			'id' => $config['key'] . '-' . $config['slug'],
			'title' => $config['title']
		);

		if ( $config['pages'] ) {
			$args['title'] = array_shift( $config['pages'] );
			$args['parent'] = $config['key'] . '-' . $config['slug'];
			$args['menu'] = $config['title'];
		}

		$main = $this->page( $args );

		if ( ! $config['pages'] ) {
			return;
		}

		$subs = array();

		foreach ( $config['pages'] as $id => $title ) {
			$this->menu( $id, $title );
		}

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


	public function after_title() {

		global $post, $wp_meta_boxes;

		do_meta_boxes( get_current_screen(), 'after_title', $post );

		unset( $wp_meta_boxes['post']['after_title'] );

	}


	public function menu( $id, $title ) {

		$args = array(
			'id' => $this->key . '-' . $id,
			'title' => $title,
			'parent' => $this->key . '-' . $this->slug
		);

		$this->page( $args );

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

		new ThemePlate_Page( $args );

	}

}
