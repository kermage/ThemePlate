<?php

/**
 * WordPress markup cleaner
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

class Cleaner {

	private static Cleaner $instance;


	public static function instance(): Cleaner {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	private function __construct() {

		$features = glob( __DIR__ . '/src/Features/*.php' );

		foreach ( $features as $feature ) {
			$feature = basename( $feature, '.php' );
			$feature = __NAMESPACE__ . '\\Cleaner\\Features\\' . $feature;

			new $feature();
		}

	}

}
