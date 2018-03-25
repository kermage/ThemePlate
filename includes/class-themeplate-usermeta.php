<?php

/**
 * Setup user meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_UserMeta {

	private $tpmb;


	public function __construct( $config ) {

		try {
			$this->tpmb = new ThemePlate_MetaBox( 'user', $config );
		} catch( Exception $e ) {
			return false;
		}

		add_action( 'show_user_profile', array( $this, 'create' ) );
		add_action( 'edit_user_profile', array( $this, 'create' ) );
		add_action( 'user_new_form', array( $this, 'create' ) );
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
		add_action( 'user_register', array( $this, 'save' ) );

	}


	public function create( $user ) {

		$meta_box = $this->tpmb->config;
		$user_id = is_object( $user ) ? $user->ID : '';
		$this->tpmb->object_id = $user_id;

		if ( ! ThemePlate_Helpers::should_display( $meta_box, $user_id ) ) {
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
