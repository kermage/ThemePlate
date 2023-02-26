<?php

/**
 * Compatibility
 *
 * @package ThemePlate
 * @since 0.1.0
 */

if ( function_exists( 'add_action' ) && ! function_exists( 'themeplate_compatibility' ) ) {
	function themeplate_compatibility() {
		require_once 'legacy-' . basename( __FILE__ );
		require_once 'namespace-' . basename( __FILE__ );
	}

	add_action( 'after_setup_theme', 'themeplate_compatibility' );
}
