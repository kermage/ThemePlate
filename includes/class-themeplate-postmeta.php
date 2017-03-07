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

		$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
		$template = basename( get_post_meta( $post_id, '_wp_page_template', true ) );
		$taxonomies = get_object_taxonomies( get_post_type() );
		$allterms = array();
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			foreach ( (array) $terms as $term ) {
				array_push( $allterms, $term->term_id );
			}
		}

		$check = ( $meta_box['show_on']['key'] == 'id' ? $post_id : $check );
		$check = ( $meta_box['show_on']['key'] == 'template' ? $template : $check );
		$check = ( $meta_box['show_on']['key'] == 'term' ? $allterms : $check );
		$check = ( $meta_box['hide_on']['key'] == 'id' ? $post_id : $check );
		$check = ( $meta_box['hide_on']['key'] == 'template' ? $template : $check );
		$check = ( $meta_box['hide_on']['key'] == 'term' ? $allterms : $check );

		if ( ( ! isset( $meta_box['show_on'] ) && ! isset( $meta_box['hide_on'] ) ) ||
			( isset( $meta_box['show_on'] ) && array_intersect( (array) $check, (array) $meta_box['show_on']['value'] ) ) ||
			( isset( $meta_box['hide_on'] ) && ! array_intersect( (array) $check, (array) $meta_box['hide_on']['value'] ) )
		) {
			$meta_box['id'] = ThemePlate()->key . '_' . $meta_box['id'];
			$id = 'themeplate_' . $meta_box['id'];
			if ( $meta_box['screen'] == 'post' )
				$id .= '_post';

			add_meta_box( $id, $meta_box['title'], array( $this, 'create' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'], $meta_box );
		}
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

				if ( $field['group'] == 'start' && ! $grouped ) {
					echo '</table><td><table class="themeplate form-table grouped"><tr>';
					$grouped = true;
				} elseif ( ! $grouped ) {
					echo '<tr>';
				}

				$label = '<label for="' . $field['id'] . '">' . $field['name'] . ( $field['desc'] ? '<span>' . $field['desc'] . '</span>' : '' ) . '</label>';

				if ( $grouped ) {
					if ( ! $stacking ) {
						echo '<td' . ( $field['width'] ? ' style="width: ' . $field['width'] . '"' : '' ) . '>';
					}

					if ( $field['stack'] && ! $stacking ) {
						echo '<div class="stacked">';
						$stacking = true;
					}

					echo '<div class="label">' . $label . '</div>';
					ThemePlate_Fields::instance()->render( $field );

					if ( $stacking ) {
						echo '</div>';

						if ( $field['stack'] ) {
							echo '<div class="stacked">';
						} else {
							echo '</td>';
							$stacking = false;
						}
					} else {
						echo '</td>';
					}
				} else {
					echo '<th scope="row">' . $label . '</th>';
					echo '<td>';
						ThemePlate_Fields::instance()->render( $field );
					echo '</td>';
				}

				if ( $field['group'] == 'end' && $grouped ) {
					echo '</tr></table></td><table class="themeplate form-table">';
					$grouped = false;
				} elseif ( ! $grouped ) {
					echo '</tr>';
				}
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

		foreach( $_POST[ThemePlate()->key] as $key => $val ) {
			$meta = get_post_meta( $post_id, $key, true );
			if ( $val && ! $meta ) {
				add_post_meta( $post_id, $key, $val, true );
			} elseif ( $val && $val != $meta ) {
				update_post_meta( $post_id, $key, $val, $meta );
			} elseif ( ! $val && $meta ) {
				delete_post_meta( $post_id, $key, $val );
			}
		}
	}

}
