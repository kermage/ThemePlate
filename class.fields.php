<?php

/**
 * Setup fields
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Fields {

	private static $instance;


	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	public function __construct() {


	}


	public function render( $field ) {

		if ( ! is_array( $field ) )
			return false;

		$list = false;

		switch ( $field['type'] ) {
			default:
			case 'text':
				echo '<input type="text" name="themeplate[' . $field['id'] . ']" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'textarea' :
				echo '<textarea name="themeplate[' . $field['id'] . ']" id="' . $field['id'] . '" rows="4">' . $field['value'] . '</textarea>';
				break;

			case 'select' :
				echo '<select name="themeplate[' . $field['id'] . ']' . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				echo '<option disabled="disabled" selected="selected" hidden>' . __( '&mdash; Select &mdash;' ) . '</option>';
				foreach( $field['options'] as $value => $option ) {
					echo '<option value="' . ( $value + 1 ) . '"';
					if ( in_array( ( $value + 1 ), (array) $field['value'] ) ) echo ' selected="selected"';
					echo '>' . $option . '</option>';
				}
				echo '</select>';
				break;

			case 'radiolist' :
				$list = true;
			case 'radio' :
				foreach( $field['options'] as $value => $option ) {
					echo '<label><input type="radio" name="themeplate[' . $field['id'] . ']" value="' . ( $value + 1 ) . '"' . checked( $field['value'], ( $value + 1 ), false ) . ' />' . $option . '</label>';
					echo ( $list ? '<br>' : '' );
				}
				break;

			case 'checklist' :
				$list = true;
			case 'checkbox' :
				echo '<input type="hidden" name="themeplate[' . $field['id'] . ']" />';
				if ( $field['options'] ) {
					foreach( $field['options'] as $value => $option ) {
						echo '<label><input type="checkbox" name="themeplate[' . $field['id'] . '][]" value="' . ( $value + 1 ) . '"';
						if ( in_array( ( $value + 1 ), (array) $field['value'] ) ) echo ' checked="checked"';
						echo ' />' . $option . '</label>';
						echo ( $list ? '<br>' : '' );
					}
				} else {
					echo '<input type="checkbox" id="' . $field['id'] . '" name="themeplate[' . $field['id'] . ']" value="1"' . checked( $field['value'], 1, false ) . ' />';
				}
				break;

			case 'color':
				echo '<input type="text" name="themeplate[' . $field['id'] . ']" id="' . $field['id'] . '" class="wp-color-picker" value="' . $field['value'] . '" data-default-color="' . $field['value'] . '" />';
				break;

			case 'file':
				echo '<input type="hidden" name="themeplate[' . $field['id'] . ']" id="themeplate_' . $field['id'] . '" value="' . $field['value'] . '" /><div id="themeplate_' . $field['id'] . '_files">';
				if ( $field['value'] ) {
					$files = explode( ',', $field['value'] );
					foreach( $files as $file ) {
						echo '<p>' . basename( get_attached_file( $file ) ) . '</p>';
					}
				}
				echo '</div><input type="button" class="button" id="themeplate_' . $field['id'] . '_button" value="' . ( $field['value'] ? 'Re-select' : 'Select' ) . '" ' . ( $field['multiple'] ? 'multiple' : '' ) . ' /> <input type="' . ( $field['value'] ? 'button' : 'hidden' ) . '" class="button" id="themeplate_' . $field['id'] . '_remove" value="Remove" />';
				break;

			case 'date':
				echo '<input type="date" name="themeplate[' . $field['id'] . ']" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'time':
				echo '<input type="time" name="themeplate[' . $field['id'] . ']" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'number':
				echo '<input type="number" name="themeplate[' . $field['id'] . ']" id="' . $field['id'] . '" value="' . $field['value'] . '"';
				if ( is_array( $field['options'] ) ) foreach( $field['options'] as $option => $value ) echo $option . '="' . $value . '"';
				echo ' />';
				break;

			case 'editor':
				$settings = array(
					'textarea_name' => 'themeplate[' . $field['id'] . ']',
					'textarea_rows' => 10
				);
				wp_editor( $field['value'], $field['id'], $settings );
				break;

			case 'page':
				echo '<select name="themeplate[' . $field['id'] . ']' . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				echo '<option disabled="disabled" selected="selected" hidden>' . __( '&mdash; Select &mdash;' ) . '</option>';
				$pages = get_pages( array ( 'post_type' => $field['options'] ) );
				foreach( $pages as $page ) {
					echo '<option value="' . $page->ID . '"';
					if ( in_array( $page->ID, (array) $field['value'] ) ) echo ' selected="selected"';
					echo '>' . $page->post_title . '</option>';
				}
				echo '</select>';
				break;

			case 'term':
				echo '<select name="themeplate[' . $field['id'] . ']' . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				echo '<option disabled="disabled" selected="selected" hidden>' . __( '&mdash; Select &mdash;' ) . '</option>';
				$terms = get_terms( array ( 'taxonomy' => $field['options'] ) );
				foreach( $terms as $term ) {
					echo '<option value="' . $term->term_id . '"';
					if ( in_array( $term->term_id, (array) $field['value'] ) ) echo ' selected="selected"';
					echo '>' . $term->name . '</option>';
				}
				echo '</select>';
				break;
		}

	}

}

ThemePlate_Fields::instance();
