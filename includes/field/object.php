<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Object {

	public static function render( $field ) {

		switch ( $field['type'] ) {
			default:
			case 'post':
			case 'page':
				$defaults = array(
					'post_type'   => $field['type'],
					'numberposts' => -1,
				);
				if ( ThemePlate_Helper_Main::is_sequential( $field['options'] ) ) {
					$field['options'] = array( 'post_type' => $field['options'] );
				}
				$args     = ThemePlate_Helper_Main::fool_proof( $defaults, $field['options'] );
				$items    = get_posts( $args );
				$val_prop = 'ID';
				$lbl_prop = 'post_title';
				break;
			case 'user':
				$defaults = array( 'role' => '' );
				if ( ThemePlate_Helper_Main::is_sequential( $field['options'] ) ) {
					$field['options'] = array( 'role' => $field['options'] );
				}
				$args     = ThemePlate_Helper_Main::fool_proof( $defaults, $field['options'] );
				$items    = get_users( $args );
				$val_prop = 'ID';
				$lbl_prop = 'display_name';
				break;
			case 'term':
				$defaults = array( 'taxonomy' => array() );
				if ( ThemePlate_Helper_Main::is_sequential( $field['options'] ) ) {
					$field['options'] = array( 'taxonomy' => $field['options'] );
				}
				$args     = ThemePlate_Helper_Main::fool_proof( $defaults, $field['options'] );
				$items    = get_terms( $args );
				$val_prop = 'term_id';
				$lbl_prop = 'name';
				break;
		}

		$items = array_map( static function( $post ) {
			return (array) $post;
		}, (array) $items );

		$field['type']    = 'select2';
		$field['options'] = array_column( $items, $val_prop, $lbl_prop );

		ThemePlate_Field_Select::render( $field );

	}

}
