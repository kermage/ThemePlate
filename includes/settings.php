<?php

/**
 * Setup options meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Settings {

	private $config;
	private $form;


	public function __construct( $config ) {

		$defaults = array(
			'show_on'  => array(),
			'hide_on'  => array(),
			'page'     => ThemePlate()->slug,
			'context'  => 'normal',
			'priority' => 'default',
		);
		$config   = ThemePlate_Helpers::fool_proof( $defaults, $config );

		$config['page']        = ThemePlate()->key . '-' . $config['page'];
		$config['object_type'] = 'options';

		$this->config = $config;

		try {
			$this->form = new ThemePlate_Form( $config );
		} catch ( Exception $e ) {
			throw new Exception( $e );
		}

		add_action( 'current_screen', array( $this, 'create' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function create() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$settings = $this->config;
		$section  = $settings['page'] . '_' . $settings['context'];
		$priority = ThemePlate_Helpers::get_priority( $settings );

		add_action( 'themeplate_settings_' . $section, array( $this, 'add' ), $priority );

	}


	public function add() {

		$settings = $this->config;
		$this->form->layout_postbox( $settings['page'] );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->form->enqueue();

	}


	private function is_valid_screen() {

		$settings = $this->config;
		$screen   = get_current_screen();

		if ( strpos( $screen->id, $settings['page'] ) === false ) {
			return false;
		}

		return true;

	}

}
