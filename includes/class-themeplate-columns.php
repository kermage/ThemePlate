<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Columns {

	private $config;


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			throw new Exception();
		}

		if ( ! array_key_exists( 'post_type', $config ) || ! array_key_exists( 'id', $config ) || ! array_key_exists( 'title', $config ) || ! array_key_exists( 'callback', $config ) ) {
			throw new Exception();
		}

		$this->config = $config;

		add_filter( 'manage_' . $config['post_type'] . '_posts_columns', array( $this, 'modify' ), 10 );
		add_action( 'manage_' . $config['post_type'] . '_posts_custom_column', array( $this, 'populate' ), 10, 2 );

	}


	public function modify( $columns ) {

		$config = $this->config;

		$columns[$config['id']] = $config['title'];

		return $columns;

	}


	public function populate( $column_name, $post_id ) {

		$config = $this->config;

		if ( $column_name !== $config['id'] ) {
			return;
		}

		return call_user_func( $config['callback'], $post_id );

	}

}
