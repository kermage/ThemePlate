<?php

/**
 * Setup menu meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Meta;

use ThemePlate\Column;
use ThemePlate\Core\Helper\Box;
use ThemePlate\Core\Helper\Main;
use ThemePlate\Core\Helper\Meta;

class Menu extends Base {

	public function __construct( $config ) {

		$config['object_type'] = 'menu';

		try {
			parent::__construct( $config );
		} catch ( \Exception $e ) {
			throw new \Exception( $e );
		}

		$defaults = array(
			'priority' => 'default',
		);

		$this->config = Main::fool_proof( $defaults, $this->config );

		$priority = Box::get_priority( $this->config );

		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'create' ), $priority );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ), 11 );

	}


	public function create( $item_id ) {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->form->layout_inside( $item_id );

	}


	public function save( $item_id ) {

		if ( ! $this->can_save() ) {
			return;
		}

		if ( ! current_user_can( 'edit_theme_options', $item_id ) ) {
			return;
		}

		parent::save( $item_id );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->form->enqueue( 'menu' );

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( null === $screen || 'nav-menus' !== $screen->base ) {
			return false;
		}

		return true;

	}


	protected function column_data( $args ) {}

}
