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
	class_alias( LegacyCleaner::class, Cleaner::class );
}

if ( ! class_exists( NavWalker::class ) && class_exists( LegacyNavWalker::class ) ) {
	class_alias( LegacyNavWalker::class, NavWalker::class );
}
