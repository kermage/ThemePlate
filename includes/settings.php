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
	private $page;


	public function __construct( $config ) {

		$expected = array(
			'id',
			'title',
		);

		if ( ! ThemePlate_Helper_Main::is_complete( $config, $expected ) ) {
			throw new Exception();
		}

		$defaults = array(
			'show_on'  => array(),
			'hide_on'  => array(),
			'page'     => ThemePlate()->slug,
			'context'  => 'normal',
			'priority' => 'default',
		);
		$config   = ThemePlate_Helper_Main::fool_proof( $defaults, $config );

		$config['object_type'] = 'options';

		$this->config = $config;

		try {
			$this->form = new ThemePlate_Form( $config );
		} catch ( Exception $e ) {
			throw new Exception( $e );
		}

		add_action( 'current_screen', array( $this, 'create' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ), 11 );

	}


	public function create() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$settings = $this->config;
		$section  = $this->page . '_' . $settings['context'];
		$priority = ThemePlate_Helper_Box::get_priority( $settings );

		add_action( 'themeplate_settings_' . $section, array( $this, 'add' ), $priority );

	}


	public function add() {

		$this->form->layout_postbox( $this->page );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->form->enqueue( 'settings' );

	}


	private function is_valid_screen() {

		$screen = get_current_screen();
		$prefix = ThemePlate()->key . '-';

		if ( null === $screen || false === strpos( $screen->id, '_page_' . $prefix ) ) {
			return false;
		}

		$page_s = (array) $this->config['page'];
		$sparts = explode( $prefix, $screen->id );

		foreach ( $page_s as $page ) {
			if ( $sparts[1] === $page ) {
				$this->page = $prefix . $page;

				return true;
			}
		}

		return false;

	}

}
