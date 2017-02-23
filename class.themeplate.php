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

		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class.meta-boxes.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class.post-types.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'class.settings.php' );

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function scripts_styles() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_style( 'themeplate-style', TP_URL . 'themeplate.css', array(), TP_VERSION, false );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'themeplate.js', array(), TP_VERSION, true );

	}

}

ThemePlate::instance();
