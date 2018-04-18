<?php

/**
 * Setup custom forms
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Form {

	private $config;
	private $fields;


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			throw new Exception();
		}

		if ( ! array_key_exists( 'object_type', $config ) || ! array_key_exists( 'id', $config ) || ! array_key_exists( 'title', $config ) || ! array_key_exists( 'fields', $config ) ) {
			throw new Exception();
		}

		if ( ! is_array( $config['fields'] ) || empty( $config['fields'] ) ) {
			throw new Exception();
		}

		$this->config = $config;
		$this->fields = new ThemePlate_Fields( $config['fields'] );

	}


	public function enqueue() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'themeplate-style', TP_URL . 'assets/themeplate.css', array(), TP_VERSION );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'assets/themeplate.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-show-hide', TP_URL . 'assets/show-hide.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-repeater', TP_URL . 'assets/repeater.js', array(), TP_VERSION, true );
		wp_enqueue_style( 'themeplate-select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5' );
		wp_enqueue_script( 'themeplate-select2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js', array(), '4.0.5', true );

	}


	public function layout_postbox( $object_id = 0 ) {

		$meta_box = $this->config;

		printf( '<div id="themeplate_%s" class="postbox">', $meta_box['id'] );
			echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
				echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $meta_box['title'] ) . '</span>';
				echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';

			echo '<h2 class="hndle"><span>' . $meta_box['title'] . '</span></h2>';

			echo '<div class="inside">';
				$this->layout_inside( $object_id );
			echo '</div>';
		echo '</div>';

	}


	public function layout_inside( $object_id = 0 ) {

		$meta_box = $this->config;

		wp_nonce_field( 'save_themeplate_' . $meta_box['id'], 'themeplate_' . $meta_box['id'] . '_nonce' );

		ThemePlate_Helpers::render_options( $meta_box );

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p class="description">' . $meta_box['description'] . '</p>';
		}

		echo '<div class="fields-container ' . $meta_box['style'] . '">';
			$this->fields->setup( $meta_box['id'], $meta_box['object_type'], $object_id );
		echo '</div>';

	}

}
