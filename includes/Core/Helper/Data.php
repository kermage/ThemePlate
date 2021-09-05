<?php

/**
 * Helper functions
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Core\Helper;

class Data {

	private static $storages = array();


	public static function store( $config ) {

		$key = 'options' === $config['object_type'] ? $config['page'] : $config['object_type'];

		foreach ( $config['fields'] as $field ) {
			self::$storages[ strtolower( $key ) ][ $config['id'] . '_' . $field['id'] ] = $field;
		}

	}


	public static function retreive( $key, $id ) {

		return self::$storages[ strtolower( $key ) ][ $id ] ?? Field::filter( array() );

	}


	private static function get_default( $key, $id ) {

		$config = self::retreive( $key, $id );

		return $config['default'];

	}


	public static function get_meta( $meta_key, $post_id = false, $meta_type = 'post', $single = true ) {

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$value = get_metadata( $meta_type, $post_id, $meta_key, $single );

		return $value ?: self::get_default( $meta_type, $meta_key );

	}


	public static function get_option( $key, $page ) {

		$options = get_option( $page );
		$value   = $options[ $key ] ?? '';

		return $value ?: self::get_default( $page, $key );

	}

}
