<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

use ThemePlate\Cleaner;
use ThemePlate\Column;
use ThemePlate\Core\Helper\Main;
use ThemePlate\CPT\PostType;
use ThemePlate\CPT\Taxonomy;
use ThemePlate\Meta\Menu;
use ThemePlate\Meta\Post;
use ThemePlate\Meta\Term;
use ThemePlate\Meta\User;
use ThemePlate\Page;
use ThemePlate\Settings;

class ThemePlate {

	private static $_instance;

	private $key;
	private $slug;
	private $stalled;


	public static function instance( $key, $pages ) {

		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self( $key, $pages );
		}

		return self::$_instance;

	}


	private function __construct( $key, $pages ) {

		$defaults = array(
			'title' => 'ThemePlate Options',
			'key'   => 'tp',
			'pages' => array(),
			'slug'  => 'options',
		);
		$config   = $this->prepare( $key, $pages );
		$config   = Main::fool_proof( $defaults, $config );

		$this->setup( $config );

		add_filter( 'edit_form_after_title', array( $this, 'after_title' ), 11 );
		add_action( 'init', array( Cleaner::class, 'instance' ) );

		if ( defined( 'TP_DEVELOPMENT' ) ) {
			$this->stall_update();
			add_filter( 'admin_notices', array( $this, 'in_dev_mode' ), 0 );
		}

	}


	private function prepare( $key, $pages ) {

		$config = array();

		if ( ! empty( $key ) ) {
			if ( ! is_array( $key ) ) {
				$config['title'] = $key;
				$config['key']   = sanitize_title( $key );
				$this->stall_update();
			} elseif ( Main::is_sequential( $key ) ) {
				$config['title'] = array_shift( $key );
				$config['key']   = array_shift( $key );
				$this->stalled   = true;
			} else {
				$config = $key;
			}
		}

		if ( ! empty( $pages ) ) {
			$config['pages'] = $pages;
			$config['slug']  = key( $pages );
			$this->stall_update();
		}

		return $config;

	}


	private function stall_update() {

		$this->stalled = true;

		add_filter( 'site_transient_update_plugins', array( $this, 'unset_transient' ) );

	}


	public function unset_transient( $value ) {

		unset( $value->response[ plugin_basename( TP_FILE ) ] );

		return $value;

	}


	private function setup( $config ) {

		$this->key  = $config['key'];
		$this->slug = $config['slug'];

		$is_capable = apply_filters( 'themeplate_options_page', true );

		if ( $is_capable ) {
			$args = array(
				'id'    => $config['slug'],
				'title' => $config['title'],
			);

			if ( $config['pages'] ) {
				$args['title']  = array_shift( $config['pages'] );
				$args['parent'] = $config['slug'];
				$args['menu']   = $config['title'];
			}

			if ( is_string( $is_capable ) ) {
				$args['capability'] = $is_capable;
			}

			$this->page( $args );
		}

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


	public static function force_load_first( $plugins ) {

		if ( ! empty( $plugins ) ) {
			$plugin = basename( dirname( TP_FILE ) ) . '/' . basename( TP_FILE );
			$index  = array_search( $plugin, $plugins, true );

			if ( $index > 0 ) {
				unset( $plugins[ $index ] );
				array_unshift( $plugins, $plugin );
			}
		}

		return $plugins;

	}


	public function in_dev_mode() {

		echo '<div class="notice notice-warning"><p><strong>ThemePlate</strong> in development mode: <em>' . TP_FILE . '</em></p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}


	public function post_type( $args ) {

		try {
			return new PostType( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function taxonomy( $args ) {

		try {
			return new Taxonomy( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function post_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new Post( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function settings( $args ) {

		if ( isset( $args['page'] ) ) {
			$args['page'] = (array) $args['page'];

			foreach ( $args['page'] as $index => $value ) {
				$args['page'][ $index ] = $this->key . '-' . $value;
			}
		} else {
			$args['page'] = $this->key . '-' . $this->slug;
		}

		try {
			return new Settings( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function term_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new Term( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function user_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new User( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function menu_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new Menu( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function page( $args ) {

		$args['id'] = $this->key . '-' . $args['id'];

		if ( isset( $args['parent'] ) && 'options' === $args['parent'] ) {
			$args['parent'] = $this->key . '-options';
		}

		try {
			return new Page( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function column( $args ) {

		try {
			return new Column( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function set_stalled( $value ) {

		$this->stalled = $value;

	}


	public function get_key() {

		return $this->key;

	}


	public function __set( $name, $value ) {

		$method = 'set_' . $name;

		if ( method_exists( $this, $method ) ) {
			$this->$method( $value );

			return;
		}

		throw new Error( 'Cannot access private property ' . __CLASS__ . '::$' . $name );

	}


	public function __get( $name ) {

		$method = 'get_' . $name;

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}

		throw new Error( 'Cannot access private property ' . __CLASS__ . '::$' . $name );

	}

}
