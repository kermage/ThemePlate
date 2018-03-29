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
			$config['object_type'] = 'user';
			$this->tpmb = new ThemePlate_MetaBox( $config );
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

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$user_id = is_object( $user ) ? $user->ID : '';

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

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->tpmb->enqueue();

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( ! in_array( $screen->base, array( 'user', 'user-edit', 'profile' ) ) ) {
			return false;
		}

		$meta_box = $this->tpmb->get_config();

		if ( $screen->base == 'user-edit' && ! ThemePlate_Helpers::should_display( $meta_box, $_REQUEST['user_id'] ) ) {
			return false;
		}

		if ( $screen->base == 'profile' && ! ThemePlate_Helpers::should_display( $meta_box, get_current_user_id() ) ) {
			return false;
		}

		return true;

	}

}
