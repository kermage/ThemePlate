<?php

/**
 * Helper functions
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Helper_Field {

	public static function render( $field ) {

		$list = false;

		switch ( $field['type'] ) {
			default:
			case 'text':
			case 'date':
			case 'time':
			case 'email':
			case 'url':
				ThemePlate_Field_Input::render( $field );
				break;

			case 'textarea':
				ThemePlate_Field_Textarea::render( $field );
				break;

			case 'select':
			case 'select2':
				ThemePlate_Field_Select::render( $field );
				break;

			case 'radiolist':
				$list = true;
			case 'radio':
				ThemePlate_Field_Radio::render( $field, $list );
				break;

			case 'checklist':
				$list = true;
			case 'checkbox':
				ThemePlate_Field_Checkbox::render( $field, $list );
				break;

			case 'color':
				ThemePlate_Field_Color::render( $field );
				break;

			case 'file':
				ThemePlate_Field_File::render( $field );
				break;

			case 'number':
			case 'range':
				ThemePlate_Field_Number::render( $field );
				break;

			case 'editor':
				ThemePlate_Field_Editor::render( $field );
				break;

			case 'post':
			case 'page':
			case 'user':
			case 'term':
				ThemePlate_Field_Object::render( $field );
				break;

			case 'html':
				ThemePlate_Field_Html::render( $field );
				break;
		}

	}


	public static function deprecate_check( $field ) {

		if ( ! empty( $field['name'] ) ) {
			if ( ! ThemePlate()->stalled ) {
				_deprecated_argument( sprintf( 'Field <b>%1$s</b>', $field['id'] ), '3.0.0', 'Use key <b>title</b> to field config instead of <b>name</b>.' );
			}

			$field['title'] = $field['name'];
		}

		if ( ! empty( $field['desc'] ) ) {
			if ( ! ThemePlate()->stalled ) {
				_deprecated_argument( sprintf( 'Field <b>%1$s</b>', $field['id'] ), '3.0.0', 'Use key <b>description</b> to field config instead of <b>desc</b>.' );
			}

			$field['description'] = $field['desc'];
		}

		if ( ! empty( $field['std'] ) ) {
			if ( ! ThemePlate()->stalled ) {
				_deprecated_argument( sprintf( 'Field <b>%1$s</b>', $field['id'] ), '3.0.0', 'Use key <b>default</b> to field config instead of <b>std</b>.' );
			}

			$field['default'] = $field['std'];
		}

		return $field;

	}

}
