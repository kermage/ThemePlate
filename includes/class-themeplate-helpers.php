<?php

/**
 * Helper functions
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Helpers {

	public static function should_display( $meta_box, $object_id ) {

		$check = true;

		if ( isset( $meta_box['show_on'] ) ) {
			$value = $meta_box['show_on'];

			if ( is_callable( $value ) ) {
				$check = call_user_func( $value );
				unset( $meta_box['show_on'] );
			} elseif ( is_array( $value ) ) {
				if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
					$value = array( $value );
					$meta_box['show_on'] = array( $meta_box['show_on'] );
				}

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['show_on'] );

					if ( ! array_intersect( (array) $object_id, (array) $value[0]['value'] ) ) {
						$check = false;
					}
				}
			}
		}

		if ( isset( $meta_box['hide_on'] ) ) {
			$value = $meta_box['hide_on'];

			if ( is_callable( $value ) ) {
				$check = ! call_user_func( $value );
				unset( $meta_box['hide_on'] );
			} elseif ( is_array( $value ) ) {
				if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
					$value = array( $value );
					$meta_box['hide_on'] = array( $meta_box['hide_on'] );
				}

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['hide_on'] );

					if ( array_intersect( (array) $object_id, (array) $value[0]['value'] ) ) {
						$check = false;
					}
				}
			}
		}

		return $check;

	}

}
