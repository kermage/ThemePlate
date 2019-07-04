<?php

/**
 * Setup user meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Meta_User extends ThemePlate_Meta_Base {

	public function __construct( $config ) {

		$config['object_type'] = 'user';

		try {
			parent::__construct( $config );
		} catch ( Exception $e ) {
			throw new Exception( $e );
		}

		$defaults = array(
			'priority' => 'default',
		);

		$this->config = ThemePlate_Helper_Main::fool_proof( $defaults, $this->config );

		$priority = ThemePlate_Helper_Box::get_priority( $this->config );

		add_action( 'show_user_profile', array( $this, 'create' ), $priority );
		add_action( 'edit_user_profile', array( $this, 'create' ), $priority );
		add_action( 'user_new_form', array( $this, 'create' ), $priority );
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
		add_action( 'user_register', array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ), 11 );

		$this->columns();

	}


	public function create( $user ) {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$user_id = is_object( $user ) ? $user->ID : '';

		$this->form->layout_postbox( $user_id );

	}


	public function save( $user_id ) {

		if ( ! $this->can_save() ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		parent::save( $user_id );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->form->enqueue( 'user' );

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( ! in_array( $screen->base, array( 'user', 'user-edit', 'profile' ), true ) ) {
			return false;
		}

		$meta_box = $this->config;

		if ( 'user-edit' === $screen->base && ! ThemePlate_Helper_Meta::should_display( $meta_box, $_REQUEST['user_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return false;
		}

		if ( 'profile' === $screen->base && ! ThemePlate_Helper_Meta::should_display( $meta_box, get_current_user_id() ) ) {
			return false;
		}

		return true;

	}


	protected function column_data( $args ) {

		$args['users'] = true;

		new ThemePlate_Columns( $args );

	}

}
