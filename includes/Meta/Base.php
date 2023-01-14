<?php

/**
 * Setup meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Legacy\Meta;

use ThemePlate\Legacy\Core\Form;
use ThemePlate\Legacy\Core\Helper\Box;
use ThemePlate\Legacy\Core\Helper\Main;
use ThemePlate\Legacy\Core\Helper\Meta;

abstract class Base {

	protected $config;
	protected $form;


	public function __construct( $config ) {

		$expected = array(
			'object_type',
			'id',
			'title',
		);

		if ( ! Main::is_complete( $config, $expected ) ) {
			throw new \Exception();
		}

		try {
			$this->form = new Form( $config );
		} catch ( \Exception $e ) {
			throw new \Exception( $e );
		}

		$config['fields'] = $this->form->get_fields();

		$defaults     = array(
			'show_on' => array(),
			'hide_on' => array(),
		);
		$this->config = Main::fool_proof( $defaults, $config );
		$this->config = Meta::normalize_options( $this->config );

	}


	public function get_config() {

		return $this->config;

	}


	public function save( $object_id ) {

		$meta_box = $this->config;
		$not_menu = true;

		if ( 'menu' === $meta_box['object_type'] ) {
			$meta_box['object_type'] = 'post';

			$not_menu = false;
		}

		foreach ( $meta_box['fields'] as $id => $field ) {
			$key = $meta_box['id'] . '_' . $id;

			if ( ! isset( $_POST['themeplate'][ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				continue;
			}

			$stored  = get_metadata( $meta_box['object_type'], $object_id, $key, ! $field['repeatable'] );
			$updated = $not_menu ? $_POST['themeplate'][ $key ] : $_POST['themeplate'][ $key ][ $object_id ]; // phpcs:ignore WordPress.Security.NonceVerification
			$cleaned = Box::prepare_save( $updated );

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

		$index = 'themeplate_' . $this->config['id'] . '_nonce';

		return ! ( ! isset( $_POST[ $index ] ) || ! wp_verify_nonce( $_POST[ $index ], 'save_themeplate_' . $this->config['id'] ) );

	}


	public function columns() {

		$meta_box = $this->config;

		foreach ( $this->form->get_fields() as $id => $field ) {
			if ( ! $field['column'] ) {
				continue;
			}

			$field['id']          = $meta_box['id'] . '_' . $id;
			$field['object_type'] = $meta_box['object_type'];

			$args = array(
				'id'            => $field['id'],
				'title'         => $field['title'],
				'callback'      => array( Meta::class, 'display_column' ),
				'callback_args' => $field,
			);

			$this->column_data( $args );
		}

	}


	abstract protected function column_data( $args );

}
