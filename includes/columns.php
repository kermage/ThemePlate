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

		foreach ( $context['list'] as $item ) {
			add_filter( 'manage_' . $item['modify'] . '_columns', array( $this, 'modify' ), 10 );
			add_action( 'manage_' . $item['populate'] . '_custom_column', array( $this, 'populate' ), 10, 3 );
		}

	}


	private function context() {

		$config  = $this->config;
		$context = array();

		if ( isset( $config['post_type'] ) ) {
			$context['type'] = 'post_type';

			if ( ! empty( $config['post_type'] ) ) {
				$context['list'][]['modify'] = $context['list'][]['populate'] = $config['post_type'] . '_posts';
			} else {
				$context['list'][0]['modify'] = $context['list'][0]['populate'] = 'posts';
				$context['list'][1]['modify'] = $context['list'][1]['populate'] = 'pages';
			}
		} elseif ( ! empty( $config['taxonomy'] ) ) {
			$context['type']               = 'taxonomy';
			$context['list'][]['modify']   = 'edit-' . $config['taxonomy'];
			$context['list'][]['populate'] = $config['taxonomy'];
		} elseif ( ! empty( $config['users'] ) ) {
			$context['type']             = 'users';
			$context['list'][]['modify'] = $context['list'][]['populate'] = 'users';
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


	public function populate( $content_name, $name_id, $object_id = null ) {

		$config = $this->config;

		if ( 'post_type' === $config['context']['type'] ) {
			$column_name = $content_name;
			$object_id   = $name_id;
		} else {
			$column_name = $name_id;
		}

		if ( $column_name !== $config['id'] ) {
			return $content_name;
		}

		if ( 'post_type' === $config['context']['type'] ) {
			return call_user_func( $config['callback'], $object_id, $config['callback_args'] );
		} else {
			ob_start();
			call_user_func( $config['callback'], $object_id, $config['callback_args'] );
			return ob_get_clean();
		}

	}

}
