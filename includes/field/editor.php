<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Editor {

	public static function render( $field ) {

		$defaults = array(
			'textarea_name' => $field['name'],
			'textarea_rows' => 10,
		);
		$settings = ThemePlate_Helper_Main::fool_proof( $defaults, $field['options'] );
		wp_editor( $field['value'], $field['id'], $settings );

	}

}
