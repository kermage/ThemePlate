<?php

/**
 * Setup custom post types
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_PostTypes {

	private static $instance;


	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	public function __construct() {


	}


	public function add_type( $param ) {
		$plural = $param['plural'];
		$singular = $param['singular'];
		$args = $param['args'];

		$labels = array(
			'name'                  => _x( $plural, 'Post Type General Name', 'themeplate' ),
			'singular_name'         => _x( $singular, 'Post Type Singular Name', 'themeplate' ),
			'add_new'               => __( 'Add New ' . $singular, 'themeplate' ),
			'add_new_item'          => __( 'Add New ' . $singular, 'themeplate' ),
			'edit_item'             => __( 'Edit ' . $singular, 'themeplate' ),
			'new_item'              => __( 'New ' . $singular, 'themeplate' ),
			'view_item'             => __( 'View ' . $singular, 'themeplate' ),
			'update_item'           => __( 'Update ' . $singular, 'themeplate' ),
			'search_items'          => __( 'Search ' . $singular, 'themeplate' ),
			'not_found'             => __( $singular . ' not found', 'themeplate' ),
			'not_found_in_trash'    => __( $singular . ' not found in Trash', 'themeplate' ),
			'parent_item_colon'     => __( 'Parent ' . $singular . ':', 'themeplate' ),
			'all_items'             => __( 'All ' . $plural, 'themeplate' ),
			'archives'              => __( $singular . ' Archives', 'themeplate' ),
			'insert_into_item'      => __( 'Insert into ' . $singular, 'themeplate' ),
			'uploaded_to_this_item' => __( 'Uploaded to this ' . $singular, 'themeplate' ),
			'featured_image'        => __( $singular . ' Featured Image', 'themeplate' ),
			'set_featured_image'    => __( 'Set ' . $singular . ' Featured Image', 'themeplate' ),
			'remove_featured_image' => __( 'Remove ' . $singular . ' Featured Image', 'themeplate' ),
			'use_featured_image'    => __( 'Use as ' . $singular . ' Featured Image', 'themeplate' ),
			'menu_name'             => __( $plural, 'themeplate' ),
			'name_admin_bar'        => __( $plural, 'themeplate' )
		);
		$defaults = array(
			'label'       => __( $plural, 'themeplate' ),
			'labels'      => $labels,
			'description' => __( $param['description'], 'themeplate' )
		);

		if ( is_array( $param['tax'] ) ) {
			foreach ( $param['tax'] as $tax_name => $tax ) {
				themeplate_add_taxonomy( array(
					'name'        => $tax_name,
					'plural'      => __( $tax['plural'], 'themeplate' ),
					'singular'    => __( $tax['singular'], 'themeplate' ),
					'description' => __( $tax['description'], 'themeplate' ),
					'type'        => $name,
					'args'        => $tax['args']
				) );
			}
		}

		register_post_type( $param['name'], wp_parse_args( $args, $defaults ) );
	}


	public function add_tax( $param ) {
		$plural = $param['plural'];
		$singular = $param['singular'];
		$args = $param['args'];

		$labels = array(
			'name'                       => _x( $plural, 'Taxonomy General Name', 'themeplate' ),
			'singular_name'              => _x( $singular, 'Taxonomy Singular Name', 'themeplate' ),
			'menu_name'                  => __( $plural, 'themeplate' ),
			'all_items'                  => __( 'All ' . $plural, 'themeplate' ),
			'edit_item'                  => __( 'Edit ' . $singular, 'themeplate' ),
			'view_item'                  => __( 'View ' . $singular, 'themeplate' ),
			'update_item'                => __( 'Update ' . $singular, 'themeplate' ),
			'add_new_item'               => __( 'Add New ' . $singular, 'themeplate' ),
			'new_item_name'              => __( 'New ' . $singular . ' Name', 'themeplate' ),
			'parent_item'                => __( 'Parent ' . $singular, 'themeplate' ),
			'parent_item_colon'          => __( 'Parent ' . $singular . ':', 'themeplate' ),
			'search_items'               => __( 'Search ' . $singular, 'themeplate' ),
			'popular_items'              => __( 'Popular ' . $singular, 'themeplate' ),
			'separate_items_with_commas' => __( 'Separate ' . $plural . ' with commas', 'themeplate' ),
			'add_or_remove_items'        => __( 'Add or remove ' . $plural, 'themeplate' ),
			'choose_from_most_used'      => __( 'Choose from the most used ' . $singular, 'themeplate' ),
			'not_found'                  => __( $singular . ' not found', 'themeplate' )
		);
		$defaults = array(
			'label'       => __( $plural, 'themeplate' ),
			'labels'      => $labels,
			'description' => __( $param['description'], 'themeplate' )
		);

		register_taxonomy( $param['name'], $param['type'], wp_parse_args( $args, $defaults ) );
	}

}

ThemePlate_PostTypes::instance();
