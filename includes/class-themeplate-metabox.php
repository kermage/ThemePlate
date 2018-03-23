<?php

/**
 * Setup meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_MetaBox {

	private $meta_box;


	public function __construct( $params ) {

		if ( ! is_array( $params ) || empty( $params ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $params ) || ! array_key_exists( 'title', $params ) ) {
			return false;
		}

		if ( ! is_array( $params['fields'] ) || empty( $params['fields'] ) ) {
			return false;
		}

		$this->meta_box = $params;

	}

}
