<?php

/**
 * Setup settings
 *
 * @package ThemePlate
 * @since 0.1.0
 */

if( ! function_exists( 'themeplate_settings_menu' ) ) {
	register_setting( 'themeplate', 'themeplate' );

	function themeplate_settings_menu() {
		add_menu_page(
			// Page Title
			'Theme Options',
			// Menu Title
			'Theme Options',
			// Capability
			'edit_theme_options',
			// Menu Slug
			'theme-options',
			// Content Function
			'themeplate_settings_page'
		);
	}
	add_action( 'admin_menu', 'themeplate_settings_menu' );
}

if( ! function_exists( 'themeplate_settings_page' ) ) {
	function themeplate_settings_page() {
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
									<?php settings_fields( 'themeplate' ); ?>
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
								</div>
							</div>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php themeplate_settings_section( 'themeplate' ); ?>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}

if( ! function_exists( 'themeplate_settings_section' ) ) {
	function themeplate_settings_section( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;

		echo '<div id="normal-sortables" class="meta-box-sortables">';

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			printf( '<div id="%s-box" class="postbox">', $section['id'] );
			echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
			echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $section['title'] ) . '</span>';
			echo '<span class="toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';
			echo '<h2 class="hndle"><span>' . $section['title'] . '</span></h2>';
			echo '<div class="inside">';

			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;

			echo '<table class="themeplate-meta-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
			echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}
}

if( ! function_exists( 'themeplate_add_settings' ) ) {
	function themeplate_add_settings( $param ) {
		if ( ! is_array( $param ) )
			return false;

		add_settings_section(
			$param['id'],
			$param['title'],
			$param['callback'],
			'themeplate'
		);

		foreach ( $param['fields'] as $id => $field ) {
			add_settings_field(
				$param['id'] . '_' . $id,
				$field['name'],
				'themeplate_create_settings',
				'themeplate',
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
}

if( ! function_exists( 'themeplate_create_settings' ) ) {
	function themeplate_create_settings( $param ) {
		if ( ! is_array( $param ) )
			return false;

		$id = $param['label_for'];
		$setting = get_option( 'themeplate' );
		$setting = $setting[$id];
		$setting = $setting ? $setting : $param['std'];

		switch ( $param['type'] ) {
			default:
			case 'text':
				echo '<input type="text" name="themeplate[' . $id . ']" id="' . $id . '" value="' . $setting . '" />';
				break;

			case 'textarea':
				echo '<textarea name="themeplate[' . $id . ']" id="' . $id . '" rows="4">' . $setting . '</textarea>';
				break;

			case 'select' :
				echo '<select name="themeplate[' . $id . ']' . ( $param['multiple'] ? '[]' : '' ) . '" id="' . $id . '" ' . ( $param['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				echo '<option disabled="disabled" selected="selected" hidden>' . __( '&mdash; Select &mdash;' ) . '</option>';
				foreach( $param['options'] as $value => $option ) {
					echo '<option value="' . ( $value + 1 ) . '"';
					if ( in_array( ( $value + 1 ), (array) $setting ) ) echo ' selected="selected"';
					else selected( $setting, ( $value + 1 ) );
					echo '>' . $option . '</option>';
				}
				echo '</select>';
				break;

			case 'radio' :
				foreach( $param['options'] as $value => $option ) {
					echo '<label class="radio-label"><input type="radio" name="themeplate[' . $id . ']" value="' . ( $value +  1 ) . '"' . checked( $setting, ( $value +  1 ), false ) . ' /> ' . $option . '</label>';
				}
				break;

			case 'checkbox' :
				echo '<input type="checkbox" name="themeplate[' . $id . ']" id="' . $id . '" value="1" ' . checked( $setting, 1, false ) . ' />';
				break;

			case 'color':
				echo '<input type="text" name="themeplate[' . $id . ']" id="' . $id . '" class="wp-color-picker" value="' . $setting . '" data-default-color="' . $setting . '" />';
				break;

			case 'file':
				echo '<input type="hidden" name="themeplate[' . $id . ']" id="' . $id . '" value="' . $setting . '" /><div id="' . $id . '_files">';
				if ( $setting ) {
					$files = explode( ',', $setting );
					foreach( $files as $file ) {
						echo '<p>' . basename( get_attached_file( $file ) ) . '</p>';
					}
				}
				echo '</div><input type="button" class="button" id="' . $id . '_button" value="' . ( $setting ? 'Re-select' : 'Select' ) . '" ' . ( $param['multiple'] ? 'multiple' : '' ) . '/> <input type="' . ( $setting ? 'button' : 'hidden' ) . '" class="button" id="' . $id . '_remove" value="Remove" />';
				break;

			case 'date':
				echo '<input type="date" name="themeplate[' . $id . ']" id="' . $id . '" value="' . $setting . '" />';
				break;

			case 'time':
				echo '<input type="time" name="themeplate[' . $id . ']" id="' . $id . '" value="' . $setting . '" />';
				break;

			case 'number':
				echo '<input type="number" name="themeplate[' . $id . ']" id="' . $id . '" value="' . $setting . '"';
				if ( is_array( $param['options'] ) ) foreach( $param['options'] as $option => $value ) echo $option . '="' . $value . '"';
				echo ' />';
				break;

			case 'editor':
				$settings = array(
					'textarea_name' => 'themeplate[' . $id . ']',
					'textarea_rows' => 10
				);
				wp_editor( $setting, $id, $settings );
				break;

			case 'page':
				echo '<select name="themeplate[' . $id . ']' . ( $param['multiple'] ? '[]' : '' ) . '" id="' . $id . '" ' . ( $param['multiple'] ? 'multiple="multiple"' : '' ) . '>';
				echo '<option disabled="disabled" selected="selected" hidden>' . __( '&mdash; Select &mdash;' ) . '</option>';
				$pages = get_pages( array ( 'post_type' => $param['options'] ) );
				foreach( $pages as $page ) {
					echo '<option value="' . $page->ID . '"';
					if ( in_array( $page->ID, (array) $setting ) ) echo ' selected="selected"';
					echo '>' . $page->post_title . '</option>';
				}
				echo '</select>';
				break;
		}
	}
}
