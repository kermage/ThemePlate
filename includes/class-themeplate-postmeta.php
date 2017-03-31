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

		if ( ! is_array( $meta_box ) || empty( $meta_box ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $meta_box ) || ! array_key_exists( 'title', $meta_box ) ) {
			return false;
		}

		if ( ! is_array( $meta_box['fields'] ) || empty( $meta_box['fields'] ) ) {
			return false;
		}

		$defaults = array(
			'context'  => 'advanced',
			'priority' => 'default'
		);
		$meta_box = wp_parse_args( $meta_box, $defaults );

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
			$id = $meta_box['id'];
			if ( $meta_box['screen'] == 'post' ) {
				$id = 'themeplate_' . $meta_box['id'] . '_post';
			}

			add_meta_box( $id, $meta_box['title'], array( $this, 'create' ), $meta_box['screen'], $meta_box['context'], $meta_box['priority'], $meta_box );
		}

	}


	public function create( $post, $meta_box ) {

		if ( ! empty( $meta_box['args']['description'] ) ) {
			echo '<p>' . $meta_box['args']['description'] . '</p>';
		}

		wp_nonce_field( basename( __FILE__ ), 'themeplate_meta_box_nonce' );

		echo '<table class="themeplate form-table">';

		foreach ( $meta_box['args']['fields'] as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field['id'] = $meta_box['args']['id'] . '_' . $id;
			$field['value'] = get_post_meta( $post->ID, $field['id'], true );
			$field['value'] = $field['value'] ? $field['value'] : $field['std'];

			if ( $field['group'] == 'start' && ! $grouped ) {
				echo '</table><table class="themeplate form-table grouped"><tr>';
				$grouped = true;
			} elseif ( ! $grouped ) {
				echo '<tr>';
			}

			$label = '<label for="' . $field['id'] . '">' . $field['name'] . ( $field['desc'] ? '<span>' . $field['desc'] . '</span>' : '' ) . '</label>';

			if ( $grouped ) {
				if ( ! $stacking ) {
					$width = '';
					if ( $field['width'] ) {
						if ( preg_match( '/\d+(%|px|r?em)/', $field['width'] ) ) {
							$width = ' style="width:' . $field['width'] . '"';
						} else {
							$width = ' class="' . $field['width'] . '"';
						}
					}
					echo '<td' . ( $width ? $width : '' ) . '>';
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
				echo '</tr></table><table class="themeplate form-table">';
				$grouped = false;
			} elseif ( ! $grouped ) {
				echo '</tr>';
			}
		}

		echo '</table>';

	}


	public function save( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['themeplate_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['themeplate_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		foreach ( $_POST[ThemePlate()->key] as $key => $val ) {
			$meta = get_post_meta( $post_id, $key, true );
			if ( $val && ! isset( $meta ) ) {
				add_post_meta( $post_id, $key, $val, true );
			} elseif ( $val[0] && $val != $meta ) {
				update_post_meta( $post_id, $key, $val, $meta );
			} elseif ( ! $val[0] && isset( $meta ) ) {
				delete_post_meta( $post_id, $key, $meta );
			}
		}

	}

}
