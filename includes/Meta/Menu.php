<?php

/**
 * Setup menu meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Legacy\Meta;

use ThemePlate\Legacy\Core\Helper\Box;
use ThemePlate\Legacy\Core\Helper\Main;

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

		global $wp_version;

		printf( '<div id="themeplate_%s" class="tpo postbox description-wide">', esc_attr( $this->config['id'] ) );

			if ( version_compare( $wp_version, '5.5', '<' ) ) {
				echo '<h2 class="hndle"><span>' . esc_html( $this->config['title'] ) . '</span></h2>';
			} else {
				echo '<div class="postbox-header">';
					echo '<h2 class="hndle"><span>' . esc_html( $this->config['title'] ) . '</span></h2>';
				echo '</div>';
			}

			echo '<div class="inside">';
				$this->form->layout_inside( $item_id );
			echo '</div>';
		echo '</div>';

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

		return ! ( null === $screen || 'nav-menus' !== $screen->base );

	}


	protected function column_data( $args ) {}

}
