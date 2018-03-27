<?php

/**
 * Setup options meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Settings {

	private $tpmb;
	private $key;


	public function __construct( $config ) {

		try {
			$this->tpmb = new ThemePlate_MetaBox( 'options', $config );
		} catch( Exception $e ) {
			return false;
		}

		add_action( 'current_screen', array( $this, 'create' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function create() {

		$settings = $this->tpmb->get_config();
		$key = ThemePlate()->key . '-' . ( isset( $settings['page'] ) ? $settings['page'] : ThemePlate()->slug );
		$this->key = $key;

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$page = $key . '-' . ( isset( $settings['context'] ) ? $settings['context'] : 'normal' );

		add_action( 'themeplate_settings_' . $page, array( $this, 'add' ) );

	}


	public function add() {

		$this->tpmb->layout_postbox( $this->key );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->tpmb->enqueue();

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( strpos( $screen->id, $this->key ) === false ) {
			return false;
		}

		return true;

	}

}
