<?php

/**
 * WordPress markup cleaner
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Cleaner {

	private static $instance;


	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	public function __construct() {

		// // Cleanup wp_head()
		// Display the links to the extra feeds such as category feeds.
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		// Display the link to the Really Simple Discovery service endpoint.
		remove_action( 'wp_head', 'rsd_link' );
		// Display the link to the Windows Live Writer manifest file.
		remove_action( 'wp_head', 'wlwmanifest_link' );
		// Display relational links for the posts adjacent to the current post for single post pages.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		// Output rel=canonical for singular queries.
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'embed_head', 'rel_canonical' );
		// Inject rel=shortlink into head if a shortlink is defined for the current page.
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
		// Display the XHTML generator that is generated on the wp_head hook
		remove_action( 'wp_head', 'wp_generator' );
		// Emoji support detection script and styles
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'embed_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji ');
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji ');
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

		// Remove the WordPress version from RSS feeds
		add_filter( 'the_generator', '__return_false' );

		// Remove the link to comments feed
		add_filter( 'feed_links_show_comments_feed', '__return_false' );

		// Query strings from static resources
		add_filter( 'style_loader_src', array( $this, 'query_strings' ), 15 );
		add_filter( 'script_loader_src', array( $this, 'query_strings' ), 15 );

		// Output of <link> and <script> tags
		add_filter( 'style_loader_tag', array( $this, 'style_tag' ) );
		add_filter( 'script_loader_tag', array( $this, 'script_tag' ) );

		// Remove unnecessary body and post classes
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'post_class', array( $this, 'post_class' ) );

		// Remove injected recent comments sidebar widget style
		add_filter( 'show_recent_comments_widget_style', '__return_false' );

		// Remove tag cloud inline style
		add_filter( 'wp_generate_tag_cloud', array( $this, 'tag_cloud_inline_style' ) );

		// Remove injected gallery shortcode style
		add_filter( 'use_default_gallery_style', '__return_false' );

		// Remove URL where emoji SVG images are hosted
		add_filter( 'emoji_svg_url', '__return_false' );

		// Wrap embedded media for easier responsive styling
		add_filter( 'embed_oembed_html', array( $this, 'embed_wrap' ) );

	}


	public function query_strings( $src ) {

		return remove_query_arg( 'ver', $src );

	}


	public function style_tag( $input ) {

		preg_match_all( "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches );
		// Only display media if it is meaningful
		if ( $matches[3][0] !== '' && $matches[3][0] !== 'all' ) {
			$media = ' media="' . $matches[3][0] . '"';
		}
		return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";

	}


	public function script_tag( $input ) {

		$input = str_replace( "type='text/javascript' ", '', $input );
		return str_replace( "'", '"', $input );

	}


	public function body_class( $classes ) {

		$match = '(^(postid|attachmentid|page-id|parent-pageid|category|tag|term)-\d+$|(attachment|page-parent|page-child)$)';
		$match .= '|(^(page|post|single|category|tag|archive|post-type-archive)$)';
		$match .= '|(^.*-(template(-default)?(-page-templates)?(-[\w-]+-php)?)$)';
		$match = '/' . $match . '/';
		foreach ( $classes as $key => $value ) {
			if ( preg_match( $match, $value ) ) {
				unset( $classes[$key] );
			}
		}
		return $classes;

	}


	public function post_class( $classes ) {

		$match = '/(post-\d+$|(type|status|format)-[\w-]+$)/';
		foreach ( $classes as $key => $value ) {
			if ( preg_match( $match, $value ) ) {
				unset( $classes[$key] );
			}
		}
		return $classes;

	}


	public function tag_cloud_inline_style( $tag_string ) {

		return preg_replace( '/style="font-size:.+pt;"/', '', $tag_string );

	}


	public function embed_wrap( $cache ) {

		return '<div class="embed-responsive">' . $cache . '</div>';

	}

}
