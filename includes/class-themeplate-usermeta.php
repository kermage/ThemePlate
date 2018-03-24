<?php

/**
 * Setup user meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_UserMeta {

	private $config;
	private $tpmb;


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $config ) || ! array_key_exists( 'title', $config ) ) {
			return false;
		}

		if ( ! is_array( $config['fields'] ) || empty( $config['fields'] ) ) {
			return false;
		}

		$this->config = $config;
		$this->tpmb = new ThemePlate_MetaBox( 'user', $config );

		add_action( 'show_user_profile', array( $this, 'create' ) );
		add_action( 'edit_user_profile', array( $this, 'create' ) );
		add_action( 'user_new_form', array( $this, 'create' ) );
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
		add_action( 'user_register', array( $this, 'save' ) );

	}


	public function create( $user ) {

		$meta_box = $this->config;
		$user_id = is_object( $user ) ? $user->ID : '';
		$this->tpmb->object_id( $user_id );
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

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['show_on'] );

					if ( ! is_object( $user ) || ( is_object( $user ) && ! array_intersect( (array) $user_id, (array) $value[0]['value'] ) ) ) {
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

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['hide_on'] );

					if ( is_object( $user ) && array_intersect( (array) $user_id, (array) $value[0]['value'] ) ) {
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

		$this->tpmb->layout_postbox();

	}


	public function save( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$this->tpmb->save( $user_id );

	}

}
