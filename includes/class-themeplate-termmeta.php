<?php

/**
 * Setup term meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_TermMeta {

	protected $meta_box;

	protected $form_type;


	public static function instance() {

		return new self();

	}


	public function __construct() {


	}


	public function add( $meta_box ) {

		if ( ! is_array( $meta_box ) )
			return false;

		$this->meta_box = $meta_box;
		$taxonomies = (array) $meta_box['taxonomy'];

		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', array( $this, 'add_form' ) );
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'edit_form' ) );
			add_action( 'created_' . $taxonomy, array( $this, 'save' ) );
			add_action( 'edited_' . $taxonomy, array( $this, 'save' ) );
		}

	}


	public function add_form( $tag ) {

		$this->form_type = 'add';
		$this->create( $tag->term_id );

	}


	public function edit_form( $tag ) {

		$this->form_type = 'edit';
		$this->create( $tag->term_id );

	}


	public function create( $term_id ) {

		wp_enqueue_media();

		$meta_box = $this->meta_box;
		$form_type = $this->form_type;
		$fields = $meta_box['fields'];

		if ( is_array( $fields ) ) {

			foreach ( $fields as $id => $field ) {
				$field['id'] = $meta_box['id'] . '_' . $id;
				$field['value'] = get_term_meta( $term_id, $field['id'], true );
				$field['value'] = $field['value'] ? $field['value'] : $field['std'];

				echo '<' . ( $form_type == 'add' ? 'div' : 'tr' ) . ' class="form-field">';
					echo ( $form_type == 'add' ? '' : '<th>' ) . '<label for="' . $field['id'] . '">' . $field['name'] . '</label>' . ( $form_type == 'add' ? '' : '</th>' );
					echo ( $form_type == 'add' ? '' : '<td>' );
						ThemePlate_Fields::instance()->render( $field );
					echo '<p class="description">' . $field['desc'] . '</p>' . ( $form_type == 'add' ? '' : '<td>' );
				echo '</' . ( $form_type == 'add' ? 'div' : 'tr' ) . '>';
			}

		}

	}


	public function save( $term_id ) {

		foreach( $_POST['themeplate'] as $key => $val ) {
			update_term_meta( $term_id, $key, $val );
		}

	}

}
