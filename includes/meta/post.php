<?php

/**
 * Setup post meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Meta_Post extends ThemePlate_Meta_Base {

	public function __construct( $config ) {

		$config['object_type'] = 'post';

		try {
			parent::__construct( $config );
		} catch ( Exception $e ) {
			throw new Exception( $e );
		}

		$defaults = array(
			'screen'        => array(),
			'context'       => 'advanced',
			'priority'      => 'default',
			'callback_args' => array(),
		);

		$this->config = ThemePlate_Helper_Main::fool_proof( $defaults, $this->config );

		add_action( 'add_meta_boxes', array( $this, 'create' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ), 11 );
		add_action( 'load-edit.php', array( $this, 'columns' ) );

	}


	public function create() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$meta_box = $this->config;

		if ( 'after_title' === $meta_box['context'] && function_exists( 'use_block_editor_for_post' ) && use_block_editor_for_post( get_the_ID() ) ) {
			$meta_box['context'] = 'normal';
		}

		add_meta_box( 'themeplate_' . $meta_box['id'], $meta_box['title'], array( $this, 'add' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'], $meta_box['callback_args'] );

	}


	public function add() {

		$this->form->layout_inside( get_the_ID() );

	}


	public function save( $post_id ) {

		if ( ! $this->can_save() ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'page' === $post_type && ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		parent::save( $post_id );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->form->enqueue( 'post' );

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( 'post' !== $screen->base ) {
			return false;
		}

		$meta_box = $this->config;

		if ( ! empty( $meta_box['screen'] ) && ! in_array( $screen->post_type, $meta_box['screen'], true ) ) {
			return false;
		}

		if ( ! ThemePlate_Helper_Meta::should_display( $meta_box, get_the_ID() ) ) {
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

			$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;

			$args  = array(
				'id'            => $field['id'],
				'title'         => $meta_box['title'] . ': ' . $field['name'],
				'callback'      => array( 'ThemePlate_Helper_Meta', 'display_column' ),
				'callback_args' => $field,
			);

			if ( empty( $meta_box['screen'] ) ) {
				$screen = get_current_screen();

				$meta_box['screen'][] = $screen->post_type;
			}

			foreach ( $meta_box['screen'] as $post_type ) {
				$args['post_type'] = $post_type;

				new ThemePlate_Columns( $args );
			}
		}

	}

}
