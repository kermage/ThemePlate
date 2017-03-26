<?php

/**
 * Setup user meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_UserMeta {

	private $meta_box;


	public static function instance() {

		return new self();

	}


	public function __construct() {


	}


	public function add( $meta_box ) {

		if ( ! is_array( $meta_box ) ) {
			return false;
		}

		$this->meta_box = $meta_box;

		add_action( 'show_user_profile', array( $this, 'create' ) );
		add_action( 'edit_user_profile', array( $this, 'create' ) );
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );

	}


	public function create( $user ) {

		$meta_box = $this->meta_box;

		$check = ( $meta_box['show_on']['key'] == 'id' ? $user->ID : $check );
		$check = ( $meta_box['show_on']['key'] == 'role' ? $user->roles : $check );
		$check = ( $meta_box['show_on']['key'] == 'capability' ? $user->allcaps : $check );
		$check = ( $meta_box['hide_on']['key'] == 'id' ? $user->ID : $check );
		$check = ( $meta_box['hide_on']['key'] == 'role' ? $user->roles : $check );
		$check = ( $meta_box['hide_on']['key'] == 'capability' ? $user->allcaps : $check );

		if ( ( isset( $meta_box['show_on'] ) && ! array_intersect( (array) $check, (array) $meta_box['show_on']['value'] ) ) ||
			( isset( $meta_box['hide_on'] ) && array_intersect( (array) $check, (array) $meta_box['hide_on']['value'] ) )
		) {
			return;
		}

		wp_enqueue_script( 'post' );
		wp_enqueue_media();

		printf( '<div id="%s-box" class="postbox">', ThemePlate()->key . '_' . $meta_box['id'] );
		echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
		echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $meta_box['title'] ) . '</span>';
		echo '<span class="toggle-indicator" aria-hidden="true"></span>';
		echo '</button>';
		echo '<h2 class="hndle"><span>' . $meta_box['title'] . '</span></h2>';
		echo '<div class="inside">';

		$fields = $meta_box['fields'];

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p>' . $meta_box['description'] . '</p>';
		}

		if ( is_array( $fields ) ) {
			echo '<table class="themeplate form-table">';

			foreach ( $fields as $id => $field ) {
				$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;
				$field['value'] = get_user_meta( $user->ID, $field['id'], true );
				$field['value'] = $field['value'] ? $field['value'] : $field['std'];

				echo '<tr>';
					echo '<th>';
						echo '<label for="' . $field['id'] . '">' . $field['name'] . ( $field['desc'] ? '<span>' . $field['desc'] . '</span>' : '' ) . '</label>';
					echo '</th>';
					echo '<td>';
						ThemePlate_Fields::instance()->render( $field );
					echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
		}

		echo '</div>';
		echo '</div>';

	}


	public function save( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		foreach ( $_POST[ThemePlate()->key] as $key => $val ) {
			$meta = get_user_meta( $user_id, $key, true );
			if ( $val && ! isset( $meta ) ) {
				add_user_meta( $user_id, $key, $val, true );
			} elseif ( $val[0] && $val != $meta ) {
				update_user_meta( $user_id, $key, $val, $meta );
			} elseif ( ! $val[0] && isset( $meta ) ) {
				delete_user_meta( $user_id, $key, $meta );
			}
		}

	}

}
