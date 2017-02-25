<?php

/**
 * Setup post meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_PostMeta {

	private static $instance;


	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	public function __construct() {


	}


	public function add( $meta_box ) {
		if ( ! is_array( $meta_box ) )
			return false;

		$id = 'themeplate_' . $meta_box['id'];
		if ( $meta_box['screen'] == 'post' )
			$id .= '_post';

		add_meta_box( $id, $meta_box['title'], array( $this, 'create' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'], $meta_box );
	}


	public function create( $post, $meta_box ) {
		if ( ! is_array( $meta_box ) )
			return false;

		if ( ! empty( $meta_box['args']['description'] ) )
			echo '<p>' . $meta_box['args']['description'] . '</p>';

		$fields = $meta_box['args']['fields'];
		wp_nonce_field( basename( __FILE__ ), 'themeplate_meta_box_nonce' );

		if ( is_array( $fields ) ) {
			echo '<table class="themeplate form-table">';

			foreach ( $fields as $id => $field ) {
				$field['id'] = $meta_box['args']['id'] . '_' . $id;
				$field['value'] = get_post_meta( $post->ID, $field['id'], true );
				$field['value'] = $field['value'] ? $field['value'] : $field['std'];

				echo '<tr>';
					echo '<th scope="row"><label for="' . $field['id'] . '">' . $field['name'] . '<span>' . $field['desc'] . '</span></label></th>';
					echo '<td>';
						ThemePlate_Fields::instance()->render( $field );
					echo '</td>';
				echo '</tr>';
			}
			
			echo '</table>';
		}
	}


	public function save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( ! isset( $_POST['themeplate_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['themeplate_meta_box_nonce'], basename( __FILE__ ) ) )
			return;

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		foreach( $_POST['themeplate'] as $key => $val ) {
			update_post_meta( $post_id, $key, $val );
		}
	}

}
