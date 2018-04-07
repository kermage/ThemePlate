<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field {

	public static function input( $field ) {

		echo '<input type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" />';

	}


	public static function textarea( $field ) {

		echo '<textarea name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" rows="4">' . esc_textarea( $field['value'] ) . '</textarea>';

	}


	public static function select( $field ) {

		$seq = ThemePlate_Helpers::is_sequential( $field['options'] );
		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" />';
		echo '<select' . ( 'select2' === $field['type'] ? ' class="themeplate-select2"' : '' ) . ' name="' . esc_attr( $field['name'] ) . ( $field['multiple'] ? '[]' : '' ) . '" id="' . esc_attr( $field['id'] ) . '"' . ( $field['multiple'] ? ' multiple="multiple"' : '' ) . ( $field['none'] ? ' data-none="true"' : '' ) . '>';
		if ( 'select2' === $field['type'] && ! $field['value'] ) {
			echo '<option></options>';
		} elseif ( 'select2' !== $field['type'] && ( ( $field['none'] && $field['value'] ) || ( ! $field['multiple'] && ! $field['value'] ) ) ) {
			echo '<option value="0"' . ( $field['none'] && $field['value'] ? '' : ' disabled hidden' ) . ( $field['value'] ? '>' . __( '&mdash; None &mdash;' ) : ' selected>' . __( '&mdash; Select &mdash;' ) ) . '</option>';
		}
		if ( 'select2' === $field['type'] && $field['multiple'] && $field['value'] ) {
			$ordered = array();
			$values  = array_keys( $field['options'] );
			foreach ( (array) $field['value'] as $value ) {
				$value = ( $seq ? (int) $value - 1 : $value );
				if ( ! in_array( strval( $value ), array_map( 'strval', $values ), true ) ) {
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
			if ( in_array( strval( $value ), (array) $field['value'], true ) ) {
				echo ' selected="selected"';
			}
			echo '>' . $option . '</option>';
		}
		echo '</select>';

	}


	public static function radio( $field, $list = false ) {

		$seq = ThemePlate_Helpers::is_sequential( $field['options'] );
		if ( ! empty( $field['options'] ) ) {
			echo '<fieldset id="' . esc_attr( $field['id'] ) . '">';
			foreach ( $field['options'] as $value => $option ) {
				$value = ( $seq ? $value + 1 : $value );
				echo '<' . ( $list ? 'p' : 'span' ) . '>';
				echo '<label><input type="radio" name="' . esc_attr( $field['name'] ) . '" value="' . $value . '"' . checked( $field['value'], $value, false ) . ' />' . $option . '</label>';
				echo '</' . ( $list ? 'p' : 'span' ) . '>';
			}
			echo '</fieldset>';
		}

	}


	public static function checkbox( $field, $list = false ) {

		$seq = ThemePlate_Helpers::is_sequential( $field['options'] );
		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" />';
		if ( ! empty( $field['options'] ) ) {
			echo '<fieldset id="' . esc_attr( $field['id'] ) . '">';
			foreach ( $field['options'] as $value => $option ) {
				$value = ( $seq ? $value + 1 : $value );
				echo '<' . ( $list ? 'p' : 'span' ) . '>';
				echo '<label><input type="checkbox" name="' . esc_attr( $field['name'] ) . '[]" value="' . $value . '"';
				if ( in_array( strval( $value ), (array) $field['value'], true ) ) {
					echo ' checked="checked"';
				}
				echo ' />' . $option . '</label>';
				echo '</' . ( $list ? 'p' : 'span' ) . '>';
			}
			echo '</fieldset>';
		} else {
			echo '<input type="checkbox" id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" value="1"' . checked( $field['value'], 1, false ) . ' />';
		}

	}


	public static function color( $field ) {

		echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="themeplate-color-picker" value="' . esc_attr( $field['value'] ) . '"' . ( $field['default'] ? ' data-default-color="' . esc_attr( $field['default'] ) . '"' : '' );
		if ( ! empty( $field['options'] ) ) {
			$values = json_encode( $field['options'] );
			echo ' data-palettes="' . esc_attr( $values ) . '"';
		}
		echo ' />';

	}


	public static function file( $field ) {

		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" />';
		echo '<div id="' . esc_attr( $field['id'] ) . '" class="themeplate-file' . ( $field['multiple'] ? ' multiple' : ' single' ) . '">';
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
				echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . ( $field['multiple'] ? '[]' : '' ) . '" value="' . $file . '" />';
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

		echo '<input type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '"';
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
			default:
			case 'post':
			case 'page':
				$items    = get_posts( array( 'post_type' => $field['options'], 'numberposts' => -1 ) );
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
		$values = array_column( $items, $val_prop );
		$labels = array_column( $items, $lbl_prop );

		$field['type']    = 'select2';
		$field['options'] = array_combine( $values, $labels );

		self::select( $field );

	}


	public static function html( $field ) {

		echo $field['default'];

	}

}
