<?php

/**
 * Setup user meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_UserMeta {

	private $meta_box;


	public static function instance() {

		return new self();

	}


	public function __construct() {


	}


	public function add( $meta_box ) {

		if ( ! is_array( $meta_box ) ) {
			return false;
		}

		$this->meta_box = $meta_box;

		add_action( 'show_user_profile', array( $this, 'create' ) );
		add_action( 'edit_user_profile', array( $this, 'create' ) );
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );

	}


	public function create( $user ) {

		wp_enqueue_media();

		$meta_box = $this->meta_box;
		$fields = $meta_box['fields'];

		if ( is_array( $fields ) ) {

			foreach ( $fields as $id => $field ) {
				$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;
				$field['value'] = get_user_meta( $user->ID, $field['id'], true );
				$field['value'] = $field['value'] ? $field['value'] : $field['std'];

				ThemePlate_Fields::instance()->render( $field );
			}

		}

	}


	public function save( $user_id ) {

		foreach ( $_POST[ThemePlate()->key] as $key => $val ) {
			$meta = get_user_meta( $user_id, $key, true );
			if ( $val && ! isset( $meta ) ) {
				add_user_meta( $user_id, $key, $val, true );
			} elseif ( $val[0] && $val != $meta ) {
				update_user_meta( $user_id, $key, $val, $meta );
			} elseif ( ! $val[0] && isset( $meta ) ) {
				delete_user_meta( $user_id, $key, $meta );
			}
		}

	}

}
