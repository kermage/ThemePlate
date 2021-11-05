<?php

namespace ThemePlate\Cleaner\Features;

use ThemePlate\Cleaner\BaseFeature;

class DependencyTag extends BaseFeature {

	public function key(): string {

		return 'dependency_tag';

	}


	public function action(): void {

		if ( is_admin() ) {
			return;
		}

		// Output of <link> and <script> tags

		if ( $this->enabled( 'style' ) ) {
			add_filter( 'style_loader_tag', array( $this, 'style_tag' ) );
		}

		if ( $this->enabled( 'script' ) ) {
			add_filter( 'script_loader_tag', array( $this, 'script_tag' ) );
		}

	}


	public function style_tag( $input ): string {

		preg_match_all( "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(\S+)'\s?(type='text/css')?\s+media='(.*)' />!", $input, $matches ); // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		// Only display media if it is meaningful

		$media = '';
		if ( '' !== $matches[4][0] && 'all' !== $matches[4][0] ) {
			$media = ' media="' . $matches[4][0] . '"';
		}
		return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n"; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet

	}


	public function script_tag( $input ): string {

		$input = preg_replace( "/ id='[^']+'/", '', $input );

		return str_replace( "type='text/javascript' ", '', $input );

	}

}
