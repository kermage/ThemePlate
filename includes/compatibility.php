<?php

/**
 * Compatibility
 *
 * @package ThemePlate
 * @since 0.1.0
 */

require_once 'legacy-' . basename( __FILE__ );

use ThemePlate\Cleaner;
use ThemePlate\NavWalker;

if ( class_exists( Cleaner::class ) ) {
	class ThemePlate_Cleaner extends ThemePlate\Cleaner {
	}
}

if ( class_exists( NavWalker::class ) ) {
	class ThemePlate_NavWalker extends ThemePlate\NavWalker {
	}
}
