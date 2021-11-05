<?php

namespace ThemePlate\Cleaner\Features;

use ThemePlate\Cleaner\BaseFeature;

class ExtraStyles extends BaseFeature {

	public function key(): string {

		return 'extra_styles';

	}


	public function action(): void {

		// Remove injected recent comments sidebar widget style
		add_filter( 'show_recent_comments_widget_style', '__return_false' );

		// Remove tag cloud inline style
		add_filter( 'wp_generate_tag_cloud', array( $this, 'tag_cloud_inline_style' ) );

		// Remove injected gallery shortcode style
		add_filter( 'use_default_gallery_style', '__return_false' );

	}


	public function tag_cloud_inline_style( $tag_string ) {

		return preg_replace( '/style="font-size:.+pt;"/', '', $tag_string );

	}

}
