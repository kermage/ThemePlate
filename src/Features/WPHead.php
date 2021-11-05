<?php

namespace ThemePlate\Cleaner\Features;

use ThemePlate\Cleaner\BaseFeature;

class WPHead extends BaseFeature {

	public function key(): string {

		return 'wp_head';

	}


	public function action(): void {

		// Display the link to the Really Simple Discovery service endpoint.
		remove_action( 'wp_head', 'rsd_link' );
		// Display the link to the Windows Live Writer manifest file.
		remove_action( 'wp_head', 'wlwmanifest_link' );
		// Display relational links for the posts adjacent to the current post for single post pages.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		// Output rel=canonical for singular queries.
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'embed_head', 'rel_canonical' );
		// Inject rel=shortlink into head if a shortlink is defined for the current page.
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		// Display the XHTML generator that is generated on the wp_head hook
		remove_action( 'wp_head', 'wp_generator' );

		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'wp_head', 'rest_output_link_wp_head' );

		remove_action( 'wp_head', 'feed_links_extra', 3 );

		// Remove the link to comments feed
		add_filter( 'feed_links_show_comments_feed', '__return_false' );

		add_filter( 'the_generator', '__return_false' );

	}

}
