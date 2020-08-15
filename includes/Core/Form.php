<?php

/**
 * Setup custom forms
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Core;

use ThemePlate\Core\Helper\Main;
use ThemePlate\Core\Helper\Meta;

class Form {

	private $config;
	private $fields;


	public function __construct( $config ) {

		$expected = array(
			'object_type',
			'id',
			'title',
			'fields',
		);

		if ( ! Main::is_complete( $config, $expected ) ) {
			throw new \Exception();
		}

		$defaults     = array(
			'style' => '',
		);
		$this->config = Main::fool_proof( $defaults, $config );
		$this->config = Meta::normalize_options( $this->config );
		$this->fields = new Fields( $config['fields'] );

	}


	public function enqueue( $object_type ) {

		if ( wp_script_is( 'themeplate-script', 'enqueued' ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'editor-buttons' );
		wp_enqueue_script( 'wplink' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'themeplate-select2-style', Main::get_url( TP_CORE_PATH . '/assets/select2.min.css' ), array(), '4.0.12' );
		wp_enqueue_script( 'themeplate-select2-script', Main::get_url( TP_CORE_PATH . '/assets/select2.full.min.js' ), array(), '4.0.12', true );
		wp_enqueue_style( 'themeplate-datepicker-style', Main::get_url( TP_CORE_PATH . '/assets/datepicker.min.css' ), array(), '1.9.0' );
		wp_enqueue_script( 'themeplate-datepicker-script', Main::get_url( TP_CORE_PATH . '/assets/datepicker.min.js' ), array(), '1.9.0', true );
		wp_add_inline_script( 'themeplate-datepicker-script', 'if ( ! jQuery.fn.bootstrapDP && jQuery.fn.datepicker && jQuery.fn.datepicker.noConflict ) jQuery.fn.bootstrapDP = jQuery.fn.datepicker.noConflict();' );
		wp_enqueue_style( 'themeplate-style', Main::get_url( TP_CORE_PATH . '/assets/themeplate.css' ), array(), TP_CORE_VERSION );
		wp_enqueue_script( 'themeplate-script', Main::get_url( TP_CORE_PATH . '/assets/themeplate.js' ), array(), TP_CORE_VERSION, true );
		wp_enqueue_script( 'themeplate-wysiwyg', Main::get_url( TP_CORE_PATH . '/assets/wysiwyg.js' ), array(), TP_CORE_VERSION, true );
		wp_enqueue_script( 'themeplate-show-hide', Main::get_url( TP_CORE_PATH . '/assets/show-hide.js' ), array(), TP_CORE_VERSION, true );
		wp_enqueue_script( 'themeplate-repeater', Main::get_url( TP_CORE_PATH . '/assets/repeater.js' ), array(), TP_CORE_VERSION, true );

		wp_localize_script( 'themeplate-script', 'ThemePlate', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		if ( 'post' !== $object_type ) {
			return;
		}

		if ( function_exists( 'use_block_editor_for_post' ) && use_block_editor_for_post( get_the_ID() ) ) {
			wp_enqueue_script( 'themeplate-show-hide-gutenberg', Main::get_url( TP_CORE_PATH . '/assets/show-hide-gutenberg.js' ), array(), TP_CORE_VERSION, true );
		} else {
			wp_enqueue_script( 'themeplate-show-hide-classic', Main::get_url( TP_CORE_PATH . '/assets/show-hide-classic.js' ), array(), TP_CORE_VERSION, true );
		}

	}


	public function layout_postbox( $object_id ) {

		global $wp_version;

		$meta_box = $this->config;

		printf( '<div id="themeplate_%s" class="tpo postbox">', esc_attr( $meta_box['id'] ) );

		if ( version_compare( $wp_version, '5.5', '<' ) ) {
			echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
				echo '<span class="screen-reader-text">' . esc_html( sprintf( __( 'Toggle panel: %s' ), $meta_box['title'] ) ) . '</span>';
				echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';

			echo '<h2 class="hndle"><span>' . esc_html( $meta_box['title'] ) . '</span></h2>';
		} else {
			echo '<div class="postbox-header">';
				echo '<h2 class="hndle"><span>' . esc_html( $meta_box['title'] ) . '</span></h2>';

				echo '<div class="handle-actions hide-if-no-js">';
					echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
						echo '<span class="screen-reader-text">' . esc_html( sprintf( __( 'Toggle panel: %s' ), $meta_box['title'] ) ) . '</span>';
						echo '<span class="toggle-indicator" aria-hidden="true"></span>';
					echo '</button>';
				echo '</div>';
			echo '</div>';
		}

			echo '<div class="inside">';
				$this->layout_inside( $object_id );
			echo '</div>';
		echo '</div>';

	}


	public function layout_inside( $object_id ) {

		$meta_box = $this->config;

		wp_nonce_field( 'save_themeplate_' . $meta_box['id'], 'themeplate_' . $meta_box['id'] . '_nonce' );

		Meta::render_options( $meta_box );

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p class="description">' . $meta_box['description'] . '</p>'; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		echo '<div class="fields-container ' . esc_attr( $meta_box['style'] ) . '">';
			$this->fields->setup( $meta_box['id'], $meta_box['object_type'], $object_id );
		echo '</div>';

	}


	public function get_fields() {

		return $this->fields->get_collection();

	}

}
