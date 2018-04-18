<?php

/**
 * Setup meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


abstract class ThemePlate_Meta_Base {

	protected $config;
	protected $form;


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			throw new Exception();
		}

		if ( ! array_key_exists( 'id', $config ) || ! array_key_exists( 'title', $config ) ) {
			throw new Exception();
		}

		try {
			$this->form = new ThemePlate_Form( $config );
		} catch ( Exception $e ) {
			throw new Exception( $e );
		}

		$config['fields'] = $this->form->get_fields();

		$defaults     = array(
			'show_on' => array(),
			'hide_on' => array(),
		);
		$this->config = ThemePlate_Helpers::fool_proof( $defaults, $config );
		$this->config = ThemePlate_Helpers::normalize_options( $this->config );

	}


	public function save( $object_id ) {

		$meta_box = $this->config;

		foreach ( $meta_box['fields'] as $id => $field ) {
			$key = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;

			if ( ! isset( $_POST[ ThemePlate()->key ][ $key ] ) ) {
				continue;
			}

			$stored  = get_metadata( $meta_box['object_type'], $object_id, $key, ! $field['repeatable'] );
			$updated = $_POST[ ThemePlate()->key ][ $key ];
			$cleaned = ThemePlate_Helpers::preprare_save( $updated );

			if ( is_array( $cleaned ) ) {
				$cleaned = array_filter( $cleaned );
			}

			if ( $field['repeatable'] ) {
				delete_metadata( $meta_box['object_type'], $object_id, $key );

				foreach ( $cleaned as $i => $value ) {
					if ( 'i-x' === $i ) {
						continue;
					}

					add_metadata( $meta_box['object_type'], $object_id, $key, $value );
				}
			} else {
				if ( ( ! $stored && ! $cleaned ) || $stored === $cleaned ) {
					continue;
				}

				if ( $cleaned ) {
					update_metadata( $meta_box['object_type'], $object_id, $key, $cleaned, $stored );
				} else {
					delete_metadata( $meta_box['object_type'], $object_id, $key, $stored );
				}
			}
		}

	}


	public function can_save() {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( ! isset( $_POST[ 'themeplate_' . $this->config['id'] . '_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'themeplate_' . $this->config['id'] . '_nonce' ], 'save_themeplate_' . $this->config['id'] ) ) {
			return false;
		}

		return true;

	}

}
