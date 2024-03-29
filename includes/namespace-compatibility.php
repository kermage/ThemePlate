<?php

/**
 * Compatibility
 *
 * @package ThemePlate
 * @since 0.1.0
 */

use ThemePlate\Legacy\Cleaner;
use ThemePlate\Legacy\NavWalker;

if ( ! class_exists( ThemePlate_Cleaner::class ) && class_exists( Cleaner::class ) ) {
	class_alias( Cleaner::class, ThemePlate_Cleaner::class );
}

if ( ! class_exists( ThemePlate_NavWalker::class ) && class_exists( NavWalker::class ) ) {
	class_alias( NavWalker::class, ThemePlate_NavWalker::class );
}
