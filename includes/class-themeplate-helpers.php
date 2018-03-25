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

		if ( ! empty( $meta_box['show_on_cb'] ) ) {
			$check = call_user_func( $meta_box['show_on_cb'] );
		}

		if ( ! empty( $meta_box['show_on_id'] ) ) {
			$check = array_intersect( (array) $object_id, (array) $meta_box['show_on_id'] );
		}

		if ( ! empty( $meta_box['hide_on_cb'] ) ) {
			$check = ! call_user_func( $meta_box['hide_on_cb'] );
		}

		if ( ! empty( $meta_box['hide_on_id'] ) ) {
			$check = ! array_intersect( (array) $object_id, (array) $meta_box['hide_on_id'] );
		}

		return $check;

	}


	public static function normalize_options( $container ) {

		if ( ! empty( $container['show_on'] ) ) {
			$value = $container['show_on'];

			if ( is_callable( $value ) ) {
				$container['show_on_cb'] = $value;
				unset( $container['show_on'] );
			} elseif ( is_array( $value ) ) {
				if ( ! self::is_sequential( $value ) ) {
					$value = array( $value );
					$container['show_on'] = array( $container['show_on'] );
				}

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					$container['show_on_id'] = $value[0]['value'];
					unset( $container['show_on'] );
				}
			}
		}

		if ( ! empty( $container['hide_on'] ) ) {
			$value = $container['hide_on'];

			if ( is_callable( $value ) ) {
				$container['hide_on_cb'] = $value;
				unset( $container['hide_on'] );
			} elseif ( is_array( $value ) ) {
				if ( ! self::is_sequential( $value ) ) {
					$value = array( $value );
					$container['hide_on'] = array( $container['hide_on'] );
				}

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					$container['hide_on_id'] = $value[0]['value'];
					unset( $container['hide_on'] );
				}
			}
		}

		return $container;

	}


	public static function render_options( $container ) {

		if ( ! empty( $container['show_on'] ) || ! empty( $container['hide_on'] ) ) {
			echo '<div class="themeplate-options"';

			if ( ! empty( $container['show_on'] ) ) {
				$show_on = json_encode( $container['show_on'], JSON_NUMERIC_CHECK );
				echo ' data-show="' . esc_attr( $show_on ) . '"';
			}

			if ( ! empty( $container['hide_on'] ) ) {
				$hide_on = json_encode( $container['hide_on'], JSON_NUMERIC_CHECK );
				echo ' data-hide="' . esc_attr( $hide_on ) . '"';
			}

			echo '></div>';
		}

	}


	public static function fool_proof( $defaults, $options ) {

		$result = array_merge( $defaults, $options );

		foreach ( $defaults as $key => $value ) {
			if ( is_array( $value ) ) {
				$result[$key] = (array) $result[$key];
			}

			if ( is_bool( $value ) ) {
				$result[$key] = (bool) $result[$key];
			}
		}

		return $result;

	}


	public static function is_sequential( $array ) {

		return ( array_keys( $array ) === range( 0, count( $array ) - 1 ) );

	}

}
