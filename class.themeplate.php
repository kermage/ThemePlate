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

		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'meta-boxes.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'post-types.php' );
		require_once( TP_PATH . DIRECTORY_SEPARATOR . 'settings.php' );

	}

}

ThemePlate::instance();
