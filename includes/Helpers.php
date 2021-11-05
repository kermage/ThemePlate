<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use Exception;
use ThemePlate\Column;
use ThemePlate\CPT\PostType;
use ThemePlate\CPT\Taxonomy;
use ThemePlate\Meta\Menu;
use ThemePlate\Meta\Post;
use ThemePlate\Meta\Term;
use ThemePlate\Meta\User;
use ThemePlate\Page;
use ThemePlate\Settings;

trait Helpers {

	public function post_type( $args ) {

		try {
			return new PostType( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function taxonomy( $args ) {

		try {
			return new Taxonomy( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function post_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new Post( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function settings( $args ) {

		if ( isset( $args['page'] ) ) {
			$args['page'] = (array) $args['page'];

			foreach ( $args['page'] as $index => $value ) {
				$args['page'][ $index ] = $this->key . '-' . $value;
			}
		} else {
			$args['page'] = $this->key . '-' . $this->slug;
		}

		try {
			return new Settings( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function term_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new Term( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function user_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new User( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function menu_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			return new Menu( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function page( $args ) {

		$args['id'] = $this->key . '-' . $args['id'];

		if ( isset( $args['parent'] ) && 'options' === $args['parent'] ) {
			$args['parent'] = $this->key . '-options';
		}

		try {
			return new Page( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function column( $args ) {

		try {
			return new Column( $args );
		} catch ( Exception $e ) {
			return $e;
		}

	}

}
