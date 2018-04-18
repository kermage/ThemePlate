<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Textarea {

	public static function render( $field ) {

		echo '<textarea name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" rows="4">' . esc_textarea( $field['value'] ) . '</textarea>';

	}

}
