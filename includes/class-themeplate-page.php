<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Page {

	private $config;


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $config ) || ! array_key_exists( 'title', $config ) ) {
			return false;
		}

		$this->config = $config;

	}

}
