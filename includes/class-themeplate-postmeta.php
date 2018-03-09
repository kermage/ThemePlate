<?php

/**
 * Setup post meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_PostMeta {

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

		add_action( 'add_meta_boxes', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );

	}


	public function add() {

		$meta_box = $this->meta_box;
		$post_id = get_the_ID();
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

					if ( ! array_intersect( (array) $post_id, (array) $value[0]['value'] ) ) {
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

					if ( array_intersect( (array) $post_id, (array) $value[0]['value'] ) ) {
						$check = false;
					}
				}
			}
		}

		if ( ! $check ) {
			return;
		}

		$defaults = array(
			'screen'   => '',
			'context'  => 'advanced',
			'priority' => 'default'
		);
		$meta_box = wp_parse_args( $meta_box, $defaults );

		$meta_box['id'] = ThemePlate()->key . '_' . $meta_box['id'];
		$id = 'themeplate_' . $meta_box['id'];

		add_meta_box( $id, $meta_box['title'], array( $this, 'create' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'], $meta_box );

	}


	public function create( $post, $meta_box ) {

		wp_nonce_field( basename( __FILE__ ), 'themeplate_meta_box_nonce' );

		if ( isset( $meta_box['args']['show_on'] ) ) {
			$show_on = json_encode( $meta_box['args']['show_on'], JSON_NUMERIC_CHECK );
			echo '<div class="themeplate-show" data-show="' . esc_attr( $show_on ) . '"></div>';
		}

		if ( isset( $meta_box['args']['hide_on'] ) ) {
			$hide_on = json_encode( $meta_box['args']['hide_on'], JSON_NUMERIC_CHECK );
			echo '<div class="themeplate-hide" data-hide="' . esc_attr( $hide_on ) . '"></div>';
		}

		if ( ! empty( $meta_box['args']['description'] ) ) {
			echo '<p class="description">' . $meta_box['args']['description'] . '</p>';
		}

		$style = isset( $meta_box['args']['style'] ) ? $meta_box['args']['style'] : '';

		echo '<div class="fields-container ' . $style . '">';

		foreach ( $meta_box['args']['fields'] as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field['id'] = $meta_box['args']['id'] . '_' . $id;
			$field['object'] = array(
				'type' => 'post',
				'id' => $post->ID
			);

			$default = isset( $field['std'] ) ? $field['std'] : '';
			$stored = get_post_meta( $field['object']['id'], $field['id'], true );
			$field['value'] = $stored ? $stored : $default;
			$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';

			echo '<div class="field-wrapper type-' . $field['type'] . '">';
				echo '<div class="field-label">';
					echo '<label class="label" for="' . $field['id'] . '">' . $field['name'] . '</label>';
					echo ! empty( $field['desc'] ) ? '<p class="description">' . $field['desc'] . '</p>' : '';
				echo '</div>';
				echo '<div class="field-input">';
					$field['name'] = ( isset( $field['prefix'] ) ? $field['prefix'] : ThemePlate()->key ) . '[' . $field['id'] . ']';
					ThemePlate_Fields::instance()->render( $field );
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';

	}


	public function save( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['themeplate_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['themeplate_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		foreach ( $_POST[ThemePlate()->key] as $key => $val ) {
			$meta = get_post_meta( $post_id, $key, true );
			if ( $val && ! isset( $meta ) ) {
				add_post_meta( $post_id, $key, $val, true );
			} elseif ( isset( $val[0] ) && $val != $meta ) {
				update_post_meta( $post_id, $key, $val, $meta );
			} elseif ( ! isset( $val[0] ) && isset( $meta ) ) {
				delete_post_meta( $post_id, $key, $meta );
			}
		}

	}

}
