<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Multidate {

	public static function render( $field ) {

		echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="themeplate-date-picker" value="' . esc_attr( $field['value'] ) . '" />';

	}

}
