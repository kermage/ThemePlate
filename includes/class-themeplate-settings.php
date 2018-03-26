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

		add_action( 'admin_init', array( $this, 'create' ) );

	}


	public function create() {

		$settings = $this->tpmb->get_config();
		$key = ThemePlate()->key . '-' . ( isset( $settings['page'] ) ? $settings['page'] : ThemePlate()->slug );
		$page = $key . '-' . ( isset( $settings['context'] ) ? $settings['context'] : 'normal' );

		$this->key = $key;

		add_action( 'themeplate_settings_' . $page, array( $this, 'add' ) );

	}


	public function add() {

		$this->tpmb->layout_postbox( $this->key );

	}

}
