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
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function create( $user ) {

		$meta_box = $this->tpmb->get_config();
		$user_id = is_object( $user ) ? $user->ID : '';

		if ( ! ThemePlate_Helpers::should_display( $meta_box, $user_id ) ) {
			return;
		}

		wp_enqueue_script( 'post' );
		wp_enqueue_media();

		$this->tpmb->layout_postbox( $user_id );

	}


	public function save( $user_id ) {

		if ( ! $this->tpmb->can_save() ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$this->tpmb->save( $user_id );

	}


	public function scripts_styles() {

		$screen = get_current_screen();

		if ( ! in_array( $screen->base, array( 'user', 'user-edit', 'profile' ) ) ) {
			return;
		}

		$this->tpmb->enqueue();

	}

}
