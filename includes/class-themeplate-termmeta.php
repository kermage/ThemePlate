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
			if ( empty( $config['taxonomy'] ) ) {
				$taxonomies = get_taxonomies( array( '_builtin' => false ) );
				$taxonomies[] = 'category';
				$taxonomies[] = 'post_tag';
				$config['taxonomy'] = $taxonomies;
			} else {
				$taxonomies = $config['taxonomy'];
			}

			$defaults = array(
				'taxonomy' => array()
			);
			$config = ThemePlate_Helpers::fool_proof( $defaults, $config );
			$config['object_type'] = 'term';
			$this->tpmb = new ThemePlate_MetaBox( $config );
		} catch ( Exception $e ) {
			return false;
		}

		foreach ( (array) $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form', array( $this, 'create' ) );
			add_action( $taxonomy . '_edit_form', array( $this, 'create' ) );
			add_action( 'created_' . $taxonomy, array( $this, 'save' ) );
			add_action( 'edited_' . $taxonomy, array( $this, 'save' ) );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );

	}


	public function create( $tag ) {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$term_id = is_object( $tag ) ? $tag->term_id : '';

		$this->tpmb->layout_postbox( $term_id );

	}


	public function save( $term_id ) {

		if ( ! $this->tpmb->can_save() ) {
			return;
		}

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		$this->tpmb->save( $term_id );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		wp_enqueue_script( 'post' );
		wp_enqueue_media();

		$this->tpmb->enqueue();

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( ! in_array( $screen->base, array( 'edit-tags', 'term' ), true ) ) {
			return false;
		}

		$meta_box = $this->tpmb->get_config();

		if ( ! in_array( $screen->taxonomy, $meta_box['taxonomy'], true ) ) {
			return false;
		}

		if ( $screen->base === 'edit-tags' && ! ThemePlate_Helpers::should_display( $meta_box, '' ) ) {
			return false;
		}

		if ( $screen->base === 'term' && ! ThemePlate_Helpers::should_display( $meta_box, $_REQUEST['tag_ID'] ) ) {
			return false;
		}

		return true;

	}

}
