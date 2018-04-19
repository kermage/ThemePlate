<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Checkbox {

	public static function render( $field, $list = false ) {

		$seq = ThemePlate_Helper_Main::is_sequential( $field['options'] );
		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" />';
		if ( ! empty( $field['options'] ) ) {
			echo '<fieldset id="' . esc_attr( $field['id'] ) . '">';
			foreach ( $field['options'] as $value => $option ) {
				$value = ( $seq ? $value + 1 : $value );
				echo '<' . ( $list ? 'p' : 'span' ) . '>';
				echo '<label><input type="checkbox" name="' . esc_attr( $field['name'] ) . '[]" value="' . esc_attr( $value ) . '"';
				if ( in_array( strval( $value ), (array) $field['value'], true ) ) {
					echo ' checked="checked"';
				}
				echo ' />' . esc_html( $option ) . '</label>';
				echo '</' . ( $list ? 'p' : 'span' ) . '>';
			}
			echo '</fieldset>';
		} else {
			echo '<input type="checkbox" id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" value="1"' . checked( $field['value'], 1, false ) . ' />';
		}

	}

}
