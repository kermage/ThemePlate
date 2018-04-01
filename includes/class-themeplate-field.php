<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field {

	public static function input( $field ) {

		echo '<input type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" value="' . esc_attr( $field['value'] ) . '" />';

	}


	public static function textarea( $field ) {

		echo '<textarea name="' . $field['name'] . '" id="' . $field['id'] . '" rows="4">' . esc_textarea( $field['value'] ) . '</textarea>';

	}


	public static function select( $field, $seq ) {

		echo '<input type="hidden" name="' . $field['name'] . '" />';
		echo '<select' . ( 'select2' === $field['type'] ? ' class="themeplate-select2"' : '' ) . ' name="' . $field['name'] . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '"' . ( $field['multiple'] ? ' multiple="multiple"' : '' ) . ( $field['none'] ? ' data-none="true"' : '' ) . '>';
		if ( 'select2' === $field['type'] && ! $field['value'] ) {
			echo '<option></options>';
		} elseif ( 'select2' !== $field['type'] && ( ( $field['none'] && $field['value'] ) || ( ! $field['multiple'] && ! $field['value'] ) ) ) {
			echo '<option value="0"' . ( $field['none'] && $field['value'] ? '' : ' disabled hidden' ) . ( $field['value'] ? '>' . __( '&mdash; None &mdash;' ) : ' selected>' . __( '&mdash; Select &mdash;' ) ) . '</option>';
		}
		if ( 'select2' === $field['type'] && $field['multiple'] && $field['value'] ) {
			$ordered = array();
			foreach ( (array) $field['value'] as $value ) {
				$value = ( $seq ? (int) $value - 1 : $value );
				if ( ! in_array( $value, array_keys( $field['options'] ), true ) ) {
					continue;
				}
				$ordered[ $value ] = $field['options'][ $value ];
				unset( $field['options'][ $value ] );
			}
			$field['options'] = $ordered + $field['options'];
		}
		foreach ( $field['options'] as $value => $option ) {
			$value = ( $seq ? $value + 1 : $value );
			echo '<option value="' . $value . '"';
			if ( in_array( $value, (array) $field['value'], true ) ) {
				echo ' selected="selected"';
			}
			echo '>' . $option . '</option>';
		}
		echo '</select>';

	}


	public static function radio( $field, $seq, $list ) {

		if ( ! empty( $field['options'] ) ) {
			echo '<fieldset id="' . $field['id'] . '">';
			foreach ( $field['options'] as $value => $option ) {
				$value = ( $seq ? $value + 1 : $value );
				echo '<' . ( $list ? 'p' : 'span' ) . '>';
				echo '<label><input type="radio" name="' . $field['name'] . '" value="' . $value . '"' . checked( $field['value'], $value, false ) . ' />' . $option . '</label>';
				echo '</' . ( $list ? 'p' : 'span' ) . '>';
			}
			echo '</fieldset>';
		}

	}


	public static function checkbox( $field, $seq, $list ) {

		echo '<input type="hidden" name="' . $field['name'] . '" />';
		if ( ! empty( $field['options'] ) ) {
			echo '<fieldset id="' . $field['id'] . '">';
			foreach ( $field['options'] as $value => $option ) {
				$value = ( $seq ? $value + 1 : $value );
				echo '<' . ( $list ? 'p' : 'span' ) . '>';
				echo '<label><input type="checkbox" name="' . $field['name'] . '[]" value="' . $value . '"';
				if ( in_array( $value, (array) $field['value'], true ) ) {
					echo ' checked="checked"';
				}
				echo ' />' . $option . '</label>';
				echo '</' . ( $list ? 'p' : 'span' ) . '>';
			}
			echo '</fieldset>';
		} else {
			echo '<input type="checkbox" id="' . $field['id'] . '" name="' . $field['name'] . '" value="1"' . checked( $field['value'], 1, false ) . ' />';
		}

	}


	public static function color( $field ) {

		echo '<input type="text" name="' . $field['name'] . '" id="' . $field['id'] . '" class="themeplate-color-picker" value="' . $field['value'] . '"' . ( $field['std'] ? ' data-default-color="' . $field['std'] . '"' : '' );
		if ( ! empty( $field['options'] ) ) {
			$values = json_encode( $field['options'] );
			echo ' data-palettes="' . esc_attr( $values ) . '"';
		}
		echo ' />';

	}


	public static function file( $field ) {

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
				$name    = basename( get_attached_file( $file ) );
				$info    = wp_check_filetype( $name );
				$type    = wp_ext2type( $info['ext'] );
				$preview = ( 'image' === $type ? wp_get_attachment_url( $file ) : includes_url( '/images/media/' ) . $type . '.png' );
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

	}


	public static function number( $field ) {

		echo '<input type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" value="' . $field['value'] . '"';
		if ( ! empty( $field['options'] ) ) {
			foreach ( $field['options'] as $option => $value ) {
				echo $option . '="' . $value . '"';
			}
		}
		if ( 'range' === $field['type'] ) {
			echo ' oninput="this.nextElementSibling.innerHTML=this.value" />';
			echo '<span>' . $field['value'] . '</span>';
		} else {
			echo ' />';
		}

	}


	public static function editor( $field ) {

		$settings = array(
			'textarea_name' => $field['name'],
			'textarea_rows' => 10,
		);
		wp_editor( $field['value'], $field['id'], $settings );

	}


	public static function object( $field ) {

		switch ( $field['type'] ) {
			case 'post':
				$items    = get_posts( array( 'post_type' => $field['options'], 'numberposts' => -1 ) );
				$val_prop = 'ID';
				$lbl_prop = 'post_title';
				break;
			case 'page':
				$items    = get_pages( array( 'post_type' => $field['options'] ) );
				$val_prop = 'ID';
				$lbl_prop = 'post_title';
				break;
			case 'user':
				$items    = get_users( array( 'role' => $field['options'] ) );
				$val_prop = 'ID';
				$lbl_prop = 'display_name';
				break;
			case 'term':
				$items    = get_terms( array( 'taxonomy' => $field['options'] ) );
				$val_prop = 'term_id';
				$lbl_prop = 'name';
				break;
		}
		echo '<input type="hidden" name="' . $field['name'] . '" />';
		echo '<select class="themeplate-select2" name="' . $field['name'] . ( $field['multiple'] ? '[]' : '' ) . '" id="' . $field['id'] . '"' . ( $field['multiple'] ? ' multiple="multiple"' : '' ) . ( $field['none'] ? ' data-none="true"' : '' ) . '>';
		if ( ! $field['value'] ) {
			echo '<option></options>';
		} elseif ( $field['none'] && $field['value'] ) {
			echo '<option value="0">' . __( '&mdash; None &mdash;' ) . '</option>';
		}
		if ( $field['multiple'] && $field['value'] ) {
			$ordered = array();
			foreach ( (array) $field['value'] as $value ) {
				$key = array_search( $value, array_column( $items, $val_prop ) );
				if ( false === $key ) {
					continue;
				}
				$ordered[] = $items[ $key ];
				unset( $items[ $key ] );
				$items = array_values( $items );
			}
			$items = array_merge( $ordered, $items );
		}
		foreach ( $items as $item ) {
			echo '<option value="' . $item->{$val_prop} . '"';
			if ( in_array( $item->{$val_prop}, (array) $field['value'], true ) ) {
				echo ' selected="selected"';
			}
			echo '>' . $item->{$lbl_prop} . '</option>';
		}
		echo '</select>';

	}


	public static function group( $field ) {

		foreach ( $field['fields'] as $id => $sub ) {
			$sub['id'] = $field['id'] . '_' . $id;

			$stored = isset( $field['value'][ $id ] ) ? $field['value'][ $id ] : '';
			$value  = $stored ? $stored : $sub['std'];

			echo '<div class="field-wrapper type-' . $sub['type'] . ' ' . $sub['style'] . '">';
				ThemePlate_Helpers::render_options( $sub );

				if ( ! empty( $sub['name'] ) || ! empty( $sub['desc'] ) ) {
					echo '<div class="field-label">';
						echo ! empty( $sub['name'] ) ? '<label class="label" for="' . $sub['id'] . '">' . $sub['name'] . '</label>' : '';
						echo ! empty( $sub['desc'] ) ? '<p class="description">' . $sub['desc'] . '</p>' : '';
					echo '</div>';
				}

				echo '<div class="field-input' . ( $sub['repeatable'] ? ' repeatable' : '' ) . '">';
					$base_name = $field['name'] . '[' . $id . ']';

					if ( ! $sub['repeatable'] ) {
						$sub['value'] = $value;
						$sub['name']  = $base_name;

						ThemePlate_Fields::render( $sub );
					} else {
						$base_id = $sub['id'];

						foreach ( (array) $value as $i => $val ) {
							$sub['value'] = $val;
							$sub['id']    = $base_id . '_' . $i;
							$sub['name']  = $base_name . '[' . $i . ']';

							echo '<div class="themeplate-clone">';
								echo '<div class="themeplate-handle"></div>';
								ThemePlate_Fields::render( $sub );
								echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
							echo '</div>';
						}

						$sub['value'] = $sub['std'];
						$sub['id']    = $base_id . '_i-x';
						$sub['name']  = $base_name . '[i-x]';

						echo '<div class="themeplate-clone hidden">';
							echo '<div class="themeplate-handle"></div>';
							ThemePlate_Fields::render( $sub );
							echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
						echo '</div>';
						echo '<input type="button" class="button clone-add" value="Add Field" />';
					}
				echo '</div>';
			echo '</div>';
		}

	}


	public static function html( $field ) {

		echo $field['std'];

	}

}
