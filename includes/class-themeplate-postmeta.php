<?php

/**
 * Setup post meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_PostMeta {

	private $tpmb;


	public function __construct( $config ) {

		try {
			$config['object_type'] = 'post';
			$this->tpmb = new ThemePlate_MetaBox( $config );
		} catch( Exception $e ) {
			return false;
		}

		add_action( 'add_meta_boxes', array( $this, 'create' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function create() {

		$meta_box = $this->tpmb->get_config();
		$defaults = array(
			'screen'   => '',
			'context'  => 'advanced',
			'priority' => 'default'
		);
		$meta_box = wp_parse_args( $meta_box, $defaults );

		if ( ! $this->is_valid( $meta_box['screen'] ) ) {
			return;
		}

		$post_id = get_the_ID();

		if ( ! ThemePlate_Helpers::should_display( $meta_box, $post_id ) ) {
			return;
		}

		add_meta_box( 'themeplate_' . $meta_box['id'], $meta_box['title'], array( $this, 'add' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'] );

	}


	public function add() {

		$this->tpmb->layout_inside( get_the_ID() );

	}


	public function save( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( ! $this->tpmb->can_save() ) {
			return;
		}

		if ( $post_type == 'page' ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		$this->tpmb->save( $post_id );

	}


	public function scripts_styles() {

		$screen = get_current_screen();

		if ( $screen->base != 'post' ) {
			return;
		}

		$this->tpmb->enqueue();

	}


	private function is_valid( $types ) {

		$screen = get_current_screen();

		if ( ! empty( $types ) && ! in_array( $screen->post_type, $types ) ) {
			return false;
		}

		return true;

	}

}
