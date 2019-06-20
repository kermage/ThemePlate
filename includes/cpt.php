<?php

/**
 * Setup custom posts and taxonomies
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_CPT {

	private $config;


	public function __construct( $kind, $config ) {

		$expected = array(
			'name',
			'plural',
			'singular',
		);

		if ( 'taxonomy' === $kind ) {
			$expected[] = 'type';
		}

		if ( ! ThemePlate_Helper_Main::is_complete( $config, $expected ) ) {
			throw new Exception();
		}

		$defaults     = array(
			'args' => array(),
		);
		$this->config = ThemePlate_Helper_Main::fool_proof( $defaults, $config );

		if ( did_action( 'init' ) ) {
			$this->$kind();
		} else {
			add_action( 'init', array( $this, $kind ) );
		}

	}


	public function post_type() {

		$config   = $this->config;
		$plural   = $config['plural'];
		$singular = $config['singular'];
		$defaults = array(
			'labels'       => array(),
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array(),
		);
		$args     = ThemePlate_Helper_Main::fool_proof( $defaults, $config['args'] );

		$labels = array(
			'name'                     => $plural,
			'singular_name'            => $singular,
			'add_new'                  => 'Add New ' . $singular,
			'add_new_item'             => 'Add New ' . $singular,
			'edit_item'                => 'Edit ' . $singular,
			'new_item'                 => 'New ' . $singular,
			'view_item'                => 'View ' . $singular,
			'view_items'               => 'View ' . $plural,
			'search_items'             => 'Search ' . $plural,
			'not_found'                => 'No ' . strtolower( $plural ) . ' found.',
			'not_found_in_trash'       => 'No ' . strtolower( $plural ) . ' found in Trash.',
			'parent_item_colon'        => 'Parent ' . $singular . ':',
			'all_items'                => 'All ' . $plural,
			'archives'                 => $singular . ' Archives',
			'attributes'               => $singular . ' Attributes',
			'insert_into_item'         => 'Insert into ' . strtolower( $singular ),
			'uploaded_to_this_item'    => 'Uploaded to this ' . strtolower( $singular ),
			'featured_image'           => $singular . ' Featured Image',
			'set_featured_image'       => 'Set ' . strtolower( $singular ) . ' featured image',
			'remove_featured_image'    => 'Remove ' . strtolower( $singular ) . ' featured image',
			'use_featured_image'       => 'Use as ' . strtolower( $singular ) . ' featured image',
			'filter_items_list'        => 'Filter ' . strtolower( $plural ) . ' list',
			'items_list_navigation'    => $plural . ' list navigation',
			'items_list'               => $plural . ' list',
			'item_published'           => $singular . ' published.',
			'item_published_privately' => $singular . ' published privately.',
			'item_reverted_to_draft'   => $singular . ' reverted to draft.',
			'item_scheduled'           => $singular . ' scheduled.',
			'item_updated'             => $singular . ' updated.',
			'menu_name'                => $plural,
			'name_admin_bar'           => $singular,
		);

		$args['labels']  = ThemePlate_Helper_Main::fool_proof( $labels, $args['labels'] );
		$args['rewrite'] = ThemePlate_Helper_Main::fool_proof( array( 'with_front' => false ), $args['rewrite'] );

		register_post_type( $config['name'], $args );

		add_filter( 'post_updated_messages', array( $this, 'custom_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_custom_messages' ), 10, 2 );

	}


	public function taxonomy() {

		$config   = $this->config;
		$plural   = $config['plural'];
		$singular = $config['singular'];
		$defaults = array(
			'labels'       => array(),
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array(),
		);

		$args = ThemePlate_Helper_Main::fool_proof( $defaults, $config['args'] );

		$labels = array(
			'name'                       => $plural,
			'singular_name'              => $singular,
			'search_items'               => 'Search ' . $plural,
			'popular_items'              => 'Popular ' . $plural,
			'all_items'                  => 'All ' . $plural,
			'parent_item'                => 'Parent ' . $singular,
			'parent_item_colon'          => 'Parent ' . $singular . ':',
			'edit_item'                  => 'Edit ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'new_item_name'              => 'New ' . $singular . ' Name',
			'separate_items_with_commas' => 'Separate ' . strtolower( $plural ) . ' with commas',
			'add_or_remove_items'        => 'Add or remove ' . strtolower( $plural ),
			'choose_from_most_used'      => 'Choose from the most used ' . strtolower( $singular ),
			'not_found'                  => 'No ' . strtolower( $plural ) . ' found.',
			'no_terms'                   => 'No ' . strtolower( $plural ),
			'items_list_navigation'      => $plural . ' list navigation',
			'items_list'                 => $plural . ' list',
			'most_used'                  => 'Most Used ' . $plural,
			'back_to_items'              => '&larr; Back to ' . $plural,
			'menu_name'                  => $plural,
			'name_admin_bar'             => $singular,
		);

		$args['labels']  = ThemePlate_Helper_Main::fool_proof( $labels, $args['labels'] );
		$args['rewrite'] = ThemePlate_Helper_Main::fool_proof( array( 'with_front' => false ), $args['rewrite'] );

		register_taxonomy( $config['name'], $config['type'], $args );

	}


	public function custom_messages( $messages ) {

		global $post_type_object, $post;

		$name     = $this->config['name'];
		$singular = $this->config['singular'];

		$permalink = get_permalink();

		if ( ! $permalink ) {
			$permalink = '';
		}

		$preview_post_link_html   = '';
		$scheduled_post_link_html = '';
		$view_post_link_html      = '';
		$preview_url              = get_preview_post_link( $post );
		$viewable                 = is_post_type_viewable( $post_type_object );

		if ( $viewable ) {
			$preview_post_link_html = sprintf(
				' <a target="_blank" href="%1$s">%2$s</a>',
				esc_url( $preview_url ),
				__( 'Preview ' . $singular )
			);

			$scheduled_post_link_html = sprintf(
				' <a target="_blank" href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				__( 'Preview ' . $singular )
			);

			$view_post_link_html = sprintf(
				' <a href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				__( 'View ' . $singular )
			);
		}

		$scheduled_date = date_i18n( __( 'M j, Y @ H:i' ), strtotime( $post->post_date ) );

		$messages[ $name ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( $singular . ' updated.' ) . $view_post_link_html,
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			4  => __( $singular . ' updated.' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( $singular . ' restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( $singular . ' published.' ) . $view_post_link_html,
			7  => __( $singular . ' saved.' ),
			8  => __( $singular . ' submitted.' ) . $preview_post_link_html,
			9  => sprintf( __( $singular . ' scheduled for: %s.' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_post_link_html,
			10 => __( $singular . ' draft updated.' ) . $preview_post_link_html,
		);

		return $messages;

	}


	public function bulk_custom_messages( $messages, $counts ) {

		$name     = $this->config['name'];
		$singular = strtolower( $this->config['singular'] );
		$plural   = strtolower( $this->config['plural'] );

		$messages[ $name ] = array(
			'updated'   => _n( '%s ' . $singular . ' updated.', '%s ' . $plural . ' updated.', $counts['updated'] ),
			'locked'    => _n( '%s ' . $singular . ' not updated, somebody is editing it.', '%s ' . $plural . ' not updated, somebody is editing them.', $counts['locked'] ),
			'deleted'   => _n( '%s ' . $singular . ' permanently deleted.', '%s ' . $plural . ' permanently deleted.', $counts['deleted'] ),
			'trashed'   => _n( '%s ' . $singular . ' moved to the Trash.', '%s ' . $plural . ' moved to the Trash.', $counts['trashed'] ),
			'untrashed' => _n( '%s ' . $singular . ' restored from the Trash.', '%s ' . $plural . ' restored from the Trash.', $counts['untrashed'] ),
		);

		return $messages;

	}

}
