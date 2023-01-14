<?php

/**
 * Setup custom posts and taxonomies
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Legacy\CPT;

use ThemePlate\Legacy\Core\Helper\Main;

abstract class Base {

	protected $config;


	public function __construct( $kind, $config ) {

		$expected = array(
			'name',
			'plural',
			'singular',
		);

		if ( 'taxonomy' === $kind ) {
			$expected[] = 'type';
		}

		if ( ! Main::is_complete( $config, $expected ) ) {
			throw new \Exception();
		}

		$defaults     = array(
			'args' => array(),
		);
		$this->config = Main::fool_proof( $defaults, $config );

		if ( did_action( 'init' ) ) {
			$this->register();
		} else {
			add_action( 'init', array( $this, 'register' ) );
		}

	}


	abstract public function register();

}
