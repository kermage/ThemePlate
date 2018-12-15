<?php

/**
 * Helper functions
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Helper_Main {

	public static function fool_proof( $defaults, $options ) {

		$result = array_merge( $defaults, $options );

		foreach ( $defaults as $key => $value ) {
			if ( is_array( $value ) ) {
				$result[ $key ] = (array) $result[ $key ];
				$result[ $key ] = array_filter( $result[ $key ] );
			}

			if ( is_bool( $value ) ) {
				$result[ $key ] = (bool) $result[ $key ];
			}

			if ( is_int( $value ) ) {
				$result[ $key ] = (int) $result[ $key ];
			}
		}

		return $result;

	}


	public static function is_sequential( $array ) {

		return ( array_keys( $array ) === range( 0, count( $array ) - 1 ) );

	}


	public static function is_complete( $config, $expected ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			return false;
		}

		foreach ( $expected as $key ) {
			if ( ! array_key_exists( $key, $config ) ) {
				return false;
			}
		}

		return true;

	}

}
