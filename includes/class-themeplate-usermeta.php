<?php

/**
 * Setup user meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_UserMeta {

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

		add_action( 'show_user_profile', array( $this, 'create' ) );
		add_action( 'edit_user_profile', array( $this, 'create' ) );
		add_action( 'user_new_form', array( $this, 'create' ) );
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
		add_action( 'user_register', array( $this, 'save' ) );

	}


	public function create( $user ) {

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

					if ( ! is_object( $user ) || ( is_object( $user ) && ! array_intersect( (array) $user->ID, (array) $value[0]['value'] ) ) ) {
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

					if ( is_object( $user ) && array_intersect( (array) $user->ID, (array) $value[0]['value'] ) ) {
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

		printf( '<div id="themeplate_%s-box" class="postbox">', ThemePlate()->key . '_' . $meta_box['id'] );
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

			$field['id'] = $meta_box['id'] . '_' . $id;
			$field['object'] = array(
				'type' => 'user',
				'id' => is_object( $user ) ? $user->ID : ''
			);

			$default = isset( $field['std'] ) ? $field['std'] : '';
			$stored = $field['object']['id'] ? get_user_meta( $field['object']['id'], $field['id'], true ) : '';
			$field['value'] = $stored ? $stored : $default;
			$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';

			echo '<div class="field-wrapper type-' . $field['type'] . '">';
				echo '<div class="field-label">';
					echo '<label class="label" for="' . $field['id'] . '">' . $field['name'] . '</label>';
					echo ! empty( $field['desc'] ) ? '<p class="description">' . $field['desc'] . '</p>' : '';
				echo '</div>';
				echo '<div class="field-input">';
					$field['name'] = ThemePlate()->key . '[' . $field['id'] . ']';
					ThemePlate_Fields::instance()->render( $field );
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';

		echo '</div>';
		echo '</div>';

	}


	public function save( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		foreach ( $_POST[ThemePlate()->key] as $key => $val ) {
			$meta = get_user_meta( $user_id, $key, true );
			if ( $val && ! isset( $meta ) ) {
				add_user_meta( $user_id, $key, $val, true );
			} elseif ( isset( $val[0] ) && $val != $meta ) {
				update_user_meta( $user_id, $key, $val, $meta );
			} elseif ( ! isset( $val[0] ) && isset( $meta ) ) {
				delete_user_meta( $user_id, $key, $meta );
			}
		}

	}

}
