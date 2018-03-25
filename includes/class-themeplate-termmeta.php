<?php

/**
 * Setup term meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_TermMeta {

	private $tpmb;


	public function __construct( $config ) {

		try {
			$this->tpmb = new ThemePlate_MetaBox( 'term', $config );
		} catch( Exception $e ) {
			return false;
		}

		if ( empty( $config['taxonomy'] ) ) {
			$taxonomies = get_taxonomies( array( '_builtin' => false ) );
			$taxonomies['category'] = 'category';
			$taxonomies['post_tag'] = 'post_tag';
		} else {
			$taxonomies = $config['taxonomy'];
		}

		foreach ( (array) $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form', array( $this, 'create' ) );
			add_action( $taxonomy . '_edit_form', array( $this, 'create' ) );
			add_action( 'created_' . $taxonomy, array( $this, 'save' ) );
			add_action( 'edited_' . $taxonomy, array( $this, 'save' ) );
		}

	}


	public function create( $tag ) {

		$meta_box = $this->tpmb->config;
		$term_id = is_object( $tag ) ? $tag->term_id : '';
		$this->tpmb->object_id = $term_id;

		if ( ! ThemePlate_Helpers::should_display( $meta_box, $term_id ) ) {
			return;
		}

		wp_enqueue_script( 'post' );
		wp_enqueue_media();

		$this->tpmb->layout_postbox();

	}


	public function save( $term_id ) {

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		$this->tpmb->save( $term_id );

	}

}
