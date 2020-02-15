<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate {

	private static $_instance;

	public $key;
	public $slug;
	public $stalled;


	public static function instance( $key, $pages ) {

		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self( $key, $pages );
		}

		return self::$_instance;

	}


	private function __construct( $key, $pages ) {

		require TP_PATH . 'vendor/autoload.php';
		require TP_PATH . 'includes/compatibility.php';

		$defaults = array(
			'title' => 'ThemePlate Options',
			'key'   => 'tp',
			'pages' => array(),
			'slug'  => 'options',
		);
		$config   = $this->prepare( $key, $pages );
		$config   = \ThemePlate\Core\Helper\Main::fool_proof( $defaults, $config );

		$this->setup( $config );

		add_filter( 'edit_form_after_title', array( $this, 'after_title' ), 11 );
		add_action( 'init', array( \ThemePlate\Cleaner::class, 'instance' ) );
		add_action( 'plugins_loaded', array( $this, 'force_load_first' ) );

	}


	private function prepare( $key, $pages ) {

		$config = array();

		if ( ! empty( $key ) ) {
			if ( ! is_array( $key ) ) {
				$config['title'] = $key;
				$config['key']   = sanitize_title( $key );
				$this->stall_update();
			} elseif ( \ThemePlate\Core\Helper\Main::is_sequential( $key ) ) {
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


	public function force_load_first() {

		$plugins = get_option( 'active_plugins' );

		if ( empty( $plugins ) ) {
			return;
		}

		$plugin = basename( __DIR__ ) . '/' . basename( TP_FILE );
		$index  = array_search( $plugin, $plugins, true );

		if ( $index > 0 ) {
			array_splice( $plugins, $index, 1 );
			array_unshift( $plugins, $plugin );
			update_option( 'active_plugins', $plugins );
		}

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
			return new \ThemePlate\CPT\PostType( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function taxonomy( $args ) {

		try {
			return new \ThemePlate\CPT\Taxonomy( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function post_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new \ThemePlate\Meta\Post( $args );
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
			return new \ThemePlate\Settings( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function term_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new \ThemePlate\Meta\Term( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function user_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new \ThemePlate\Meta\User( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function page( $args ) {

		$args['id'] = $this->key . '-' . $args['id'];

		if ( 'options' === $args['parent'] ) {
			$args['parent'] = $this->key . '-options';
		}

		try {
			return new \ThemePlate\Page( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function column( $args ) {

		try {
			return new \ThemePlate\Column( $args );
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
