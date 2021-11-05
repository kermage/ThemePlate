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

		foreach ( self::features() as $feature ) {
			$feature->register();
		}

	}


	public static function features() {

		$list = array();

		foreach ( glob( __DIR__ . '/src/Features/*.php' ) as $feature ) {
			$feature = basename( $feature, '.php' );
			$feature = __NAMESPACE__ . '\\Cleaner\\Features\\' . $feature;
			$cleaner = new $feature();

			$list[ $cleaner->feature() ] = $cleaner;
		}

		return $list;

	}

}
