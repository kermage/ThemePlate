<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Date {

	public static function render( $field ) {

		echo '<div id="' . esc_attr( $field['id'] ) . '" class="themeplate-date-picker' . ( $field['multiple'] ? ' multiple' : ' single' ) . '">';
		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $field['value'] ) . '" />';
		echo '</div>';

		if ( $field['multiple'] ) {
			echo '<ul class="ul-disc"></ul>';
		}

	}

}
