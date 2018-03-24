<?php

/**
 * Setup post meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_PostMeta {

	private $config;
	private $tpmb;


	public function __construct( $config ) {

		try {
			$this->tpmb = new ThemePlate_MetaBox( 'post', $config );
		} catch( Exception $e ) {
			return false;
		}

		$this->config = $config;

		add_action( 'add_meta_boxes', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );

	}


	public function add() {

		$meta_box = $this->config;
		$post_id = get_the_ID();
		$this->tpmb->object_id( $post_id );
		$check = true;

		if ( isset( $meta_box['show_on'] ) ) {
			$value = $meta_box['show_on'];

			if ( is_callable( $value ) ) {
				$check = call_user_func( $value );
				unset( $meta_box['show_on'] );
			} elseif ( is_array( $value ) ) {
				if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
					$value = array( $value );
					$meta_box['show_on'] = array( $meta_box['show_on'] );
				}

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['show_on'] );

					if ( ! array_intersect( (array) $post_id, (array) $value[0]['value'] ) ) {
						$check = false;
					}
				}
			}
		}

		if ( isset( $meta_box['hide_on'] ) ) {
			$value = $meta_box['hide_on'];

			if ( is_callable( $value ) ) {
				$check = ! call_user_func( $value );
				unset( $meta_box['hide_on'] );
			} elseif ( is_array( $value ) ) {
				if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
					$value = array( $value );
					$meta_box['hide_on'] = array( $meta_box['hide_on'] );
				}

				if ( ( count( $value ) == 1 ) && isset( $value[0]['key'] ) && $value[0]['key'] == 'id' ) {
					unset( $meta_box['hide_on'] );

					if ( array_intersect( (array) $post_id, (array) $value[0]['value'] ) ) {
						$check = false;
					}
				}
			}
		}

		if ( ! $check ) {
			return;
		}

		$defaults = array(
			'screen'   => '',
			'context'  => 'advanced',
			'priority' => 'default'
		);
		$meta_box = wp_parse_args( $meta_box, $defaults );

		add_meta_box( 'themeplate_' . $meta_box['id'], $meta_box['title'], array( $this->tpmb, 'layout_inside' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'] );

	}


	public function save( $post_id ) {

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		$this->tpmb->save( $post_id );

	}

}
