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

		$field_name = ( $field['prefix'] ? $field['prefix'] : ThemePlate()->key );
		$field_name .= '[' . $field['id'] . ']';

		$list = false;

		switch ( $field['type'] ) {
			default:
			case 'text':
				echo '<input type="text" name="' . $field_name . '" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'textarea' :
				echo '<textarea name="' . $field_name . '" id="' . $field['id'] . '" rows="4">' . $field['value'] . '</textarea>';
				break;

			case 'select' :
				echo '<select name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				}
				foreach( $field['options'] as $value => $option ) {
					$value = is_string( $value ) ? $value : ( $value + 1 );
					echo '<option value="' . $value . '"';
					if ( in_array( $value, (array) $field['value'] ) ) echo ' selected="selected"';
					echo '>' . $option . '</option>';
				}
				echo '</select>';
				break;

			case 'radiolist' :
				$list = true;
			case 'radio' :
				foreach( $field['options'] as $value => $option ) {
					$value = is_string( $value ) ? $value : ( $value + 1 );
					echo '<label><input type="radio" name="' . $field_name . '" value="' . $value . '"' . checked( $field['value'], $value, false ) . ' />' . $option . '</label>';
					echo ( $list ? '<br>' : '' );
				}
				break;

			case 'checklist' :
				$list = true;
			case 'checkbox' :
				echo '<input type="hidden" name="' . $field_name . '" />';
				if ( $field['options'] ) {
					foreach( $field['options'] as $value => $option ) {
						$value = is_string( $value ) ? $value : ( $value + 1 );
						echo '<label><input type="checkbox" name="' . $field_name . '[]" value="' . $value . '"';
						if ( in_array( $value, (array) $field['value'] ) ) echo ' checked="checked"';
						echo ' />' . $option . '</label>';
						echo ( $list ? '<br>' : '' );
					}
				} else {
					echo '<input type="checkbox" id="' . $field['id'] . '" name="' . $field_name . '" value="1"' . checked( $field['value'], 1, false ) . ' />';
				}
				break;

			case 'color':
				echo '<input type="text" name="' . $field_name . '" id="' . $field['id'] . '" class="wp-color-picker" value="' . $field['value'] . '" data-default-color="' . $field['std'] . '" />';
				break;

			case 'file':
				echo '<div id="themeplate_' . $field['id'] . '_preview" class="preview-holder' . ( $field['multiple'] ? ' multiple' : '' ) . '">';
				if ( $field['value'] ) {
					if( $field['multiple'] ) {
						foreach( (array) $field['value'] as $file ) {
							$name = basename( get_attached_file( $file ) );
							$info = wp_check_filetype( $name );
							$type = wp_ext2type( $info['ext'] );
							$preview = ( $type == 'image' ? wp_get_attachment_url( $file ) : includes_url( '/images/media/' ) . $type . '.png' );
							echo '<div id="file-' . $file . '" class="attachment"><div class="attachment-preview landscape"><div class="thumbnail">';
							echo '<div class="centered"><img src="' . $preview . '"/></div>';
							echo '<div class="filename"><div>' . $name . '</div></div>';
							echo '</div></div>';
							echo '<input type="hidden" name="' . $field_name . '[]" value="' . $file . '" />';
							echo '</div>';
						}
					} else {
						$file = $field['value'];
						$name = basename( get_attached_file( $file ) );
						$info = wp_check_filetype( $name );
						$type = wp_ext2type( $info['ext'] );
						$preview = ( $type == 'image' ? wp_get_attachment_url( $file ) : includes_url( '/images/media/' ) . $type . '.png' );
						echo '<div class="attachment"><div class="attachment-preview landscape"><div class="thumbnail">';
						echo '<div class="centered"><img src="' . $preview . '"/></div>';
						echo '<div class="filename"><div>' . $name . '</div></div>';
						echo '</div></div>';
						echo '<input type="hidden" name="' . $field_name . '" value="' . $file . '" />';
						echo '</div>';
					}
				}
				echo '</div>';
				if ( ! is_array( $field['value'] ) && ( strpos( $field['value'], ',' ) !== false ) ) {
					$field['value'] = explode( ',', $field['value'] );
				} elseif ( is_array( $field['value'] ) ) {
					$field['value'] = implode( ',', $field['value'] );
				}
				echo '<input type="hidden" id="themeplate_' . $field['id'] . '" value="' . $field['value'] . '" />';
				echo '<input type="button" class="button" id="themeplate_' . $field['id'] . '_button" value="' . ( $field['value'] ? 'Re-select' : 'Select' ) . '" ' . ( $field['multiple'] ? 'multiple' : '' ) . ' data-key="' . ( $field['prefix'] ? $field['prefix'] : ThemePlate()->key ) . '" />';
				echo '<input type="' . ( $field['value'] ? 'button' : 'hidden' ) . '" class="button" id="themeplate_' . $field['id'] . '_remove" value="Remove" ' . ( $field['multiple'] ? 'multiple' : '' ) . ' data-key="' . ( $field['prefix'] ? $field['prefix'] : ThemePlate()->key ) . '" />';
				break;

			case 'date':
				echo '<input type="date" name="' . $field_name . '" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'time':
				echo '<input type="time" name="' . $field_name . '" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'number':
				echo '<input type="number" name="' . $field_name . '" id="' . $field['id'] . '" value="' . $field['value'] . '"';
				if ( is_array( $field['options'] ) ) foreach( $field['options'] as $option => $value ) echo $option . '="' . $value . '"';
				echo ' />';
				break;

			case 'editor':
				$settings = array(
					'textarea_name' => $field_name,
					'textarea_rows' => 10
				);
				wp_editor( $field['value'], $field['id'], $settings );
				break;

			case 'post':
				$list = 'post';
			case 'page':
				echo '<select name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				}
				if ( $list == 'post' ) {
					$pages = get_posts( array( 'post_type' => $field['options'] ) );
				} else {
					$pages = get_pages( array( 'post_type' => $field['options'] ) );
				}
				foreach( $pages as $page ) {
					echo '<option value="' . $page->ID . '"';
					if ( in_array( $page->ID, (array) $field['value'] ) ) echo ' selected="selected"';
					echo '>' . $page->post_title . '</option>';
				}
				echo '</select>';
				break;

			case 'user':
				echo '<select name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				}
				$users = get_users( array( 'role' => $field['options'] ) );
				foreach( $users as $user ) {
					echo '<option value="' . $user->ID . '"';
					if ( in_array( $user->ID, (array) $field['value'] ) ) echo ' selected="selected"';
					echo '>' . $user->display_name . '</option>';
				}
				echo '</select>';
				break;

			case 'term':
				echo '<select name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				}
				$terms = get_terms( array( 'taxonomy' => $field['options'] ) );
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
