<?php

/**
 * Setup meta fields
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Fields {

	private $collection;


	public function __construct( $collection ) {

		if ( ! is_array( $collection ) || empty( $collection ) ) {
			throw new Exception();
		}

		$this->collection = $this->filter( $collection );

	}


	private function filter( $fields ) {

		$processed = array();

		foreach ( $fields as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$defaults = array(
				'type'       => 'text',
				'options'    => array(),
				'multiple'   => false,
				'none'       => false,
				'default'    => '',
				'style'      => '',
				'repeatable' => false,
				'required'   => false,
				'column'     => false,
			);
			$field    = ThemePlate_Helper_Main::fool_proof( $defaults, $field );
			$field    = ThemePlate_Helper_Meta::normalize_options( $field );
			$field    = ThemePlate_Helper_Field::deprecate_check( $field );

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

			$value = $stored ? $stored : $field['default'];
			$name  = $key . '[' . $field['id'] . ']';

			$this->layout( $field, $value, $name );
		}

	}


	private function layout( $field, $value, $name ) {

		echo '<div class="field-wrapper type-' . $field['type'] . ' ' . $field['style'] . '">';
			ThemePlate_Helper_Meta::render_options( $field );

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

				echo ! empty( $field['information'] ) ? '<p class="description">' . $field['information'] . '</p>' : '';
			echo '</div>';
		echo '</div>';

	}


	private function render( $field ) {

		if ( 'group' !== $field['type'] ) {
			return ThemePlate_Helper_Field::render( $field );
		}

		foreach ( $field['fields'] as $id => $sub ) {
			$sub['id'] = $field['id'] . '_' . $id;

			$stored = isset( $field['value'][ $id ] ) ? $field['value'][ $id ] : '';
			$value  = $stored ? $stored : $sub['default'];
			$name   = $field['name'] . '[' . $id . ']';

			$this->layout( $sub, $value, $name );
		}

	}


	public function get_collection() {

		return $this->collection;

	}

}
