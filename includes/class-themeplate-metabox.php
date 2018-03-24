<?php

/**
 * Setup meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_MetaBox {

	private $object_type;
	private $object_id;
	private $meta_box;

	private $meta_defaults = array(
		'show_on'    => array(),
		'hide_on'    => array(),
		'style'      => ''
	);

	private $field_defaults = array(
		'type'       => 'text',
		'options'    => array(),
		'multiple'   => false,
		'none'       => false,
		'std'        => '',
		'style'      => '',
		'repeatable' => false
	);


	public function __construct( $type, $params ) {

		if ( ! is_array( $params ) || empty( $params ) ) {
			throw new Exception();
		}

		if ( ! array_key_exists( 'id', $params ) || ! array_key_exists( 'title', $params ) || ! array_key_exists( 'fields', $params ) ) {
			throw new Exception();
		}

		if ( ! is_array( $params['fields'] ) || empty( $params['fields'] ) ) {
			throw new Exception();
		}

		$this->object_type = $type;
		$this->meta_box = $params;

	}


	public function object_id( $number ) {

		$this->object_id = $number;

	}


	public function layout_postbox() {

		$meta_box = $this->meta_box;

		printf( '<div id="themeplate_%s" class="postbox">', $meta_box['id'] );
			echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
				echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $meta_box['title'] ) . '</span>';
				echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';

			echo '<h2 class="hndle"><span>' . $meta_box['title'] . '</span></h2>';

			echo '<div class="inside">';
				$this->layout_inside();
			echo '</div>';
		echo '</div>';

	}


	public function layout_inside() {

		$meta_box = $this->meta_box;
		$meta_box = array_merge( $this->meta_defaults, $meta_box );

		wp_nonce_field( basename( __FILE__ ), 'themeplate_' . $meta_box['id'] . '_nonce' );

		ThemePlate_Helpers::render_options( $meta_box );

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p class="description">' . $meta_box['description'] . '</p>';
		}

		$style = isset( $meta_box['style'] ) ? $meta_box['style'] : '';

		echo '<div class="fields-container ' . $style . '">';
			$this->layout_fields();
		echo '</div>';

	}


	public function layout_fields() {

		$meta_box = $this->meta_box;

		foreach ( $meta_box['fields'] as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field = array_merge( $this->field_defaults, $field );

			if ( $this->object_type == 'options' ) {
				$field['id'] = $meta_box['id'] . '_' . $id;
			} else {
				$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;
			}

			$this->layout_field( $field );
		}

	}


	public function layout_field( $field ) {

		if ( $this->object_type == 'options' ) {
			$options = get_option( $this->object_id );
			$stored = isset( $options[$field['id']] ) ? $options[$field['id']] : '';
			$key = $this->object_id;
		} else {
			$stored = get_metadata( $this->object_type, $this->object_id, $field['id'], ! $field['repeatable'] );
			$key = ThemePlate()->key;
		}

		$value = $stored ? $stored : $field['std'];

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
					$field['name'] =  $base_name;

					ThemePlate_Fields::instance()->render( $field );
				} else {
					$base_id = $field['id'];

					foreach ( (array) $value as $i => $val ) {
						$field['value'] = $val;
						$field['id'] = $base_id . '_' . $i;
						$field['name'] =  $base_name . '[' . $i . ']';

						echo '<div class="themeplate-clone">';
							echo '<div class="themeplate-handle"></div>';
							ThemePlate_Fields::instance()->render( $field );
							echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
						echo '</div>';
					}

					$field['value'] = $field['std'];
					$field['id'] = $base_id . '_i-x';
					$field['name'] =  $base_name . '[i-x]';

					echo '<div class="themeplate-clone hidden">';
						echo '<div class="themeplate-handle"></div>';
						ThemePlate_Fields::instance()->render( $field );
						echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
					echo '</div>';
					echo '<input type="button" class="button clone-add" value="Add Field" />';
				}
			echo '</div>';
		echo '</div>';

	}


	public function save( $object_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['themeplate_' . $this->meta_box['id'] . '_nonce'] ) || ! wp_verify_nonce( $_POST['themeplate_' . $this->meta_box['id'] . '_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		foreach ( $this->meta_box['fields'] as $id => $field ) {
			$key = ThemePlate()->key . '_' . $this->meta_box['id'] . '_' . $id;

			if ( ! isset( $_POST[ThemePlate()->key][$key] ) ) {
				continue;
			}

			$stored = get_metadata( $this->object_type, $object_id, $key, ! $field['repeatable'] );
			$updated = $_POST[ThemePlate()->key][$key];

			if ( ! $field['repeatable'] ) {
				delete_metadata( $this->object_type, $object_id, $key );

				foreach ( (array) $updated as $i => $value ) {
					foreach ( (array) $value as $j => $val ) {
						if ( is_array( $val ) ) {
							$value[$j] = array_merge( array_filter( $val ) );
						}
					}

					if ( is_array( $value ) ) {
						$value = array_filter( $value );
					}

					if ( $i === 'i-x' || empty( $value ) ) {
						continue;
					}

					add_metadata( $this->object_type, $object_id, $key, $value );
				}
			} else {
				foreach ( (array) $updated as $i => $value ) {
					if ( is_array( $value ) ) {
						$updated[$i] = array_merge( array_filter( $value ) );
					}
				}

				if ( is_array( $updated ) ) {
					$updated = array_filter( $updated );
				}

				if ( ( ! $stored && ! $updated ) || $stored == $updated ) {
					continue;
				}

				if ( $updated ) {
					update_metadata( $this->object_type, $object_id, $key, $updated, $stored );
				} else {
					delete_metadata( $this->object_type, $object_id, $key, $stored );
				}
			}

		}

	}

}
