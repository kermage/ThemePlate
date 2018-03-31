<?php

/**
 * Setup meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_MetaBox {

	private $config;
	private $fields;

	private $defaults = array(
		'show_on' => array(),
		'hide_on' => array(),
		'style'   => '',
	);


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

		$this->config = ThemePlate_Helpers::fool_proof( $this->defaults, $config );
		$this->config = ThemePlate_Helpers::normalize_options( $this->config );
		$this->fields = new ThemePlate_Fields( $config['fields'] );

	}


	public function enqueue() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'themeplate-style', TP_URL . 'assets/themeplate.css', array(), TP_VERSION, 'all' );
		wp_enqueue_script( 'themeplate-script', TP_URL . 'assets/themeplate.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-show-hide', TP_URL . 'assets/show-hide.js', array(), TP_VERSION, true );
		wp_enqueue_script( 'themeplate-repeater', TP_URL . 'assets/repeater.js', array(), TP_VERSION, true );
		wp_enqueue_style( 'themeplate-select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5', 'all' );
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

		wp_nonce_field( basename( __FILE__ ), 'themeplate_' . $meta_box['id'] . '_nonce' );

		ThemePlate_Helpers::render_options( $meta_box );

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p class="description">' . $meta_box['description'] . '</p>';
		}

		echo '<div class="fields-container ' . $meta_box['style'] . '">';
			$this->fields->setup( $meta_box['id'], $meta_box['object_type'], $object_id );
		echo '</div>';

	}


	public function save( $object_id ) {

		$meta_box = $this->config;
		$fields   = $this->fields->get_collection();

		foreach ( $fields as $id => $field ) {
			$key = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;

			if ( ! isset( $_POST[ ThemePlate()->key ][ $key ] ) ) {
				continue;
			}

			$stored  = get_metadata( $meta_box['object_type'], $object_id, $key, ! $field['repeatable'] );
			$updated = $_POST[ ThemePlate()->key ][ $key ];

			if ( $field['repeatable'] ) {
				delete_metadata( $meta_box['object_type'], $object_id, $key );

				foreach ( (array) $updated as $i => $value ) {
					foreach ( (array) $value as $j => $val ) {
						if ( is_array( $val ) ) {
							$value[ $j ] = array_merge( array_filter( $val ) );
						}
					}

					if ( is_array( $value ) ) {
						$value = array_filter( $value );
					}

					if ( 'i-x' === $i || empty( $value ) ) {
						continue;
					}

					add_metadata( $meta_box['object_type'], $object_id, $key, $value );
				}
			} else {
				foreach ( (array) $updated as $i => $value ) {
					if ( is_array( $value ) ) {
						$updated[ $i ] = array_merge( array_filter( $value ) );
					}
				}

				if ( is_array( $updated ) ) {
					$updated = array_filter( $updated );
				}

				if ( ( ! $stored && ! $updated ) || $stored === $updated ) {
					continue;
				}

				if ( $updated ) {
					update_metadata( $meta_box['object_type'], $object_id, $key, $updated, $stored );
				} else {
					delete_metadata( $meta_box['object_type'], $object_id, $key, $stored );
				}
			}
		}

	}


	public function get_config() {

		return $this->config;

	}


	public function can_save() {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( ! isset( $_POST[ 'themeplate_' . $this->config['id'] . '_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'themeplate_' . $this->config['id'] . '_nonce' ], basename( __FILE__ ) ) ) {
			return false;
		}

		return true;

	}

}
