<?php

/**
 * Setup term meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_TermMeta {

	private $meta_box;


	public static function instance() {

		return new self();

	}


	public function __construct() {


	}


	public function add( $meta_box ) {

		if ( ! is_array( $meta_box ) || empty( $meta_box ) ) {
			return false;
		}

		if ( ! is_array( $meta_box['fields'] ) || empty( $meta_box['fields'] ) ) {
			return false;
		}

		$this->meta_box = $meta_box;

		foreach ( (array) $meta_box['taxonomy'] as $taxonomy ) {
			add_action( $taxonomy . '_add_form', array( $this, 'create' ) );
			add_action( $taxonomy . '_edit_form', array( $this, 'create' ) );
			add_action( 'created_' . $taxonomy, array( $this, 'save' ) );
			add_action( 'edited_' . $taxonomy, array( $this, 'save' ) );
		}

	}


	public function create( $tag ) {

		$meta_box = $this->meta_box;

		$check = ( $meta_box['show_on']['key'] == 'id' ? $tag->term_id : $check );
		$check = ( $meta_box['hide_on']['key'] == 'id' ? $tag->term_id : $check );

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

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p>' . $meta_box['description'] . '</p>';
		}

		echo '<table class="themeplate form-table">';

		foreach ( $meta_box['fields'] as $id => $field ) {
			$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;
			$field['value'] = get_term_meta( $tag->term_id, $field['id'], true );
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

		echo '</div>';
		echo '</div>';

	}


	public function save( $term_id ) {

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		foreach ( $_POST[ThemePlate()->key] as $key => $val ) {
			$meta = get_term_meta( $term_id, $key, true );
			if ( $val && ! isset( $meta ) ) {
				add_term_meta( $term_id, $key, $val, true );
			} elseif ( $val[0] && $val != $meta ) {
				update_term_meta( $term_id, $key, $val, $meta );
			} elseif ( ! $val[0] && isset( $meta ) ) {
				delete_term_meta( $term_id, $key, $meta );
			}
		}

	}

}
