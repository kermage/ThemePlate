<?php

/**
 * Setup a field type
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Field_Object {

	public static function render( $field ) {

		switch ( $field['type'] ) {
			default:
			case 'post':
			case 'page':
				$action   = 'tp_posts';
				$defaults = array( 'post_type' => $field['type'], );

				if ( ThemePlate_Helper_Main::is_sequential( $field['options'] ) ) {
					$field['options'] = array( 'post_type' => $field['options'] );
				}

				break;
			case 'user':
				$action   = 'tp_users';
				$defaults = array( 'role' => '' );

				if ( ThemePlate_Helper_Main::is_sequential( $field['options'] ) ) {
					$field['options'] = array( 'role' => $field['options'] );
				}

				break;
			case 'term':
				$action   = 'tp_terms';
				$defaults = array( 'taxonomy' => array() );

				if ( ThemePlate_Helper_Main::is_sequential( $field['options'] ) ) {
					$field['options'] = array( 'taxonomy' => $field['options'] );
				}

				break;
		}

		$args = ThemePlate_Helper_Main::fool_proof( $defaults, $field['options'] );

		echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" />';
		echo '<select class="themeplate-select2" name="' . esc_attr( $field['name'] ) . ( $field['multiple'] ? '[]' : '' ) . '" id="' . esc_attr( $field['id'] ) . '"' . ( $field['multiple'] ? ' multiple="multiple"' : '' ) . ( $field['none'] ? ' data-none="true"' : '' ) . ( $field['required'] ? ' required="required"' : '' ) . '>';
		if ( ! $field['value'] ) {
			echo '<option></option>';
		} elseif ( ( $field['none'] && $field['value'] ) || ( ! $field['multiple'] && ! $field['value'] ) ) {
			echo '<option value=""' . ( $field['none'] && $field['value'] ? '' : ' disabled hidden' ) . ( esc_attr( $field['value'] ) ? '>' . esc_attr( __( '&mdash; None &mdash;' ) ) : ' selected>' . esc_attr( __( '&mdash; Select &mdash;' ) ) ) . '</option>';
		}
		echo '</select>';
		echo '<div class="select2-options" data-action="' . $action . '" data-options="' . esc_attr( wp_json_encode( $args, JSON_NUMERIC_CHECK ) ) . '"></div>';

	}


	public static function ajax_actions() {

		add_action( 'wp_ajax_tp_posts', 'ThemePlate_Field_Object::get_posts' );
		add_action( 'wp_ajax_tp_users', 'ThemePlate_Field_Object::get_users' );
		add_action( 'wp_ajax_tp_terms', 'ThemePlate_Field_Object::get_terms' );

	}


	public static function get_posts() {

		$return   = array(
			'results'    => array(),
			'pagination' => array(
				'more' => false,
			),
		);
		$defaults = array(
			's'      => $_GET['search'],
			'fields' => 'ids',
		);
		$query    = new WP_Query( array_merge( $defaults, $_GET['options'], $_GET['page'] ) );

		if ( $_GET['page']['paged'] < $query->max_num_pages ) {
			$return['pagination']['more'] = true;
		}

		foreach ( $query->posts as $post ) {
			$return['results'][] = array(
				'id'   => $post,
				'text' => get_the_title( $post ),
			);
		}

		echo json_encode( $return );

		wp_die();

	}


	public static function get_users() {

		$return   = array();
		$defaults = array(
			'search' => $_GET['search'],
			'fields' => array( 'ID', 'display_name' ),
		);
		$query    = new WP_User_Query( array_merge( $defaults, $_GET['options'] ) );

		foreach ( $query->get_results() as $user ) {
			$return['results'][] = array(
				'id'   => $user->ID,
				'text' => $user->display_name,
			);
		}

		echo json_encode( $return );

		wp_die();

	}


	public static function get_terms() {

		$return   = array();
		$defaults = array(
			'search' => $_GET['search'],
			'fields' => 'id=>name',
		);
		$query    = new WP_Term_Query( array_merge( $defaults, $_GET['options'] ) );

		foreach ( $query->get_terms() as $id => $name ) {
			$return['results'][] = array(
				'id'   => $id,
				'text' => $name,
			);
		}

		echo json_encode( $return );

		wp_die();

	}

}
