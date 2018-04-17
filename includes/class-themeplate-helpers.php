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

		if ( isset( $meta_box['show_on_cb'] ) || isset( $meta_box['show_on_id'] ) ) {
			$check = self::display_check( $object_id, $meta_box['show_on_cb'], $meta_box['show_on_id'] );
		}

		if ( isset( $meta_box['hide_on_cb'] ) || isset( $meta_box['hide_on_id'] ) ) {
			$check = ! self::display_check( $object_id, $meta_box['hide_on_cb'], $meta_box['hide_on_id'] );
		}

		return $check;

	}


	private static function display_check( $object_id, $callback, $id ) {

		$result = true;

		if ( $callback ) {
			$result = call_user_func( $callback );
		}

		if ( $id ) {
			$result = array_intersect( (array) $object_id, (array) $id );
		}

		return $result;

	}


	public static function normalize_options( $container ) {

		if ( ! empty( $container['show_on'] ) ) {
			$container = self::option_check( 'show_on', $container );
		}

		if ( ! empty( $container['hide_on'] ) ) {
			$container = self::option_check( 'hide_on', $container );
		}

		return $container;

	}


	private static function option_check( $type, $container ) {

		$additional = array(
			$type . '_cb' => '',
			$type . '_id' => '',
		);
		$container  = array_merge( $additional, $container );
		$value      = $container[ $type ];

		if ( is_callable( $value ) ) {
			$container[ $type . '_cb' ] = $value;
			unset( $container[ $type ] );
		} elseif ( is_array( $value ) ) {
			if ( ! self::is_sequential( $value ) ) {
				$value              = array( $value );
				$container[ $type ] = array( $container[ $type ] );
			}

			if ( ( count( $value ) === 1 ) && isset( $value[0]['key'] ) && 'id' === $value[0]['key'] ) {
				$container[ $type . '_id' ] = $value[0]['value'];
				unset( $container[ $type ] );
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
				$result[ $key ] = (array) $result[ $key ];
			}

			if ( is_bool( $value ) ) {
				$result[ $key ] = (bool) $result[ $key ];
			}

			if ( is_int( $value ) ) {
				$result[$key] = (int) $result[$key];
			}
		}

		return $result;

	}


	public static function is_sequential( $array ) {

		return ( array_keys( $array ) === range( 0, count( $array ) - 1 ) );

	}


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
