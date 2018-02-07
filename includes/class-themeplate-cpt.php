<?php

/**
 * Setup custom posts and taxonomies
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_CPT {

	private $param;


	public function __construct( $kind, $param ) {

		if ( ! is_array( $param ) || empty( $param ) ) {
			return false;
		}

		if ( ! array_key_exists( 'name', $param ) ||
			! array_key_exists( 'plural', $param ) ||
			! array_key_exists( 'singular', $param )
		) {
			return false;
		}

		if ( $kind == 'taxonomy' && ! array_key_exists( 'type', $param ) ) {
			return false;
		}

		$this->$kind( $param );

		$this->param = $param;

		add_filter( 'post_updated_messages', array( $this, 'custom_messages' ) );

	}


	public function post_type( $param ) {

		$plural = $param['plural'];
		$singular = $param['singular'];
		$args = $param['args'];

		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => 'Add New ' . $singular,
			'add_new_item'          => 'Add New ' . $singular,
			'edit_item'             => 'Edit ' . $singular,
			'new_item'              => 'New ' . $singular,
			'view_item'             => 'View ' . $singular,
			'update_item'           => 'Update ' . $singular,
			'search_items'          => 'Search ' . $singular,
			'not_found'             => $singular . ' not found',
			'not_found_in_trash'    => $singular . ' not found in Trash',
			'parent_item_colon'     => 'Parent ' . $singular . ':',
			'all_items'             => 'All ' . $plural,
			'archives'              => $singular . ' Archives',
			'insert_into_item'      => 'Insert into ' . $singular,
			'uploaded_to_this_item' => 'Uploaded to this ' . $singular,
			'featured_image'        => $singular . ' Featured Image',
			'set_featured_image'    => 'Set ' . $singular . ' Featured Image',
			'remove_featured_image' => 'Remove ' . $singular . ' Featured Image',
			'use_featured_image'    => 'Use as ' . $singular . ' Featured Image',
			'menu_name'             => $plural,
			'name_admin_bar'        => $plural
		);
		$defaults = array(
			'label'       => $plural,
			'labels'      => $labels,
			'description' => $param['description'],
			'public'      => true
		);

		register_post_type( $param['name'], wp_parse_args( $args, $defaults ) );

	}


	public function taxonomy( $param ) {

		$plural = $param['plural'];
		$singular = $param['singular'];
		$args = $param['args'];

		$labels = array(
			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => 'All ' . $plural,
			'edit_item'                  => 'Edit ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'new_item_name'              => 'New ' . $singular . ' Name',
			'parent_item'                => 'Parent ' . $singular,
			'parent_item_colon'          => 'Parent ' . $singular . ':',
			'search_items'               => 'Search ' . $singular,
			'popular_items'              => 'Popular ' . $singular,
			'separate_items_with_commas' => 'Separate ' . $plural . ' with commas',
			'add_or_remove_items'        => 'Add or remove ' . $plural,
			'choose_from_most_used'      => 'Choose from the most used ' . $singular,
			'not_found'                  => $singular . ' not found'
		);
		$defaults = array(
			'label'       => $plural,
			'labels'      => $labels,
			'description' => $param['description'],
			'public'      => true
		);

		register_taxonomy( $param['name'], $param['type'], wp_parse_args( $args, $defaults ) );

	}


	public function custom_messages( $messages ) {

		global $post, $post_ID;

		$messages[$this->param['name']] = array(
			 0 => '',
			 1 => $this->param['singular'] . ' updated.',
			 2 => '',
			 3 => '',
			 4 => $this->param['singular'] . ' updated.',
			 5 => $this->param['singular'] . ' restored to revision.',
			 6 => $this->param['singular'] . ' published.',
			 7 => $this->param['singular'] . ' saved.',
			 8 => $this->param['singular'] . ' submitted.',
			 9 => $this->param['singular'] . ' scheduled.',
			10 => $this->param['singular'] . ' draft updated.'
		);

		return $messages;

	}

}
