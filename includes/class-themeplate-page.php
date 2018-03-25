<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Page {

	private $config;

	private $page_defaults = array(
		'capability' => 'edit_theme_options',
		'parent' => ''
	);


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			return false;
		}

		if ( ! array_key_exists( 'id', $config ) || ! array_key_exists( 'title', $config ) ) {
			return false;
		}

		$this->config = ThemePlate_Helpers::fool_proof( $this->page_defaults, $config );

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );

	}


	public function init() {

		register_setting( 'themeplate', $this->config['id'], array( $this, 'save' ) );

	}


	public function menu() {

		$page = $this->config;

		if ( empty( $page['parent'] ) ) {
			add_menu_page(
				// Page Title
				$page['title'],
				// Menu Title
				$page['title'],
				// Capability
				$page['capability'],
				// Menu Slug
				$page['id'],
				// Content Function
				array( $this, 'page' )
			);
		} else {
			add_submenu_page(
				// Page Slug
				$page['parent'],
				// Page Title
				$page['title'],
				// Menu Title
				$page['title'],
				// Capability
				$page['capability'],
				// Menu Slug
				$page['id'],
				// Content Function
				array( $this, 'page' )
			);
		}

	}


	public function notices() {

		if ( ! isset( $_REQUEST['page'] ) || ! isset( $_REQUEST['settings-updated'] ) ) {
			return;
		}

		$page = $this->config;

		if ( $_REQUEST['page'] === $page['id'] && $_REQUEST['settings-updated'] == true ) {
			echo '<div id="themeplate-message" class="updated"><p><strong>Settings updated.</strong></p></div>';
		}

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
									<?php settings_fields( 'themeplate' ); ?>
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
								</div>
							</div>

							<?php do_action( 'themeplate_settings_' . $this->config['id'] . '-side' ) ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php do_action( 'themeplate_settings_' . $this->config['id'] . '-normal' ) ?>
						</div>
					</div>
				</div>
			</form>
		</div>

		<?php

	}


	public function save( $options ) {

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
