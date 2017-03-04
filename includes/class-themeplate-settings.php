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


	public function page() {
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
									<?php settings_fields( ThemePlate()->key ); ?>
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
								</div>
							</div>
							<?php $this->section( 'side' ); ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php $this->section( 'normal' ); ?>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}


	public function section( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;

		echo '<div id="' . $page . '-sortables" class="meta-box-sortables">';

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			printf( '<div id="%s-box" class="postbox">', $section['id'] );
			echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
			echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $section['title'] ) . '</span>';
			echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';
			echo '<h2 class="hndle"><span>' . $section['title'] . '</span></h2>';
			echo '<div class="inside">';

			if ( $section['callback'] )
				echo '<p>' . $section['callback'] . '</p>';

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;

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

		if ( ! isset( $wp_settings_fields[$page][$section] ) )
			return;

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			$class = '';

			if ( ! empty( $field['args']['class'] ) ) {
				$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
			}

			echo "<tr{$class}>";

			if ( ! empty( $field['args']['label_for'] ) ) {
				echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
			} else {
				echo '<th scope="row">' . $field['title'] . '</th>';
			}

			echo '<td>';
			call_user_func($field['callback'], $field['args']);
			echo '</td>';
			echo '</tr>';
		}
	}


	public function add( $param ) {
		if ( ! is_array( $param ) )
			return false;

		add_settings_section(
			$param['id'],
			$param['title'],
			$param['description'],
			( $param['context'] ? $param['context'] : 'normal' )
		);

		foreach ( $param['fields'] as $id => $field ) {
			add_settings_field(
				$param['id'] . '_' . $id,
				$field['name'] . '<span>' . $field['desc'] . '</span>',
				array( $this, 'create' ),
				( $param['context'] ? $param['context'] : 'normal' ),
				$param['id'],
				array(
					'label_for' => $param['id'] . '_' . $id,
					'type'      => $field['type'],
					'std'       => $field['std'],
					'options'   => $field['options'],
					'multiple'  => $field['multiple']
				)
			);
		}
	}


	public function create( $param ) {
		if ( ! is_array( $param ) )
			return false;

		$field = $param;
		$field['id'] = $param['label_for'];
		$field['value'] = get_option( ThemePlate()->key );
		$field['value'] = $field['value'][$field['id']];
		$field['value'] = $field['value'] ? $field['value'] : $param['std'];

		ThemePlate_Fields::instance()->render( $field );
	}

}
