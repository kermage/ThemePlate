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
		'std'        => '',
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

			$processed[ $id ] = $field;
		}

		return $processed;

	}


	public function setup( $metabox_id = '', $object_type = 'post', $object_id = 0 ) {

		$fields = $this->collection;

		foreach ( $fields as $id => $field ) {
			if ( 'options' === $object_type ) {
				$field['id'] = $meta_box['id'] . '_' . $id;

				$options = get_option( $object_id );
				$stored  = isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : '';
				$key     = $object_id;
			} else {
				$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;

				$stored = get_metadata( $object_type, $object_id, $field['id'], ! $field['repeatable'] );
				$key    = ThemePlate()->key;
			}

			$value = $stored ? $stored : $field['std'];

			$this->layout( $field, $key, $value );
		}

	}


	private function layout( $field, $key, $value ) {

		echo '<div class="field-wrapper type-' . $field['type'] . ' ' . $field['style'] . '">';
			ThemePlate_Helpers::render_options( $field );

			if ( ! empty( $field['name'] ) || ! empty( $field['desc'] ) ) {
				echo '<div class="field-label">';
					echo ! empty( $field['name'] ) ? '<label class="label" for="' . $field['id'] . '">' . $field['name'] . '</label>' : '';
					echo ! empty( $field['desc'] ) ? '<p class="description">' . $field['desc'] . '</p>' : '';
				echo '</div>';
			}

			echo '<div class="field-input' . ( $field['repeatable'] ? ' repeatable' : '' ) . '">';
				$base_name = $key . '[' . $field['id'] . ']';

				if ( ! $field['repeatable'] ) {
					$field['value'] = $value;
					$field['name']  = $base_name;

					$this->render( $field );
				} else {
					$base_id = $field['id'];

					foreach ( (array) $value as $i => $val ) {
						$field['value'] = $val;
						$field['id']    = $base_id . '_' . $i;
						$field['name']  = $base_name . '[' . $i . ']';

						echo '<div class="themeplate-clone">';
							echo '<div class="themeplate-handle"></div>';
							$this->render( $field );
							echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
						echo '</div>';
					}

					$field['value'] = $field['std'];
					$field['id']    = $base_id . '_i-x';
					$field['name']  = $base_name . '[i-x]';

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


	public static function render( $field ) {

		$list = false;
		$seq  = ThemePlate_Helpers::is_sequential( $field['options'] );

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
				ThemePlate_Field::select( $field, $seq );
				break;

			case 'radiolist':
				$list = true;
			case 'radio':
				ThemePlate_Field::radio( $field, $seq, $list );
				break;

			case 'checklist':
				$list = true;
			case 'checkbox':
				ThemePlate_Field::checkbox( $field, $seq, $list );
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
				ThemePlate_Field::group( $field );
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
