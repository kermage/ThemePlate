<?php

/**
 * Setup settings
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Settings {

	private $param;


	public function __construct( $param ) {

		if ( ! is_array( $param ) || empty( $param ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $param ) || ! array_key_exists( 'title', $param ) ) {
			return false;
		}

		if ( ! is_array( $param['fields'] ) || empty( $param['fields'] ) ) {
			return false;
		}

		$this->param = $param;

		add_action( 'admin_init', array( $this, 'add' ) );

	}


	public function add() {

		$param = $this->param;

		$page = ThemePlate()->key . '-' . ( isset( $param['page'] ) ? $param['page'] : ThemePlate()->slug );
		$page .= '-' . ( isset( $param['context'] ) ? $param['context'] : 'normal' );

		add_settings_section(
			$param['id'],
			$param['title'],
			$param,
			$page
		);

		foreach ( $param['fields'] as $id => $field ) {
			if ( ! is_array( $field ) || empty( $field ) ) {
				continue;
			}

			$field['id'] = $param['id'] . '_' . $id;
			$field['page'] = isset( $param['page'] ) ? $param['page'] : ThemePlate()->slug;
			$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';
			$field['style'] = isset( $field['style'] ) ? $field['style'] : '';

			add_settings_field(
				$field['id'],
				$field['name'],
				array( $this, 'create' ),
				$page,
				$param['id'],
				$field
			);
		}

	}


	public static function page() {

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
							<?php self::section( $page, 'side' ); ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php self::section( $page, 'normal' ); ?>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php

	}


	public static function section( $page, $context ) {

		global $wp_settings_sections, $wp_settings_fields;

		$page = $page . '-' . $context;

		if ( ! isset( $wp_settings_sections[$page] ) ) {
			return;
		}

		echo '<div id="' . $context . '-sortables" class="meta-box-sortables">';

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			printf( '<div id="themeplate_%s" class="postbox">', $section['id'] );
			echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
			echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $section['title'] ) . '</span>';
			echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';
			echo '<h2 class="hndle"><span>' . $section['title'] . '</span></h2>';
			echo '<div class="inside">';

			if ( ! empty( $section['callback']['description'] ) ) {
				echo '<p class="description">' . $section['callback']['description'] . '</p>';
			}

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
				continue;
			}

			$style = isset( $section['callback']['style'] ) ? $section['callback']['style'] : '';

			echo '<div class="fields-container ' . $style . '">';
				self::fields( $page, $section['id'] );
			echo '</div>';

			echo '</div>';
			echo '</div>';
		}

		echo '</div>';

	}


	public static function fields( $page, $section ) {

		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[$page][$section] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			echo '<div class="field-wrapper type-' . $field['args']['type'] . ' ' . $field['args']['style'] . '">';
				if ( ! empty( $field['args']['name'] ) || ! empty( $field['args']['desc'] ) ) {
					echo '<div class="field-label">';
						echo ! empty( $field['args']['name'] ) ? '<label class="label" for="' . $field['args']['id'] . '">' . $field['args']['name'] . '</label>' : '';
						echo ! empty( $field['args']['desc'] ) ? '<p class="description">' . $field['args']['desc'] . '</p>' : '';
					echo '</div>';
				}

				echo '<div class="field-input' . ( isset( $field['args']['repeatable'] ) ? ' repeatable' : '' ) . '">';
					call_user_func( $field['callback'], $field['args'] );
				echo '</div>';
			echo '</div>';
		}

	}


	public function create( $field ) {

		$field['object'] = array(
			'type' => 'option',
			'id' => ThemePlate()->key . '-' . $field['page']
		);

		$key = $field['id'];
		$name = $field['object']['id'] . '[' . $key . ']';
		$default = isset( $field['std'] ) ? $field['std'] : '';
		$unique = isset( $field['repeatable'] ) ? false : true;
		$options = get_option( $field['object']['id'] );
		$stored = isset( $options[$key] ) ? $options[$key] : '';
		$value = $stored ? $stored : $default;

		if ( $unique ) {
			$field['value'] = $value;
			$field['name'] = $name;

			ThemePlate_Fields::instance()->render( $field );
		} else {
			foreach ( (array) $value as $i => $val ) {
				$field['value'] = $val;
				$field['id'] = $key . '_' . $i;
				$field['name'] =  $name . '[' . $i . ']';

				echo '<div class="themeplate-clone">';
					echo '<div class="themeplate-handle"></div>';
					ThemePlate_Fields::instance()->render( $field );
					echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
				echo '</div>';
			}

			$field['value'] = $default;
			$field['id'] = $key . '_i-x';
			$field['name'] =  $name . '[i-x]';

			echo '<div class="themeplate-clone hidden">';
				echo '<div class="themeplate-handle"></div>';
				ThemePlate_Fields::instance()->render( $field );
				echo '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';
			echo '</div>';
			echo '<input type="button" class="button clone-add" value="Add Field" />';
		}

	}


	public static function save( $options ) {

		$values = array();

		foreach ( $options as $option => $value ) {
			foreach ( (array) $value as $i => $val ) {
				foreach ( (array) $val as $j => $v ) {
					if ( is_array( $v ) ) {
						$val[$j] = array_merge( array_filter( $v ) );
					}
				}

				if ( is_array( $val ) ) {
					$value[$i] = array_merge( array_filter( $val ) );
				}

				if ( ! empty( $value[$i] ) || ! is_array( $value ) ) {
					continue;
				}

				unset( $value[$i] );
			}

			if ( is_array( $value ) ) {
				$value = array_merge( $value );
			}

			$values[$option] = $value;
		}

		return $values;

	}

}
