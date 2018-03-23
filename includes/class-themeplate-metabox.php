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


	public function __construct( $type, $id, $params ) {

		if ( ! is_array( $params ) || empty( $params ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $params ) || ! array_key_exists( 'title', $params ) ) {
			return false;
		}

		if ( ! is_array( $params['fields'] ) || empty( $params['fields'] ) ) {
			return false;
		}

		$this->object_type = $type;
		$this->object_id = $id;
		$this->meta_box = $params;

		$this->setup();

	}


	public function setup() {

		if ( $this->object_type == 'post' ) {
			$this->layout_inside();
		} else {
			$this->layout_postbox();
		}

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

		if ( isset( $meta_box['show_on'] ) || isset( $meta_box['hide_on'] ) ) {
			echo '<div class="themeplate-options"';

			if ( isset( $meta_box['show_on'] ) ) {
				$show_on = json_encode( $meta_box['show_on'], JSON_NUMERIC_CHECK );
				echo ' data-show="' . esc_attr( $show_on ) . '"';
			}

			if ( isset( $meta_box['hide_on'] ) ) {
				$hide_on = json_encode( $meta_box['hide_on'], JSON_NUMERIC_CHECK );
				echo ' data-hide="' . esc_attr( $hide_on ) . '"';
			}

			echo '></div>';
		}

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

			$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;
			$field['object'] = array(
				'type' => $this->object_type,
				'id' => $this->object_id
			);
			$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';
			$field['style'] = isset( $field['style'] ) ? $field['style'] : '';

			$this->layout_field( $field );
		}

	}


	public function layout_field( $field ) {

		$default = isset( $field['std'] ) ? $field['std'] : '';
		$unique = isset( $field['repeatable'] ) ? false : true;
		$stored = get_metadata( $field['object']['type'], $field['object']['id'], $field['id'], $unique );
		$value = $stored ? $stored : $default;

		echo '<div class="field-wrapper type-' . $field['type'] . ' ' . $field['style'] . '">';
			if ( isset( $field['show_on'] ) || isset( $field['hide_on'] ) ) {
				echo '<div class="themeplate-options"';

				if ( isset( $field['show_on'] ) ) {
					$show_on = json_encode( $field['show_on'], JSON_NUMERIC_CHECK );
					echo ' data-show="' . esc_attr( $show_on ) . '"';
				}

				if ( isset( $field['hide_on'] ) ) {
					$hide_on = json_encode( $field['hide_on'], JSON_NUMERIC_CHECK );
					echo ' data-hide="' . esc_attr( $hide_on ) . '"';
				}

				echo '></div>';
			}

			if ( ! empty( $field['name'] ) || ! empty( $field['desc'] ) ) {
				echo '<div class="field-label">';
					echo ! empty( $field['name'] ) ? '<label class="label" for="' . $field['id'] . '">' . $field['name'] . '</label>' : '';
					echo ! empty( $field['desc'] ) ? '<p class="description">' . $field['desc'] . '</p>' : '';
				echo '</div>';
			}

			echo '<div class="field-input' . ( $unique ? '' : ' repeatable' ) . '">';
				$base_name = ThemePlate()->key . '[' . $field['id'] . ']';

				if ( $unique ) {
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

					$field['value'] = $default;
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


	public function save() {

		foreach ( $this->meta_box['fields'] as $id => $field ) {
			$key = ThemePlate()->key . '_' . $this->meta_box['id'] . '_' . $id;

			if ( ! isset( $_POST[ThemePlate()->key][$key] ) ) {
				continue;
			}

			$unique = isset( $field['repeatable'] ) ? false : true;
			$stored = get_metadata( $this->object_type, $this->object_id, $key, $unique );
			$updated = $_POST[ThemePlate()->key][$key];

			if ( ! $unique ) {
				delete_metadata( $this->object_type, $this->object_id, $key );

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

					add_metadata( $this->object_type, $this->object_id, $key, $value );
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
					update_metadata( $this->object_type, $this->object_id, $key, $updated, $stored );
				} else {
					delete_metadata( $this->object_type, $this->object_id, $key, $stored );
				}
			}

		}

	}

}
