<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Date {

	public static function render( $field ) {

		echo '<div class="wrapper">';

		if ( $field['multiple'] ) {
			echo '<div id="' . esc_attr( $field['id'] ) . '" class="themeplate-date-picker multiple">';
			echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $field['value'] ) . '" />';
			echo '</div>';
			echo '<ul class="ul-disc"></ul>';
		} else {
			echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="themeplate-date-picker single" value="' . esc_attr( $field['value'] ) . '" />';
		}

		echo '</div>';

	}

}
