<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Color {

	public static function render( $field ) {

		echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="themeplate-color-picker" value="' . esc_attr( $field['value'] ) . '"' . ( esc_attr( $field['default'] ) ? ' data-default-color="' . esc_attr( $field['default'] ) . '"' : '' );
		if ( ! empty( $field['options'] ) ) {
			$values = json_encode( $field['options'] );
			echo ' data-palettes="' . esc_attr( $values ) . '"';
		}
		echo ' />';

	}

}
