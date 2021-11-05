<?php

namespace ThemePlate\Cleaner\Features;

use ThemePlate\Cleaner\BaseFeature;

class EmojiDetection extends BaseFeature {

	public function key(): string {

		return 'emoji_detection';

	}


	public function action(): void {

		// Emoji support detection script and styles
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'embed_head', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		// Remove URL where emoji SVG images are hosted
		add_filter( 'emoji_svg_url', '__return_false' );

	}

}
