<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Core\Field;

class Link {

	public static function render( $field ) {

		echo '<div class="wrapper">';
		echo '<input type="button" class="button attachment-add" value="Select Link" />';

		foreach ( array( 'url', 'text', 'target' ) as $attr ) {
			$value = isset( $field['value'][ $attr ] ) ? $field['value'][ $attr ] : '';

			echo '<input type="hidden" class="input-' . $attr . '" name="' . esc_attr( $field['name'] ) . '[' . $attr . ']" value="' . esc_attr( $value ) . '">';
		}

		echo '</div>';

	}

}
