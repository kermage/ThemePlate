<?php

/**
 * Setup options meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Settings {

	private $tpmb;


	public function __construct( $config ) {

		try {
			$this->tpmb = new ThemePlate_MetaBox( 'options', $config );
		} catch( Exception $e ) {
			return false;
		}

		add_action( 'admin_init', array( $this, 'add' ) );

	}


	public function add() {

		$settings = $this->tpmb->config;
		$page = ThemePlate()->key . '-' . ( isset( $settings['page'] ) ? $settings['page'] : ThemePlate()->slug );
		$this->tpmb->object_id = $page;
		$page .= '-' . ( isset( $settings['context'] ) ? $settings['context'] : 'normal' );

		add_action( 'themeplate_settings_' . $page, array( $this->tpmb, 'layout_postbox' ) );

	}

}
