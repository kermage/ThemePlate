<?php

/**
 * Helper functions
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Helper_Meta {

	public static function should_display( $meta_box, $object_id ) {

		$check = true;

		foreach ( array( 'show', 'hide' ) as $key ) {
			if ( isset( $meta_box[ $key . '_on_cb' ] ) || isset( $meta_box[ $key . '_on_id' ] ) ) {
				$check = self::display_check( $object_id, $meta_box[ $key . '_on_cb' ], $meta_box[ $key . '_on_id' ] );

				if ( 'hide' === $key ) {
					$check = ! $check;
				}
			}
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

		foreach ( array( 'show', 'hide' ) as $key ) {
			if ( ! empty( $container[ $key . '_on' ] ) ) {
				$container = self::option_check( $key . '_on', $container );
			}
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
			if ( ! ThemePlate_Helper_Main::is_sequential( $value ) ) {
				$value              = array( $value );
				$container[ $type ] = array( $container[ $type ] );
			}

			if ( ( 1 === count( $value ) ) && isset( $value[0]['key'] ) && 'id' === $value[0]['key'] ) {
				$container[ $type . '_id' ] = $value[0]['value'];
				unset( $container[ $type ] );
			}
		}

		return $container;

	}


	public static function render_options( $container ) {

		if ( ! empty( $container['show_on'] ) || ! empty( $container['hide_on'] ) ) {
			echo '<div class="themeplate-options"';

			foreach ( array( 'show', 'hide' ) as $key ) {
				if ( ! empty( $container[ $key . '_on' ] ) ) {
					$value = json_encode( $container[ $key . '_on' ], JSON_NUMERIC_CHECK );
					echo ' data-' . $key . '="' . esc_attr( $value ) . '"';
				}
			}

			echo '></div>';
		}

	}


	public static function display_column( $id, $key ) {

		$value = get_post_meta( $id, $key );

		if ( $value ) {
			print_r( $value );
		}

	}

}
