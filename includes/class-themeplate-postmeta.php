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
			$defaults = array(
				'screen'   => array(),
				'context'  => 'advanced',
				'priority' => 'default'
			);
			$config   = ThemePlate_Helpers::fool_proof( $defaults, $config );

			$config['object_type'] = 'post';

			$this->tpmb = new ThemePlate_MetaBox( $config );
		} catch ( Exception $e ) {
			return false;
		}

		add_action( 'add_meta_boxes', array( $this, 'create' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function create() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$meta_box = $this->tpmb->get_config();

		add_meta_box( 'themeplate_' . $meta_box['id'], $meta_box['title'], array( $this, 'add' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'] );

	}


	public function add() {

		$this->tpmb->layout_inside( get_the_ID() );

	}


	public function save( $post_id ) {

		if ( ! $this->tpmb->can_save() ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( $post_type === 'page' && ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$this->tpmb->save( $post_id );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->tpmb->enqueue();

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( $screen->base !== 'post' ) {
			return false;
		}

		$meta_box = $this->tpmb->get_config();

		if ( ! in_array( $screen->post_type, $meta_box['screen'], true ) ) {
			return false;
		}

		if ( ! ThemePlate_Helpers::should_display( $meta_box, get_the_ID() ) ) {
			return false;
		}

		return true;

	}

}
