<?php

/**
 * Setup user meta boxes
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_UserMeta {

	private $meta_box;


	public function __construct( $meta_box ) {

		if ( ! is_array( $meta_box ) || empty( $meta_box ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $meta_box ) || ! array_key_exists( 'title', $meta_box ) ) {
			return false;
		}

		if ( ! is_array( $meta_box['fields'] ) || empty( $meta_box['fields'] ) ) {
			return false;
		}

		$this->meta_box = $meta_box;

		add_action( 'show_user_profile', array( $this, 'create' ) );
		add_action( 'edit_user_profile', array( $this, 'create' ) );
		add_action( 'user_new_form', array( $this, 'create' ) );
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
		add_action( 'user_register', array( $this, 'save' ) );

	}


	public function create( $user ) {

		$meta_box = $this->meta_box;

		if ( is_object( $user ) ) :
		$first = true;
		$check = true;

		foreach ( $meta_box as $key => $value ) {
			if ( $key == 'show_on' ) {
				if ( $first ) {
					$first = false;
					$check = false;
				}

				if ( is_callable( $value ) ) {
					$check = call_user_func( $value );
				} elseif ( is_array( $value ) ) {
					if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
						$value = array( $value );
					}

					foreach ( (array) $value as $show_on ) {
						if ( $show_on['key'] == 'id' && array_intersect( (array) $user->ID, (array) $show_on['value'] ) ) {
							$check = true;
						}
						if ( $show_on['key'] == 'role' && array_intersect( (array) $user->roles, (array) $show_on['value'] ) ) {
							$check = true;
						}
						if ( $show_on['key'] == 'capability' && array_intersect( array_keys( (array) $user->allcaps ), (array) $show_on['value'] ) ) {
							$check = true;
						}
					}
				}
			}

			if ( $key == 'hide_on' ) {
				if ( $first ) {
					$first = false;
				}

				if ( is_callable( $value ) ) {
					$check = ! call_user_func( $value );
				} elseif ( is_array( $value ) ) {
					if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
						$value = array( $value );
					}

					foreach ( (array) $value as $hide_on ) {
						if ( $hide_on['key'] == 'id' && array_intersect( (array) $user->ID, (array) $hide_on['value'] ) ) {
							$check = false;
						}
						if ( $hide_on['key'] == 'role' && array_intersect( (array) $user->roles, (array) $hide_on['value'] ) ) {
							$check = false;
						}
						if ( $hide_on['key'] == 'capability' && array_intersect( array_keys( (array) $user->allcaps ), (array) $hide_on['value'] ) ) {
							$check = false;
						}
					}
				}
			}
		}

		if ( ! $check ) {
			return;
		}
		endif;

		wp_enqueue_script( 'post' );
		wp_enqueue_media();

		printf( '<div id="themeplate_%s-box" class="postbox">', ThemePlate()->key . '_' . $meta_box['id'] );
		echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
		echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $meta_box['title'] ) . '</span>';
		echo '<span class="toggle-indicator" aria-hidden="true"></span>';
		echo '</button>';
		echo '<h2 class="hndle"><span>' . $meta_box['title'] . '</span></h2>';
		echo '<div class="inside">';

		if ( ! empty( $meta_box['description'] ) ) {
			echo '<p>' . $meta_box['description'] . '</p>';
		}

		echo '<table class="form-table">';

		$grouped = false;
		$stacking = false;

		foreach ( $meta_box['fields'] as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field['id'] = ThemePlate()->key . '_' . $meta_box['id'] . '_' . $id;

			$default = isset( $field['std'] ) ? $field['std'] : '';
			$stored = is_object( $user ) ? get_user_meta( $user->ID, $field['id'], true ) : '';
			$field['value'] = $stored ? $stored : $default;

			if ( isset( $field['group'] ) && $field['group'] == 'start' && ! $grouped ) {
				echo '</table><table class="form-table grouped"><tr>';
				$grouped = true;
			} elseif ( ! $grouped ) {
				echo '<tr>';
			}

			$desc = isset( $field['desc'] ) ? '<span>' . $field['desc'] . '</span>' : '';
			$label = '<label for="' . $field['id'] . '">' . $field['name'] . $desc . '</label>';

			if ( $grouped ) {
				if ( ! $stacking ) {
					$width = '';
					if ( isset( $field['width'] ) ) {
						if ( preg_match( '/\d+(%|px|r?em)/', $field['width'] ) ) {
							$width = ' style="width:' . $field['width'] . '"';
						} else {
							$width = ' class="' . $field['width'] . '"';
						}
					}
					echo '<td' . ( $width ? $width : '' ) . '>';
				}

				if ( isset( $field['stack'] ) && ! $stacking ) {
					echo '<div class="stacked">';
					$stacking = true;
				}

				echo '<div class="label">' . $label . '</div>';
				ThemePlate_Fields::instance()->render( $field );

				if ( $stacking ) {
					echo '</div>';

					if ( isset( $field['stack'] ) ) {
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

			if ( isset( $field['group'] ) && $field['group'] == 'end' && $grouped ) {
				echo '</tr></table><table class="form-table">';
				$grouped = false;
			} elseif ( ! $grouped ) {
				echo '</tr>';
			}
		}

		echo '</table>';

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
			} elseif ( isset( $val[0] ) && $val[0] && $val != $meta ) {
				update_user_meta( $user_id, $key, $val, $meta );
			} elseif ( ( ! isset( $val[0] ) || ! $val[0] ) && isset( $meta ) ) {
				delete_user_meta( $user_id, $key, $meta );
			}
		}

	}

}
