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

		$expected = array(
			'object_type',
			'id',
			'title',
		);

		if ( ! ThemePlate_Helper_Main::is_complete( $config, $expected ) ) {
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
		$this->config = ThemePlate_Helper_Main::fool_proof( $defaults, $config );
		$this->config = ThemePlate_Helper_Meta::normalize_options( $this->config );

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
			$cleaned = ThemePlate_Helper_Box::prepare_save( $updated );

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


	public function columns() {

		$meta_box = $this->config;
		$fields   = $this->form->get_fields();

		foreach ( $fields as $id => $field ) {
			if ( ! $field['column'] ) {
				continue;
			}

			$field['id']          = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;
			$field['object_type'] = $meta_box['object_type'];

			$args  = array(
				'id'            => $field['id'],
				'title'         => $meta_box['title'] . ': ' . $field['title'],
				'callback'      => array( 'ThemePlate_Helper_Meta', 'display_column' ),
				'callback_args' => $field,
			);

			$this->column_data( $args );
		}

	}

}
