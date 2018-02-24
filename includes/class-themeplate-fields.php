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


	private function __construct() {


	}


	public function render( $field ) {

		if ( ! is_array( $field ) || empty( $field ) ) {
			return false;
		}

		$field_name = isset( $field['prefix'] ) ? $field['prefix'] : ThemePlate()->key;
		$field_name .= '[' . $field['id'] . ']';

		$field = array_merge( array( 'multiple' => false, 'none' => false ), $field );

		$list = false;
		$seq = false;

		if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
			if( array_keys( $field['options'] ) === range( 0, count( $field['options'] ) - 1 ) ) {
				$seq = true;
			}
		}

		switch ( $field['type'] ) {
			default:
			case 'text':
				echo '<input type="text" name="' . $field_name . '" id="' . $field['id'] . '" value="' . esc_attr( $field['value'] ) . '" />';
				break;

			case 'textarea' :
				echo '<textarea name="' . $field_name . '" id="' . $field['id'] . '" rows="4">' . esc_textarea( $field['value'] ) . '</textarea>';
				break;

			case 'select' :
				echo '<select class="themeplate-select2" name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="0">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				} elseif ( $field['none'] && $field['value'] ) {
					echo '<option value="0">' . __( '&mdash; None &mdash;' ) . '</option>';
				}
				if ( $field['multiple'] && $field['value'] ) {
					$ordered = array();
					foreach ( (array) $field['value'] as $value ) {
						$value = ( $seq ? $value - 1 : $value );
						$ordered[$value] = $field['options'][$value];
						unset( $field['options'][$value] );
					}
					$field['options'] = $ordered + $field['options'];
				}
				foreach ( $field['options'] as $value => $option ) {
					$value = ( $seq ? $value + 1 : $value );
					echo '<option value="' . $value . '"';
					if ( in_array( $value, (array) $field['value'] ) ) {
						echo ' selected="selected"';
					}
					echo '>' . $option . '</option>';
				}
				echo '</select>';
				break;

			case 'radiolist' :
				$list = true;
			case 'radio' :
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					foreach ( $field['options'] as $value => $option ) {
						$value = ( $seq ? $value + 1 : $value );
						echo '<label><input type="radio" name="' . $field_name . '" value="' . $value . '"' . checked( $field['value'], $value, false ) . ' />' . $option . '</label>';
						echo ( $list ? '<br>' : '' );
					}
				}
				break;

			case 'checklist' :
				$list = true;
			case 'checkbox' :
				echo '<input type="hidden" name="' . $field_name . '" />';
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					foreach ( $field['options'] as $value => $option ) {
						$value = ( $seq ? $value + 1 : $value );
						echo '<label><input type="checkbox" name="' . $field_name . '[]" value="' . $value . '"';
						if ( in_array( $value, (array) $field['value'] ) ) {
							echo ' checked="checked"';
						}
						echo ' />' . $option . '</label>';
						echo ( $list ? '<br>' : '' );
					}
				} else {
					echo '<input type="checkbox" id="' . $field['id'] . '" name="' . $field_name . '" value="1"' . checked( $field['value'], 1, false ) . ' />';
				}
				break;

			case 'color':
				echo '<input type="text" name="' . $field_name . '" id="' . $field['id'] . '" class="themeplate-color-picker" value="' . $field['value'] . '"' . ( isset( $field['std'] ) ? ' data-default-color="' . $field['std'] . '"' : '' ) . ' />';
				break;

			case 'file':
				echo '<div id="' . $field['id'] . '" class="themeplate-file' . ( $field['multiple'] ? ' multiple' : ' single' ) . '" data-key="' . ( isset( $field['prefix'] ) ? $field['prefix'] : ThemePlate()->key ) . '">';
				echo '<div class="preview-holder">';
				if ( $field['value'] ) {
					foreach ( (array) $field['value'] as $file ) {
						$name = basename( get_attached_file( $file ) );
						$info = wp_check_filetype( $name );
						$type = wp_ext2type( $info['ext'] );
						$preview = ( $type == 'image' ? wp_get_attachment_url( $file ) : includes_url( '/images/media/' ) . $type . '.png' );
						echo '<div class="attachment"><div class="attachment-preview landscape"><div class="thumbnail">';
						echo '<div class="centered"><img src="' . $preview . '"/></div>';
						echo '<div class="filename"><div>' . $name . '</div></div>';
						echo '</div></div>';
						echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
						echo '<input type="hidden" name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" value="' . $file . '" />';
						echo '</div>';
					}
				}
				if ( $field['multiple'] ) {
					echo '</div>';
					echo '<input type="button" class="button attachment-add" value="Add" />';
					echo '<input type="button" class="button attachments-clear' . ( empty( $field['value'][0] ) ? ' hidden' : '' ) . '" value="Clear" />';
				} else {
					echo '<div class="attachment placeholder">';
					echo '<input type="button" class="button attachment-add"' . ( $field['value'] ? ' hidden' : '' ) . ' value="Select" />';
					echo '</div>';
					echo '</div>';
				}
				echo '</div>';
				break;

			case 'date':
				echo '<input type="date" name="' . $field_name . '" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'time':
				echo '<input type="time" name="' . $field_name . '" id="' . $field['id'] . '" value="' . $field['value'] . '" />';
				break;

			case 'number':
				echo '<input type="number" name="' . $field_name . '" id="' . $field['id'] . '" value="' . $field['value'] . '"';
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					foreach ( $field['options'] as $option => $value ) {
						echo $option . '="' . $value . '"';
					}
				}
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
				echo '<select class="themeplate-select2" name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="0">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				} elseif ( $field['none'] && $field['value'] ) {
					echo '<option value="0">' . __( '&mdash; None &mdash;' ) . '</option>';
				}
				if ( $list == 'post' ) {
					$pages = get_posts( array( 'post_type' => $field['options'] ) );
				} else {
					$pages = get_pages( array( 'post_type' => $field['options'] ) );
				}
				if ( $field['multiple'] && $field['value'] ) {
					$ordered = array();
					foreach ( (array) $field['value'] as $value ) {
						$key = array_search( $value, array_column( $pages, 'ID' ) );
						$ordered[] = $pages[$key];
						unset( $pages[$key] );
						$pages = array_values( $pages );
					}
					$pages = array_merge( $ordered, $pages );
				}
				foreach ( $pages as $page ) {
					echo '<option value="' . $page->ID . '"';
					if ( in_array( $page->ID, (array) $field['value'] ) ) {
						echo ' selected="selected"';
					}
					echo '>' . $page->post_title . '</option>';
				}
				echo '</select>';
				break;

			case 'user':
				echo '<select class="themeplate-select2" name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="0">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				} elseif ( $field['none'] && $field['value'] ) {
					echo '<option value="0">' . __( '&mdash; None &mdash;' ) . '</option>';
				}
				$users = get_users( array( 'role' => $field['options'] ) );
				if ( $field['multiple'] && $field['value'] ) {
					$ordered = array();
					foreach ( (array) $field['value'] as $value ) {
						$key = array_search( $value, array_column( $users, 'ID' ) );
						$ordered[] = $users[$key];
						unset( $users[$key] );
						$users = array_values( $users );
					}
					$users = array_merge( $ordered, $users );
				}
				foreach ( $users as $user ) {
					echo '<option value="' . $user->ID . '"';
					if ( in_array( $user->ID, (array) $field['value'] ) ) {
						echo ' selected="selected"';
					}
					echo '>' . $user->display_name . '</option>';
				}
				echo '</select>';
				break;

			case 'term':
				echo '<select class="themeplate-select2" name="' . $field_name . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['multiple'] ) {
					echo '<option' . ( $field['none'] ? ' value="0">' : ' selected="selected" disabled="disabled" hidden>' ) . ( $field['value'] ? __( '&mdash; None &mdash;' ) : __( '&mdash; Select &mdash;' ) ) . '</option>';
				} elseif ( $field['none'] && $field['value'] ) {
					echo '<option value="0">' . __( '&mdash; None &mdash;' ) . '</option>';
				}
				$terms = get_terms( array( 'taxonomy' => $field['options'] ) );
				if ( $field['multiple'] && $field['value'] ) {
					$ordered = array();
					foreach ( (array) $field['value'] as $value ) {
						$key = array_search( $value, array_column( $terms, 'term_id' ) );
						$ordered[] = $terms[$key];
						unset( $terms[$key] );
						$terms = array_values( $terms );
					}
					$terms = array_merge( $ordered, $terms );
				}
				foreach ( $terms as $term ) {
					echo '<option value="' . $term->term_id . '"';
					if ( in_array( $term->term_id, (array) $field['value'] ) ) {
						echo ' selected="selected"';
					}
					echo '>' . $term->name . '</option>';
				}
				echo '</select>';
				break;
		}

	}

}
