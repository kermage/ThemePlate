<?php

/**
 * Setup term meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Meta;

use ThemePlate\Column;
use ThemePlate\Core\Helper\Box;
use ThemePlate\Core\Helper\Main;
use ThemePlate\Core\Helper\Meta;

class Term extends Base {

	public function __construct( $config ) {

		$config['object_type'] = 'term';

		try {
			parent::__construct( $config );
		} catch ( \Exception $e ) {
			throw new \Exception( $e );
		}

		if ( empty( $config['taxonomy'] ) ) {
			$taxonomies   = get_taxonomies( array( '_builtin' => false ) );
			$taxonomies[] = 'category';
			$taxonomies[] = 'post_tag';

			$this->config['taxonomy'] = $taxonomies;
		}

		$defaults = array(
			'taxonomy' => array(),
			'priority' => 'default',
		);

		$this->config = Main::fool_proof( $defaults, $this->config );

		$priority = Box::get_priority( $this->config );

		foreach ( $this->config['taxonomy'] as $taxonomy ) {
			add_action( $taxonomy . '_add_form', array( $this, 'create' ), $priority );
			add_action( $taxonomy . '_edit_form', array( $this, 'create' ), $priority );
			add_action( 'created_' . $taxonomy, array( $this, 'save' ) );
			add_action( 'edited_' . $taxonomy, array( $this, 'save' ) );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ), 11 );

		$this->columns();

	}


	public function create( $tag ) {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$term_id = is_object( $tag ) ? $tag->term_id : '';

		$this->form->layout_postbox( $term_id );

	}


	public function save( $term_id ) {

		if ( ! $this->can_save() ) {
			return;
		}

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		parent::save( $term_id );

	}


	public function scripts_styles() {

		if ( ! $this->is_valid_screen() ) {
			return;
		}

		$this->form->enqueue( 'term' );

	}


	private function is_valid_screen() {

		$screen = get_current_screen();

		if ( null === $screen || ! in_array( $screen->base, array( 'edit-tags', 'term' ), true ) ) {
			return false;
		}

		$meta_box = $this->config;

		if ( ! in_array( $screen->taxonomy, $meta_box['taxonomy'], true ) ) {
			return false;
		}

		if ( 'edit-tags' === $screen->base && ! Meta::should_display( $meta_box, '' ) ) {
			return false;
		}

		if ( 'term' === $screen->base && ! Meta::should_display( $meta_box, $_REQUEST['tag_ID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return false;
		}

		return true;

	}


	protected function column_data( $args ) {

		foreach ( $this->config['taxonomy'] as $taxonomy ) {
			$args['taxonomy'] = $taxonomy;

			new Column( $args );
		}

	}

}
