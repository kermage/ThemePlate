<?php

namespace ThemePlate\Cleaner\Features;

use ThemePlate\Cleaner\BaseFeature;
use ThemePlate\NavWalker as ThemePlateNavWalker;

class NavWalker extends BaseFeature {

	public function key(): string {

		return 'nav_walker';

	}


	public function action(): void {

		add_filter( 'wp_nav_menu_args', array( $this, 'clean_walker' ) );

	}


	public function clean_walker( $args ) {

		if ( empty( $args['container_class'] ) && empty( $args['container_id'] ) ) {
			$args['container'] = false;
		}

		if ( class_exists( ThemePlateNavWalker::class ) ) {
			if ( empty( $args['walker'] ) ) {
				$args['walker'] = new ThemePlateNavWalker();
			}

			if ( $args['walker'] instanceof ThemePlateNavWalker && 'wp_page_menu' === $args['fallback_cb'] ) {
				$args['fallback_cb'] = '\ThemePlate\NavWalker::fallback';
			}
		}

		if ( empty( $args['items_wrap'] ) ) {
			$args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
		}

		return $args;

	}

}
