<?php

/**
 * Setup fields
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Fields {

	private static $instance;


	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	public function __construct() {


	}

}

ThemePlate_Fields::instance();
