<?php

/**
 * Setup meta fields
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Fields {

	private $collection;

	private $defaults = array(
		'type'       => 'text',
		'options'    => array(),
		'multiple'   => false,
		'none'       => false,
		'default'    => '',
		'style'      => '',
		'repeatable' => false,
	);


	public function __construct( $collection ) {

		if ( ! is_array( $collection ) || empty( $collection ) ) {
			return false;
		}

		$this->collection = $this->filter( $collection );

	}


	private function filter( $fields ) {

		$processed = array();

		foreach ( $fields as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field = ThemePlate_Helpers::fool_proof( $this->defaults, $field );
			$field = ThemePlate_Helpers::normalize_options( $field );

			if ( 'group' === $field['type'] ) {
				if ( array_key_exists( 'fields', $field ) && ! empty( $field['fields'] ) ) {
					$field['fields'] = $this->filter( $field['fields'] );
				} else {
					continue;
				}
			}

			$processed[ $id ] = $field;
		}

		return $processed;

	}


	public function setup( $metabox_id = '', $object_type = 'post', $object_id = 0 ) {

		$fields = $this->collection;

		foreach ( $fields as $id => $field ) {
			if ( 'options' === $object_type ) {
				$field['id'] = $metabox_id . '_' . $id;

				$options = get_option( $object_id );
				$stored  = isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : '';
				$key     = $object_id;
			} else {
				$field['id'] = ThemePlate()->key . '_' . $metabox_id . '_' . $id;

				$stored = get_metadata( $object_type, $object_id, $field['id'], ! $field['repeatable'] );
				$key    = ThemePlate()->key;
			}

			if ( ! empty( $field['name'] ) ) {
				_deprecated_argument( sprintf( 'Field <b>%1$s</b>', $field['id'] ), '3.0.0', 'Use key <b>title</b> to field config instead of <b>name</b>.' );

				$field['title'] = $field['name'];
			}

			if ( ! empty( $field['desc'] ) ) {
				_deprecated_argument( sprintf( 'Field <b>%1$s</b>', $field['id'] ), '3.0.0', 'Use key <b>description</b> to field config instead of <b>desc</b>.' );

				$field['description'] = $field['desc'];
			}

			if ( ! empty( $field['std'] ) ) {
				_deprecated_argument( sprintf( 'Field <b>%1$s</b>', $field['id'] ), '3.0.0', 'Use key <b>default</b> to field config instead of <b>std</b>.' );

				$field['default'] = $field['std'];
			}

			$value = $stored ? $stored : $field['default'];
			$name  = $key . '[' . $field['id'] . ']';

			$this->layout( $field, $value, $name );
		}

	}


	private function layout( $field, $value, $name ) {

		echo '<div class="field-wrapper type-' . $field['type'] . ' ' . $field['style'] . '">';
			ThemePlate_Helpers::render_options( $field );

			if ( ! empty( $field['title'] ) || ! empty( $field['description'] ) ) {
				echo '<div class="field-label">';
					echo ! empty( $field['title'] ) ? '<label class="label" for="' . $field['id'] . '">' . $field['title'] . '</label>' : '';
					echo ! empty( $field['description'] ) ? '<p class="description">' . $field['description'] . '</p>' : '';
				echo '</div>';
			}

			echo '<div class="field-input' . ( $field['repeatable'] ? ' repeatable' : '' ) . '">';
				if ( ! $field['repeatable'] ) {
					$field['value'] = $value;
					$field['name']  = $name;

					$this->render( $field );
				} else {
					$base_id = $field['id'];

					foreach ( (array) $value as $i => $val ) {
						$field['value'] = $val;
						$field['id']    = $base_id . '_' . $i;
						$field['name']  = $name . '[' . $i . ']';

						echo '<div class="themeplate-clone">';
							echo '<div class="themeplate-handle"></div>';
							$this->render( $field );
							echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
						echo '</div>';
					}

					$field['value'] = $field['default'];
					$field['id']    = $base_id . '_i-x';
					$field['name']  = $name . '[i-x]';

					echo '<div class="themeplate-clone hidden">';
						echo '<div class="themeplate-handle"></div>';
						$this->render( $field );
						echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
					echo '</div>';
					echo '<input type="button" class="button clone-add" value="Add Field" />';
				}
			echo '</div>';
		echo '</div>';

	}


	private function render( $field ) {

		$list = false;

		switch ( $field['type'] ) {
			default:
			case 'text':
			case 'date':
			case 'time':
			case 'email':
			case 'url':
				ThemePlate_Field::input( $field );
				break;

			case 'textarea':
				ThemePlate_Field::textarea( $field );
				break;

			case 'select':
			case 'select2':
				ThemePlate_Field::select( $field );
				break;

			case 'radiolist':
				$list = true;
			case 'radio':
				ThemePlate_Field::radio( $field, $list );
				break;

			case 'checklist':
				$list = true;
			case 'checkbox':
				ThemePlate_Field::checkbox( $field, $list );
				break;

			case 'color':
				ThemePlate_Field::color( $field );
				break;

			case 'file':
				ThemePlate_Field::file( $field );
				break;

			case 'number':
			case 'range':
				ThemePlate_Field::number( $field );
				break;

			case 'editor':
				ThemePlate_Field::editor( $field );
				break;

			case 'post':
			case 'page':
			case 'user':
			case 'term':
				ThemePlate_Field::object( $field );
				break;

			case 'group':
				foreach ( $field['fields'] as $id => $sub ) {
					$sub['id'] = $field['id'] . '_' . $id;

					$stored = isset( $field['value'][ $id ] ) ? $field['value'][ $id ] : '';
					$value  = $stored ? $stored : $sub['std'];
					$name   = $field['name'] . '[' . $id . ']';

					$this->layout( $sub, $value, $name );
				}
				break;

			case 'html':
				ThemePlate_Field::html( $field );
				break;
		}

	}


	public function get_collection() {

		return $this->collection;

	}

}
