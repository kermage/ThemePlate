<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use Exception;
use ThemePlate\Core\Helper\Field;
use ThemePlate\CPT\PostType;
use ThemePlate\CPT\Taxonomy;
use ThemePlate\Meta\Menu;
use ThemePlate\Meta\Post;
use ThemePlate\Meta\Term;
use ThemePlate\Meta\User;

trait Helpers {

	private $storages = array();


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
			$meta = new Post( $args );

			$this->store( $meta->get_config() );

			return $meta;
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
			$settings = new Settings( $args );

			$this->store( $settings->get_config() );

			return $settings;
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function term_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			$meta = new Term( $args );

			$this->store( $meta->get_config() );

			return $meta;
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function user_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			$meta = new User( $args );

			$this->store( $meta->get_config() );

			return $meta;
		} catch ( Exception $e ) {
			return $e;
		}

	}


	public function menu_meta( $args ) {

		$args['id'] = $this->key . '_' . $args['id'];

		try {
			$meta = new Menu( $args );

			$this->store( $meta->get_config() );

			return $meta;
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


	private function store( $config ) {

		$keys = 'options' === $config['object_type'] ? $config['page'] : $config['object_type'];

		foreach ( $config['fields'] as $field ) {
			foreach ( (array) $keys as $key ) {
				$this->storages[ strtolower( $key ) ][ $config['id'] . '_' . $field['id'] ] = $field;
			}
		}

	}


	private function retreive( $key, $id ) {

		if ( isset( $this->storages[ strtolower( $key ) ][ $id ] ) ) {
			return $this->storages[ strtolower( $key ) ][ $id ];
		}

		return Field::filter( array() );

	}


	private function get_default( $key, $id ) {

		$config = $this->retreive( $key, $id );

		return $config['default'];

	}


	public function get_meta( $meta_key, $post_id = false, $meta_type = 'post', $single = true ) {

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$value = get_metadata( $meta_type, $post_id, $meta_key, $single );

		if ( $value ) {
			return $value;
		}

		return $this->get_default( $meta_type, $meta_key );

	}


	public function get_option( $key, $page ) {

		$options = get_option( $page );
		$value   = isset( $options[ $key ] ) ? $options[ $key ] : '';

		if ( $value ) {
			return $value;
		}

		return $this->get_default( $page, $key );

	}

}
