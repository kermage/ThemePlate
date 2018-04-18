<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Radio {

	public static function render( $field, $list = false ) {

		$seq = ThemePlate_Helpers::is_sequential( $field['options'] );
		if ( ! empty( $field['options'] ) ) {
			echo '<fieldset id="' . esc_attr( $field['id'] ) . '">';
			foreach ( $field['options'] as $value => $option ) {
				$value = ( $seq ? $value + 1 : $value );
				echo '<' . ( $list ? 'p' : 'span' ) . '>';
				echo '<label><input type="radio" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $value ) . '"' . checked( $field['value'], $value, false ) . ' />' . esc_html( $option ) . '</label>';
				echo '</' . ( $list ? 'p' : 'span' ) . '>';
			}
			echo '</fieldset>';
		}

	}

}
