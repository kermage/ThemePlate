<?php

/**
 * Setup term meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_TermMeta {

	private $meta_box;


	public function __construct( $meta_box ) {

		if ( ! is_array( $meta_box ) || empty( $meta_box ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $meta_box ) || ! array_key_exists( 'title', $meta_box ) ) {
			return false;
		}

		if ( ! is_array( $meta_box['fields'] ) || empty( $meta_box['fields'] ) ) {
			return false;
		}

		$this->meta_box = $meta_box;

		if ( empty( $meta_box['taxonomy'] ) ) {
			$taxonomies = get_taxonomies( array( '_builtin' => false ) );
			$taxonomies['category'] = 'category';
			$taxonomies['post_tag'] = 'post_tag';
		} else {
			$taxonomies = $meta_box['taxonomy'];
		}

		foreach ( (array) $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form', array( $this, 'create' ) );
			add_action( $taxonomy . '_edit_form', array( $this, 'create' ) );
			add_action( 'created_' . $taxonomy, array( $this, 'save' ) );
			add_action( 'edited_' . $taxonomy, array( $this, 'save' ) );
		}

	}


	public function create( $tag ) {

		$meta_box = $this->meta_box;
		$check = true;

		if ( isset( $meta_box['show_on'] ) ) {
			$value = $meta_box['show_on'];

			if ( is_callable( $value ) ) {
				$check = call_user_func( $value );
				unset( $meta_box['show_on'] );
			} elseif ( is_array( $value ) ) {
				if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
					$value = array( $value );
					$meta_box['show_on'] = array( $meta_box['show_on'] );
				}

				if ( ( count( $value ) == 1 ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['show_on'] );

					if ( ! is_object( $tag ) || ( is_object( $tag ) && ! array_intersect( (array) $tag->term_id, (array) $value[0]['value'] ) ) ) {
						$check = false;
					}
				}
			}
		}

		if ( isset( $meta_box['hide_on'] ) ) {
			$value = $meta_box['hide_on'];

			if ( is_callable( $value ) ) {
				$check = ! call_user_func( $value );
				unset( $meta_box['hide_on'] );
			} elseif ( is_array( $value ) ) {
				if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
					$value = array( $value );
					$meta_box['hide_on'] = array( $meta_box['hide_on'] );
				}

				if ( ( count( $value ) == 1 ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['hide_on'] );

					if ( is_object( $tag ) && array_intersect( (array) $tag->term_id, (array) $value[0]['value'] ) ) {
						$check = false;
					}
				}
			}
		}

		if ( ! $check ) {
			return;
		}

		wp_enqueue_script( 'post' );
		wp_enqueue_media();

		printf( '<div id="themeplate_%s" class="postbox">', ThemePlate()->key . '_' . $meta_box['id'] );
		echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
		echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $meta_box['title'] ) . '</span>';
		echo '<span class="toggle-indicator" aria-hidden="true"></span>';
		echo '</button>';
		echo '<h2 class="hndle"><span>' . $meta_box['title'] . '</span></h2>';
		echo '<div class="inside">';

		if ( isset( $meta_box['show_on'] ) ) {
			$show_on = json_encode( $meta_box['show_on'], JSON_NUMERIC_CHECK );
			echo '<div class="themeplate-show" data-show="' . esc_attr( $show_on ) . '"></div>';
		}

		if ( isset( $meta_box['hide_on'] ) ) {
			$hide_on = json_encode( $meta_box['hide_on'], JSON_NUMERIC_CHECK );
			echo '<div class="themeplate-hide" data-hide="' . esc_attr( $hide_on ) . '"></div>';
		}

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p class="description">' . $meta_box['description'] . '</p>';
		}

		$style = isset( $meta_box['style'] ) ? $meta_box['style'] : '';

		echo '<div class="fields-container ' . $style . '">';

		foreach ( $meta_box['fields'] as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;
			$field['object'] = array(
				'type' => 'term',
				'id' => is_object( $tag ) ? $tag->term_id : ''
			);

			$key = $field['id'];
			$title = $field['name'];
			$name = ThemePlate()->key . '[' . $key . ']';
			$default = isset( $field['std'] ) ? $field['std'] : '';
			$unique = isset( $field['repeatable'] ) ? false : true;
			$stored = $field['object']['id'] ? get_term_meta( $field['object']['id'], $field['id'], $unique ) : '';
			$value = $stored ? $stored : $default;

			$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';
			$field['style'] = isset( $field['style'] ) ? $field['style'] : '';

			echo '<div class="field-wrapper type-' . $field['type'] . ' ' . $field['style'] . '">';
				echo '<div class="field-label">';
					echo '<label class="label" for="' . $key . '">' . $title . '</label>';
					echo ! empty( $field['desc'] ) ? '<p class="description">' . $field['desc'] . '</p>' : '';
				echo '</div>';
				echo '<div class="field-input' . ( $unique ? '' : ' repeatable' ) . '">';
					if ( $unique ) {
						$field['value'] = $value;
						$field['name'] =  $name;

						ThemePlate_Fields::instance()->render( $field );
					} else {
						foreach ( (array) $value as $i => $val ) {
							$field['value'] = $val;
							$field['id'] = $key . '_' . $i;
							$field['name'] =  $name . '[' . $i . ']';

							echo '<div class="themeplate-clone">';
								echo '<div class="themeplate-handle"></div>';
								ThemePlate_Fields::instance()->render( $field );
								echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
							echo '</div>';
						}

						$field['value'] = $default;
						$field['id'] = $key . '_i-x';
						$field['name'] =  $name . '[i-x]';

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

		echo '</div>';

		echo '</div>';
		echo '</div>';

	}


	public function save( $term_id ) {

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		foreach ( $this->meta_box['fields'] as $id => $field ) {
			$key = ThemePlate()->key . '_' . $this->meta_box['id'] . '_' . $id;

			if ( ! isset( $_POST[ThemePlate()->key][$key] ) ) {
				continue;
			}

			$unique = isset( $field['repeatable'] ) ? false : true;
			$stored = get_term_meta( $term_id, $key, $unique );
			$updated = $_POST[ThemePlate()->key][$key];

			if ( ! $unique ) {
				delete_term_meta( $term_id, $key );

				foreach ( (array) $updated as $i => $value ) {
					if ( is_array( $value ) ) {
						$value =  array_filter( $value );
					}

					if ( $i === 'i-x' || empty( $value ) ) {
						continue;
					}

					add_term_meta( $term_id, $key, $value );
				}
			} else {
				foreach ( (array) $updated as $i => $value ) {
					if ( is_array( $value ) ) {
						$updated[$i] =  array_filter( $value );
					}
				}

				if ( is_array( $updated ) ) {
					$updated =  array_filter( $updated );
				}

				if ( $stored == $updated ) {
					continue;
				}

				if ( $updated ) {
					update_term_meta( $term_id, $key, $updated, $stored );
				} else {
					delete_term_meta( $term_id, $key, $stored );
				}
			}
		}

	}

}
