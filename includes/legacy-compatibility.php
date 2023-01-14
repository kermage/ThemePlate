<?php

/**
 * Compatibility
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use ThemePlate\Legacy\Cleaner as LegacyCleaner;
use ThemePlate\Legacy\NavWalker as LegacyNavWalker;

if ( ! class_exists( Cleaner::class ) && class_exists( LegacyCleaner::class ) ) {
	class Cleaner extends LegacyCleaner {
	}
}

if ( ! class_exists( NavWalker::class ) && class_exists( LegacyNavWalker::class ) ) {
	class NavWalker extends LegacyNavWalker {
	}
}
