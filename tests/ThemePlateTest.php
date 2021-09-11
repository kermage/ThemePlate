<?php

/**
 * ThemePlate test case.
 *
 * @package Themeplate
 */

class ThemePlateTest extends WP_UnitTestCase {
	public function tearDown() {
		$singleton  = ThemePlate::instance( null, null );
		$reflection = new ReflectionClass( $singleton );
		$instance   = $reflection->getProperty( '_instance' );

		$instance->setAccessible( true );
		$instance->setValue( null );
		$instance->setAccessible( false );
	}

	/**
	 * @expectedDeprecated ThemePlate
	 */
	public function test_update_stalled_in_string_key() {
		ThemePlate( 'test' );

		$this->assertTrue( ThemePlate()->stalled );
	}

	/**
	 * @expectedDeprecated ThemePlate
	 */
	public function test_update_stalled_in_sequential_key() {
		ThemePlate( array( 'test' ) );

		$this->assertTrue( ThemePlate()->stalled );
	}

	/**
	 * @expectedDeprecated ThemePlate
	 */
	public function test_update_stalled_in_old_pages() {
		$args  = array(
			'title' => 'Test',
			'key'   => 'test',
		);
		$pages = array(
			'try' => 'Try',
		);

		ThemePlate( $args, $pages );

		$this->assertTrue( ThemePlate()->stalled );
	}

	public function test_update_not_stalled() {
		$args = array(
			'title' => 'Test',
			'key'   => 'test',
		);

		ThemePlate( $args );

		$this->assertNull( ThemePlate()->stalled );
	}
}
