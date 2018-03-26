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
			$this->tpmb = new ThemePlate_MetaBox( 'post', $config );
		} catch( Exception $e ) {
			return false;
		}

		add_action( 'add_meta_boxes', array( $this, 'create' ) );
		add_action( 'save_post', array( $this, 'save' ) );

	}


	public function create() {

		$meta_box = $this->tpmb->get_config();
		$post_id = get_the_ID();
		$this->tpmb->object_id( $post_id );

		if ( ! ThemePlate_Helpers::should_display( $meta_box, $post_id ) ) {
			return;
		}

		$defaults = array(
			'screen'   => '',
			'context'  => 'advanced',
			'priority' => 'default'
		);
		$meta_box = wp_parse_args( $meta_box, $defaults );

		add_meta_box( 'themeplate_' . $meta_box['id'], $meta_box['title'], array( $this->tpmb, 'layout_inside' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'] );

	}


	public function save( $post_id ) {

		if ( 'page' == $_POST['post_type'] ) {
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

}
