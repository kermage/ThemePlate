<?php

/**
 * Setup taxonomies
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Taxonomies {

	protected $meta_box;


	public static function instance() {

		return new self();

	}


	public function __construct() {


	}


	public function add( $meta_box ) {

		if ( ! is_array( $meta_box ) )
			return false;

		$this->meta_box = $meta_box;

		add_action( $meta_box['taxonomy'] . '_add_form_fields', array( $this, 'create' ) );
		add_action( $meta_box['taxonomy'] . '_edit_form_fields', array( $this, 'create' ) );
		add_action( 'created_' . $meta_box['taxonomy'], array( $this, 'save' ) );
		add_action( 'edited_' . $meta_box['taxonomy'], array( $this, 'save' ) );

	}


	public function create( $tag ) {

		wp_enqueue_media();

		$meta_box = $this->meta_box;
		$fields = $meta_box['fields'];

		if ( is_array( $fields ) ) {

			foreach ( $fields as $id => $field ) {
				$field['id'] = $meta_box['id'] . '_' . $id;
				$field['value'] = get_term_meta( $tag->term_id, $field['id'], true );
				$field['value'] = $field['value'] ? $field['value'] : $field['std'];

				echo '<tr class="form-field">';
					echo '<th><label for="' . $field['id'] . '"><strong>' . $field['name'] . '</strong></label></th>';
					echo '<td>';
						ThemePlate_Fields::instance()->render( $field );
					echo '<p class="description">' . $field['desc'] . '</p></td>';
				echo '</tr>';
			}
			
		}

	}


	public function save( $term_id ) {

		foreach( $_POST['themeplate'] as $key => $val ) {
			update_term_meta( $term_id, $key, $val );
		}

	}

}

ThemePlate_Taxonomies::instance();
