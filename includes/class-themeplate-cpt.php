<?php

/**
 * Setup custom posts and taxonomies
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_CPT {

	private $config;

	private $cpt_defaults = array(
		'args' => array()
	);

	private $args_defaults = array(
		'labels' => array(),
		'public' => true
	);


	public function __construct( $kind, $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			return false;
		}

		if ( ! array_key_exists( 'name', $config ) ||
			! array_key_exists( 'plural', $config ) ||
			! array_key_exists( 'singular', $config )
		) {
			return false;
		}

		if ( $kind == 'taxonomy' && ! array_key_exists( 'type', $config ) ) {
			return false;
		}

		$this->config = ThemePlate_Helpers::fool_proof( $this->cpt_defaults, $config );
		$this->$kind( $this->config );

	}


	public function post_type( $config ) {

		$plural = $config['plural'];
		$singular = $config['singular'];
		$args = ThemePlate_Helpers::fool_proof( $this->args_defaults, $config['args'] );

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

		$args['labels'] = array_merge( $labels, $args['labels'] );

		register_post_type( $config['name'], $args );

		add_filter( 'post_updated_messages', array( $this, 'custom_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_custom_messages' ), 10, 2 );

	}


	public function taxonomy( $config ) {

		$plural = $config['plural'];
		$singular = $config['singular'];
		$args = ThemePlate_Helpers::fool_proof( $this->args_defaults, $config['args'] );

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

		$args['labels'] = array_merge( $labels, $args['labels'] );

		register_taxonomy( $config['name'], $config['type'], $args );

	}


	public function custom_messages( $messages ) {

		global $post_type_object, $post;

		$name = $this->config['name'];
		$singular = $this->config['singular'];

		$post_ID = isset( $post_ID ) ? (int) $post_ID : 0;
		$permalink = get_permalink( $post_ID );

		if ( ! $permalink ) {
			$permalink = '';
		}

		$preview_post_link_html = $scheduled_post_link_html = $view_post_link_html = '';
		$preview_url = get_preview_post_link( $post );
		$viewable = is_post_type_viewable( $post_type_object );

		if ( $viewable ) {
			$preview_post_link_html = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>',
				esc_url( $preview_url ),
				__( 'Preview ' . $singular )
			);

			$scheduled_post_link_html = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				__( 'Preview ' . $singular )
			);

			$view_post_link_html = sprintf( ' <a href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				__( 'View ' . $singular )
			);
		}

		$scheduled_date = date_i18n( __( 'M j, Y @ H:i' ), strtotime( $post->post_date ) );

		$messages[$name] = array(
			 0 => '', // Unused. Messages start at index 1.
			 1 => __( $singular . ' updated.' ) . $view_post_link_html,
			 2 => __( 'Custom field updated.' ),
			 3 => __( 'Custom field deleted.' ),
			 4 => __( $singular . ' updated.' ),
			 5 => isset( $_GET['revision'] ) ? sprintf( __( $singular . ' restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			 6 => __( $singular . ' published.' ) . $view_post_link_html,
			 7 => __( $singular . ' saved.' ),
			 8 => __( $singular . ' submitted.' ) . $preview_post_link_html,
			 9 => sprintf( __( $singular . ' scheduled for: %s.' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_post_link_html,
			10 => __( $singular . ' draft updated.' ) . $preview_post_link_html
		);

		return $messages;

	}


	public function bulk_custom_messages( $messages, $counts ) {

		$name = $this->config['name'];
		$singular = $this->config['singular'];
		$plural = $this->config['plural'];

		$messages[$name] = array(
			'updated'   => _n( '%s ' . $singular . ' updated.', '%s ' . $plural . ' updated.', $counts['updated'] ),
			'locked'    => _n( '%s ' . $singular . ' not updated, somebody is editing it.', '%s ' . $plural . ' not updated, somebody is editing them.', $counts['locked'] ),
			'deleted'   => _n( '%s ' . $singular . ' permanently deleted.', '%s ' . $plural . ' permanently deleted.', $counts['deleted'] ),
			'trashed'   => _n( '%s ' . $singular . ' moved to the Trash.', '%s ' . $plural . ' moved to the Trash.', $counts['trashed'] ),
			'untrashed' => _n( '%s ' . $singular . ' restored from the Trash.', '%s ' . $plural . ' restored from the Trash.', $counts['untrashed'] )
		);

		return $messages;

	}

}
