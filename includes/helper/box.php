<?php

/**
 * Helper functions
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Helper_Box {

	public static function preprare_save( $data ) {

		if ( ! is_array( $data ) ) {
			return $data;
		}

		$filtered = array();

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = self::filter( $value );
			}

			$filtered[ $key ] = $value;
		}

		return $filtered;

	}


	private static function filter( $data ) {

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[ $key ] = self::filter( $data[ $key ] );
			}

			if ( empty( $value ) ) {
				unset( $data[ $key ] );
			}
		}

		return array_merge( array_filter( $data ) );

	}


	public static function get_priority( $config ) {

		switch ( $config['priority'] ) {
			default:
			case 'default':
				$priority = 10;
				break;
			case 'high':
				$priority = 5;
				break;
			case 'low':
				$priority = 15;
				break;
		}

		return $priority;

	}

}
