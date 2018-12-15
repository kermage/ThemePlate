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

		$expected = array(
			'id',
			'title',
			'callback',
			array(
				'post_type',
				'taxonomy',
				'users',
			),
		);

		if ( ! ThemePlate_Helper_Main::is_complete( $config, $expected ) ) {
			throw new Exception();
		}

		$defaults     = array(
			'position'      => 0,
			'callback_args' => array(),
		);
		$this->config = ThemePlate_Helper_Main::fool_proof( $defaults, $config );

		$context = $this->context();

		add_filter( 'manage_' . $context['modify'] . '_columns', array( $this, 'modify' ), 10 );
		add_action( 'manage_' . $context['populate'] . '_custom_column', array( $this, 'populate' ), 10, 2 );

	}


	private function context() {

		$config  = $this->config;
		$context = array();

		if ( ! empty( $config['post_type'] ) ) {
			$context['type']   = 'post_type';
			$context['modify'] = $context['populate'] = $config['post_type'] . '_posts';
		} elseif ( ! empty( $config['taxonomy'] ) ) {
			$context['type']     = 'taxonomy';
			$context['modify']   = 'edit-' . $config['taxonomy'];
			$context['populate'] = $config['taxonomy'];
		} elseif ( ! empty( $config['users'] ) ) {
			$context['type']   = 'users';
			$context['modify'] = $context['populate'] = 'users';
		}

		$this->config['context'] = $context;

		return $context;

	}


	public function modify( $columns ) {

		$config = $this->config;

		$columns[ $config['id'] ] = $config['title'];

		if ( ( $position = $config['position'] ) > 0 ) {
			$item    = array_slice( $columns, -1, 1, true );
			$start   = array_slice( $columns, 0, $position, true );
			$end     = array_slice( $columns, $position, count( $columns ) - 1, true );
			$columns = $start + $item + $end;
		}

		return $columns;

	}


	public function populate( $column_name, $post_id ) {

		$config = $this->config;

		if ( $column_name !== $config['id'] ) {
			return;
		}

		return call_user_func( $config['callback'], $post_id, $config['callback_args'] );

	}

}
