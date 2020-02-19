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
		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '["link"]" value="' . esc_attr( $field['value']['link'] ) . '">';
		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '["text"]" value="' . esc_attr( $field['value']['text'] ) . '">';
		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '["target"]" value="' . esc_attr( $field['value']['target'] ) . '">';
		echo '</div>';

	}

}
