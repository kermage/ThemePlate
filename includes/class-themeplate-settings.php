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
			$defaults = array(
				'page'   => ThemePlate()->slug,
				'context'  => 'normal'
			);
			$config = ThemePlate_Helpers::fool_proof( $defaults, $config );
			$config['page'] = ThemePlate()->key . '-' . $config['page'];
			$config['object_type'] = 'options';
			$this->tpmb = new ThemePlate_MetaBox( $config );
		} catch ( Exception $e ) {
			return false;
		}

		add_action( 'current_screen', array( $this, 'create' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function create() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$settings = $this->tpmb->get_config();
		$section = $settings['page'] . '-' . $settings['context'];

		add_action( 'themeplate_settings_' . $section, array( $this, 'add' ) );

	}


	public function add() {

		$settings = $this->tpmb->get_config();
		$this->tpmb->layout_postbox( $settings['page'] );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->tpmb->enqueue();

	}


	private function is_valid_screen() {

		$settings = $this->tpmb->get_config();
		$screen = get_current_screen();

		if ( strpos( $screen->id, $settings['page'] ) === false ) {
			return false;
		}

		return true;

	}

}
