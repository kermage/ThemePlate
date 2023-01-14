<?php

/**
 * Setup custom taxonomies
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Legacy\CPT;

use ThemePlate\Legacy\Core\Helper\Main;

class Taxonomy extends Base {

	public function __construct( $config ) {

		try {
			parent::__construct( 'taxonomy', $config );
		} catch ( \Exception $e ) {
			throw new \Exception( $e );
		}

	}


	public function register() {

		$config   = $this->config;
		$plural   = $config['plural'];
		$singular = $config['singular'];
		$defaults = array(
			'labels'       => array(),
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array(),
		);

		$args = Main::fool_proof( $defaults, $config['args'] );

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

		$args['labels']  = Main::fool_proof( $labels, $args['labels'] );
		$args['rewrite'] = Main::fool_proof( array( 'with_front' => false ), $args['rewrite'] );

		register_taxonomy( $config['name'], $config['type'], $args );

	}

}
