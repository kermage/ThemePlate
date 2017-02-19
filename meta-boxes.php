<?php

/**
 * Setup meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */

if( ! function_exists( 'themeplate_add_meta_box' ) ) {
	function themeplate_add_meta_box( $meta_box ) {
		if ( ! is_array( $meta_box ) )
			return false;

		if ( $meta_box['screen'] == 'post' )
			$meta_box['id'] =  $meta_box['id'] . '_post';

		add_meta_box( $meta_box['id'], $meta_box['title'], 'themeplate_create_meta_box', $meta_box['screen'], $meta_box['context'], $meta_box['priority'], $meta_box );
	}
}

if( ! function_exists( 'themeplate_create_meta_box' ) ) {
	function themeplate_create_meta_box( $post, $meta_box ) {
		if ( ! is_array( $meta_box ) )
			return false;

		if ( ! empty( $meta_box['args']['description'] ) )
			echo '<p>' . $meta_box['args']['description'] . '</p>';

		$fields = $meta_box['args']['fields'];
		wp_nonce_field( basename( __FILE__ ), 'themeplate_meta_box_nonce' );

		if ( is_array( $fields ) ) {
			echo '<table class="themeplate-meta-table">';

			foreach ( $fields as $id => $field ) {
				$meta = get_post_meta( $post->ID, $id, true );
				$meta = $meta ? $meta : $field['std'];
				echo '<tr><th><label for="' . $id . '"><strong>' . $field['name'] . '</strong><span>' . $field['desc'] . '</span></label></th>';

				switch ( $field['type'] ) {
					default:
					case 'text':
						echo '<td><input type="text" name="themeplate_meta[' . $id . ']" id="' . $id . '" value="' . $meta . '" /></td>';
						break;

					case 'textarea' :
						echo '<td><textarea name="themeplate_meta[' . $id . ']" id="' . $id . '" rows="4">' . $meta . '</textarea></td>';
						break;

					case 'select' :
						echo '<td><select name="themeplate_meta[' . $id . ']' . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $id . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
						echo '<option disabled="disabled" selected="selected" hidden>' . __( '&mdash; Select &mdash;' ) . '</option>';
						foreach( $field['options'] as $value => $option ) {
							echo '<option value="' . ( $value + 1 ) . '"';
							if ( in_array( ( $value + 1 ), (array) $meta ) ) echo ' selected="selected"';
							echo '>' . $option . '</option>';
						}
						echo '</select></td>';
						break;

					case 'radio' :
						echo '<td>';
						foreach( $field['options'] as $value => $option ) {
							echo '<label class="radio-label"><input type="radio" name="themeplate_meta[' . $id . ']" value="' . ( $value +  1 ) . '"' . checked( $meta, ( $value +  1 ), false ) . ' /> ' . $option . '</label>';
						}
						echo '</td>';
						break;

					case 'checkbox' :
						echo '<td><input type="hidden" name="themeplate_meta[' . $id . ']" value="0" /><input type="checkbox" id="' . $id . '" name="themeplate_meta[' . $id . ']" value="1"' . checked( $meta, 1, false ) . ' /></td>';
						break;

					case 'color':
						echo '<td><input type="text" name="themeplate_meta[' . $id . ']" id="' . $id . '" class="wp-color-picker" value="' . $meta . '" data-default-color="' . $meta . '" /></td>';
						break;

					case 'file':
						echo '<td><input type="hidden" name="themeplate_meta[' . $id . ']" id="' . $id . '" value="' . $meta . '" /><div id="' . $id . '_files">';
						if ( $meta ) {
							$files = explode( ',', $meta );
							foreach( $files as $file ) {
								echo '<p>' . basename( get_attached_file( $file ) ) . '</p>';
							}
						}
						echo '</div><input type="button" class="button" id="' . $id . '_button" value="' . ( $meta ? 'Re-select' : 'Select' ) . '" ' . ( $field['multiple'] ? 'multiple' : '' ) . ' /> <input type="' . ( $meta ? 'button' : 'hidden' ) . '" class="button" id="' . $id . '_remove" value="Remove" /></td>';
						break;

					case 'date':
						echo '<td><input type="date" name="themeplate_meta[' . $id . ']" id="' . $id . '" value="' . $meta . '" /></td>';
						break;

					case 'time':
						echo '<td><input type="time" name="themeplate_meta[' . $id . ']" id="' . $id . '" value="' . $meta . '" /></td>';
						break;

					case 'number':
						echo '<td><input type="number" name="themeplate_meta[' . $id . ']" id="' . $id . '" value="' . $meta . '"';
						if ( is_array( $field['options'] ) ) foreach( $field['options'] as $option => $value ) echo $option . '="' . $value . '"';
						echo ' /></td>';
						break;

					case 'editor':
						$settings = array(
							'textarea_name' => 'themeplate_meta[' . $id . ']',
							'textarea_rows' => 10
						);
						echo '<td>';
						wp_editor( $meta, $id, $settings );
						echo '</td>';
						break;

					case 'page':
						echo '<td><select name="themeplate_meta[' . $id . ']' . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $id . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
						echo '<option disabled="disabled" selected="selected" hidden>' . __( '&mdash; Select &mdash;' ) . '</option>';
						$pages = get_pages( array ( 'post_type' => $field['options'] ) );
						foreach( $pages as $page ) {
							echo '<option value="' . $page->ID . '"';
							if ( in_array( $page->ID, (array) $meta ) ) echo ' selected="selected"';
							echo '>' . $page->post_title . '</option>';
						}
						echo '</select></td>';
						break;
				}
				echo '</tr>';
			}
			echo '</table>';
		}
	}
}

if( ! function_exists( 'themeplate_save_meta_box' ) ) {
	function themeplate_save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( ! isset( $_POST['themeplate_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['themeplate_meta_box_nonce'], basename( __FILE__ ) ) )
			return;

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		foreach( $_POST['themeplate_meta'] as $key => $val ) {
			update_post_meta( $post_id, $key, $val );
		}
	}
	add_action( 'save_post', 'themeplate_save_meta_box' );
}
