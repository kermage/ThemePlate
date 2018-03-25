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

		$this->setup();

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );
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


	public function setup() {

		$main = new ThemePlate_Page( array(
			'id' => $this->key . '-' . $this->slug,
			'title' => array_shift( $this->pages ),
			'parent' => $this->key . '-' . $this->slug,
			'menu' => $this->title
		) );

		if ( ! $this->pages ) {
			return;
		}

		$subs = array();

		foreach ( $this->pages as $id => $title ) {
			$subs[$id] = new ThemePlate_Page( array(
				'id' => $this->key . '-' . $id,
				'title' => $title,
				'parent' => $this->key . '-' . $this->slug
			) );
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
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_style( 'themeplate-style', TP_URL . 'assets/themeplate.css', array(), TP_VERSION, false );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'assets/themeplate.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-show-hide', TP_URL . 'assets/show-hide.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-repeater', TP_URL . 'assets/repeater.js', array(), TP_VERSION, true );
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


	public function after_title() {

		global $post, $wp_meta_boxes;

		do_meta_boxes( get_current_screen(), 'after_title', $post );

		unset( $wp_meta_boxes['post']['after_title'] );

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
