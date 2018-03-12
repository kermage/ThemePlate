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
			case 'date':
			case 'time':
			case 'email':
			case 'url':
				echo '<input type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" value="' . esc_attr( $field['value'] ) . '" />';
				break;


			case 'textarea' :
				echo '<textarea name="' . $field['name'] . '" id="' . $field['id'] . '" rows="4">' . esc_textarea( $field['value'] ) . '</textarea>';
				break;


			case 'select' :
				echo '<input type="hidden" name="' . $field['name'] . '" />';
				echo '<select class="themeplate-select2" name="' . $field['name'] . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['value'] ) {
					echo '<option></options>';
				} elseif ( $field['none'] && $field['value'] ) {
					echo '<option value="0">' . __( '&mdash; None &mdash;' ) . '</option>';
				}
				if ( $field['multiple'] && $field['value'] ) {
					$ordered = array();
					foreach ( (array) $field['value'] as $value ) {
						$value = ( $seq ? (int) $value - 1 : $value );
						if ( ! in_array( $value, array_keys( $field['options'] ) ) ) {
							continue;
						}
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
					echo '<fieldset id="' . $field['id'] . '">';
					foreach ( $field['options'] as $value => $option ) {
						$value = ( $seq ? $value + 1 : $value );
						echo '<' . ( $list ? 'p' : 'span' ) . '>';
						echo '<label><input type="radio" name="' . $field['name'] . '" value="' . $value . '"' . checked( $field['value'], $value, false ) . ' />' . $option . '</label>';
						echo '</' . ( $list ? 'p' : 'span' ) . '>';
					}
					echo '</fieldset>';
				}
				break;

			case 'checklist' :
				$list = true;
			case 'checkbox' :
				echo '<input type="hidden" name="' . $field['name'] . '" />';
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					echo '<fieldset id="' . $field['id'] . '">';
					foreach ( $field['options'] as $value => $option ) {
						$value = ( $seq ? $value + 1 : $value );
						echo '<' . ( $list ? 'p' : 'span' ) . '>';
						echo '<label><input type="checkbox" name="' . $field['name'] . '[]" value="' . $value . '"';
						if ( in_array( $value, (array) $field['value'] ) ) {
							echo ' checked="checked"';
						}
						echo ' />' . $option . '</label>';
						echo '</' . ( $list ? 'p' : 'span' ) . '>';
					}
					echo '</fieldset>';
				} else {
					echo '<input type="checkbox" id="' . $field['id'] . '" name="' . $field['name'] . '" value="1"' . checked( $field['value'], 1, false ) . ' />';
				}
				break;


			case 'color':
				echo '<input type="text" name="' . $field['name'] . '" id="' . $field['id'] . '" class="themeplate-color-picker" value="' . $field['value'] . '"' . ( isset( $field['std'] ) ? ' data-default-color="' . $field['std'] . '"' : '' ) . ' />';
				break;


			case 'file':
				echo '<input type="hidden" name="' . $field['name'] . '" />';
				echo '<div id="' . $field['id'] . '" class="themeplate-file' . ( $field['multiple'] ? ' multiple' : ' single' ) . '">';
				echo '<div class="preview-holder">';
				if ( ! $field['multiple'] ) {
					echo '<div class="attachment placeholder">';
					echo '<input type="button" class="button attachment-add' . ( $field['value'] ? ' hidden' : '' ) . '" value="Select" />';
					echo '</div>';
				}
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
						echo '<input type="hidden" name="' . $field['name'] . ( $field['multiple'] ? '[]' : '' ) . '" value="' . $file . '" />';
						echo '</div>';
					}
				}
				echo '</div>';
				if ( $field['multiple'] ) {
					echo '<input type="button" class="button attachment-add" value="Add" />';
					echo '<input type="button" class="button attachments-clear' . ( ! $field['value'] ? ' hidden' : '' ) . '" value="Clear" />';
				}
				echo '</div>';
				break;


			case 'number':
			case 'range':
				echo '<input type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" value="' . $field['value'] . '"';
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					foreach ( $field['options'] as $option => $value ) {
						echo $option . '="' . $value . '"';
					}
				}
				echo ' />';
				break;


			case 'editor':
				$settings = array(
					'textarea_name' => $field['name'],
					'textarea_rows' => 10
				);
				wp_editor( $field['value'], $field['id'], $settings );
				break;


			case 'post':
			case 'page':
			case 'user':
			case 'term':
				switch ( $field['type'] ) {
					case 'post':
						$items = get_posts( array( 'post_type' => $field['options'], 'numberposts' => -1 ) );
						$val_prop = 'ID';
						$lbl_prop = 'post_title';
						break;
					case 'page':
						$items = get_pages( array( 'post_type' => $field['options'] ) );
						$val_prop = 'ID';
						$lbl_prop = 'post_title';
						break;
					case 'user':
						$items = get_users( array( 'role' => $field['options'] ) );
						$val_prop = 'ID';
						$lbl_prop = 'display_name';
						break;
					case 'term':
						$items = get_terms( array( 'taxonomy' => $field['options'] ) );
						$val_prop = 'term_id';
						$lbl_prop = 'name';
						break;
				}
				echo '<input type="hidden" name="' . $field['name'] . '" />';
				echo '<select class="themeplate-select2" name="' . $field['name'] . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '" ' . ( $field['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				if ( ! $field['value'] ) {
					echo '<option></options>';
				} elseif ( $field['none'] && $field['value'] ) {
					echo '<option value="0">' . __( '&mdash; None &mdash;' ) . '</option>';
				}
				if ( $field['multiple'] && $field['value'] ) {
					$ordered = array();
					foreach ( (array) $field['value'] as $value ) {
						$key = array_search( $value, array_column( $items, $val_prop ) );
						if ( $key === false ) {
							continue;
						}
						$ordered[] = $items[$key];
						unset( $items[$key] );
						$items = array_values( $items );
					}
					$items = array_merge( $ordered, $items );
				}
				foreach ( $items as $item ) {
					echo '<option value="' . $item->{$val_prop} . '"';
					if ( in_array( $item->{$val_prop}, (array) $field['value'] ) ) {
						echo ' selected="selected"';
					}
					echo '>' . $item->{$lbl_prop} . '</option>';
				}
				echo '</select>';
				break;


			case 'group':
				foreach ( $field['fields'] as $id => $sub ) {
					$sub['id'] = $id;
					$sub['object'] = $field['object'];

					$key = $sub['id'];
					$title = $sub['name'];
					$name = $field['name'] . '[' . $key . ']';
					$default = isset( $sub['std'] ) ? $sub['std'] : '';
					$unique = isset( $sub['repeatable'] ) ? false : true;

					if ( $field['object']['type'] == 'post' ) {
						$options = get_post_meta( $field['object']['id'], $field['id'], $unique );
						$stored = isset( $options[$key] ) ? $options[$key] : '';
					} elseif ( $field['object']['type'] == 'term' ) {
						$options = $field['object']['id'] ? get_term_meta( $field['object']['id'], $field['id'], $unique ) : '';
						$stored = isset( $options[$key] ) ? $options[$key] : '';
					} elseif ( $field['object']['type'] == 'user' ) {
						$options = $field['object']['id'] ? get_user_meta( $field['object']['id'], $field['id'], $unique ) : '';
						$stored = isset( $options[$key] ) ? $options[$key] : '';
					} elseif ( $field['object']['type'] == 'option' ) {
						$options = get_option( $field['object']['id'] );
						$stored = isset( $options[$key] ) ? $options[$key] : '';
					}

					$value = $stored ? $stored : $default;

					$sub['type'] = isset( $sub['type'] ) ? $sub['type'] : 'text';
					$sub['style'] = isset( $sub['style'] ) ? $sub['style'] : '';

					echo '<div class="field-wrapper type-' . $sub['type'] . ' ' . $sub['style'] . '">';
						echo '<div class="field-label">';
							echo '<label class="label" for="' . $key . '">' . $title . '</label>';
							echo ! empty( $sub['desc'] ) ? '<p class="description">' . $sub['desc'] . '</p>' : '';
						echo '</div>';
						echo '<div class="field-input' . ( $unique ? '' : ' repeatable' ) . '">';
							if ( $unique ) {
								$sub['value'] = $value;
								$sub['name'] = $name;

								ThemePlate_Fields::instance()->render( $sub );
							} else {
								foreach ( (array) $value as $i => $val ) {
									$sub['value'] = $val;
									$sub['id'] = $key . '_i-' . $i;
									$sub['name'] =  $name . '[i-' . $i . ']';

									echo '<div class="themeplate-clone">';
										echo '<div class="themeplate-handle"></div>';
										ThemePlate_Fields::instance()->render( $sub );
										echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
									echo '</div>';
								}

								$sub['value'] = $default;
								$sub['id'] = $key . '_i-x';
								$sub['name'] =  $name . '[i-x]';

								echo '<div class="themeplate-clone hidden">';
									echo '<div class="themeplate-handle"></div>';
									ThemePlate_Fields::instance()->render( $sub );
									echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
								echo '</div>';
								echo '<input type="button" class="button clone-add" value="Add Field" />';
							}
						echo '</div>';
					echo '</div>';
				}
				break;
		}

	}

}
