<?php

/**
 * Setup settings
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Settings {

	private static $instance;


	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	public function __construct() {

	}


	public function add( $param ) {

		if ( ! is_array( $param ) || empty( $param ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $param ) || ! array_key_exists( 'title', $param ) ) {
			return false;
		}

		if ( ! is_array( $param['fields'] ) || empty( $param['fields'] ) ) {
			return false;
		}

		$page = ThemePlate()->key . '-' . ( $param['page'] ? $param['page'] : ThemePlate()->slug );
		$page .= '-' . ( $param['context'] ? $param['context'] : 'normal' );

		add_settings_section(
			$param['id'],
			$param['title'],
			$param['description'],
			$page
		);

		foreach ( $param['fields'] as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field['id'] = $param['id'] . '_' . $id;
			$field['page'] = ( $param['page'] ? $param['page'] : ThemePlate()->slug );
			$label = $field['name'] . ( $field['desc'] ? '<span>' . $field['desc'] . '</span>' : '' );

			add_settings_field(
				$field['id'],
				$label,
				array( $this, 'create' ),
				$page,
				$param['id'],
				$field
			);
		}

	}


	public function page() {

		$page = get_current_screen()->id;
		$title = sanitize_title( ThemePlate()->title );
		$page = str_replace( 'toplevel_page_', '', $page );
		$page = str_replace( $title . '_page_', '', $page );

		wp_enqueue_script( 'post' );
		wp_enqueue_media();
		?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title(); ?></h1>
			<form action="options.php" method="post">
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="postbox-container-1" class="postbox-container">
							<div id="submitdiv" class="postbox">
								<h2>Publish</h2>
								<div id="major-publishing-actions">
									<?php settings_fields( $page ); ?>
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
								</div>
							</div>
							<?php $this->section( $page . '-side' ); ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php $this->section( $page . '-normal' ); ?>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php

	}


	public function section( $page ) {

		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) ) {
			return;
		}

		echo '<div id="' . $page . '-sortables" class="meta-box-sortables">';

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			printf( '<div id="%s-box" class="postbox">', $section['id'] );
			echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
			echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $section['title'] ) . '</span>';
			echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';
			echo '<h2 class="hndle"><span>' . $section['title'] . '</span></h2>';
			echo '<div class="inside">';

			if ( $section['callback'] ) {
				echo '<p>' . $section['callback'] . '</p>';
			}

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
				continue;
			}

			echo '<table class="themeplate form-table">';
			$this->fields( $page, $section['id'] );
			echo '</table>';
			echo '</div>';
			echo '</div>';
		}

		echo '</div>';

	}


	public function fields( $page, $section ) {

		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[$page][$section] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			if ( $field['args']['group'] == 'start' && ! $grouped ) {
				echo '</table><table class="themeplate form-table grouped"><tr>';
				$grouped = true;
			} elseif ( ! $grouped ) {
				echo '<tr>';
			}

			$label = '<label for="' . $field['args']['id'] . '">' . $field['args']['name'] . ( $field['args']['desc'] ? '<span>' . $field['args']['desc'] . '</span>' : '' ) . '</label>';

			if ( $grouped ) {
				if ( ! $stacking ) {
					$width = '';
					if ( $field['args']['width'] ) {
						if ( preg_match( '/\d+(%|px|r?em)/', $field['args']['width'] ) ) {
							$width = ' style="width:' . $field['args']['width'] . '"';
						} else {
							$width = ' class="' . $field['args']['width'] . '"';
						}
					}
					echo '<td' . ( $width ? $width : '' ) . '>';
				}

				if ( $field['args']['stack'] && ! $stacking ) {
					echo '<div class="stacked">';
					$stacking = true;
				}

				echo '<div class="label">' . $label . '</div>';
				call_user_func( $field['callback'], $field['args'] );

				if ( $stacking ) {
					echo '</div>';

					if ( $field['args']['stack'] ) {
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
					call_user_func( $field['callback'], $field['args'] );
				echo '</td>';
			}

			if ( $field['args']['group'] == 'end' && $grouped ) {
				echo '</tr></table><table class="themeplate form-table">';
				$grouped = false;
			} elseif ( ! $grouped ) {
				echo '</tr>';
			}
		}

	}


	public function create( $param ) {

		$field = $param;
		$field['prefix'] = ThemePlate()->key . '-' . $field['page'];
		$field['value'] = get_option( $field['prefix'] );
		$field['value'] = $field['value'][$field['id']];
		$field['value'] = $field['value'] ? $field['value'] : $field['std'];

		ThemePlate_Fields::instance()->render( $field );

	}

}
