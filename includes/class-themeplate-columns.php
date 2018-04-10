<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Columns {

	private $config;


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			throw new Exception();
		}

	}

}
