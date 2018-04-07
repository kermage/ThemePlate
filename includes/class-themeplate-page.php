<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Page {

	private $config;

	private $defaults = array(
		'capability' => 'manage_options',
		'parent'     => '',
		'menu'       => '',
		'icon'       => '',
		'position'   => null,
	);


	public function __construct( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			throw new Exception();
		}

		if ( ! array_key_exists( 'id', $config ) || ! array_key_exists( 'title', $config ) ) {
			throw new Exception();
		}

		$this->config = ThemePlate_Helpers::fool_proof( $this->defaults, $config );

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );

	}


	public function init() {

		$option = ThemePlate()->key . '-' . $this->config['id'];

		register_setting( $option, $option, array( $this, 'save' ) );

	}


	public function menu() {

		$page = $this->config;

		$page['id'] = ThemePlate()->key . '-' . $page['id'];

		if ( empty( $page['parent'] ) ) {
			$this->add_menu( $page );
		} else {
			$page['parent'] = ThemePlate()->key . '-' . $page['parent'];

			if ( $page['parent'] === $page['id'] ) {
				$this->add_menu( $page );
				$page['menu'] = $page['title'];
			}

			$this->add_submenu( $page );
		}

	}


	private function add_menu( $page ) {

		add_menu_page(
			// Page Title
			$page['title'],
			// Menu Title
			$page['menu'] ? $page['menu'] : $page['title'],
			// Capability
			$page['capability'],
			// Menu Slug
			$page['id'],
			// Content Function
			array( $this, 'create' ),
			// Icon URL
			$page['icon'],
			// Menu Order
			$page['position']
		);

	}


	private function add_submenu( $page ) {

		add_submenu_page(
			// Parent Slug
			$page['parent'],
			// Page Title
			$page['title'],
			// Menu Title
			$page['menu'] ? $page['menu'] : $page['title'],
			// Capability
			$page['capability'],
			// Menu Slug
			$page['id'],
			// Content Function
			array( $this, 'create' )
		);

	}


	public function notices() {

		if ( ! isset( $_REQUEST['page'] ) || ! isset( $_REQUEST['settings-updated'] ) ) {
			return;
		}

		if ( $_REQUEST['page'] === $this->config['id'] && 'true' === $_REQUEST['settings-updated'] ) {
			echo '<div id="themeplate-message" class="updated"><p><strong>Settings updated.</strong></p></div>';
		}

	}


	public function create() {

		wp_enqueue_script( 'post' );
		wp_enqueue_media();

		$page = ThemePlate()->key . '-' . $this->config['id'];

		?>

		<div class="wrap">
			<h1><?php echo get_admin_page_title(); ?></h1>

			<form action="options.php" method="post">
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<?php do_action( 'themeplate_settings_' . $page . '_content' ); ?>
						</div>

						<div id="postbox-container-1" class="postbox-container">
							<div id="submitdiv" class="postbox">
								<h2>Publish</h2>

								<div id="major-publishing-actions">
									<?php settings_fields( $page ); ?>
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
								</div>
							</div>

							<div id="side-sortables" class="meta-box-sortables">
								<?php do_action( 'themeplate_settings_' . $page . '_side' ); ?>
							</div>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<div id="normal-sortables" class="meta-box-sortables">
								<?php do_action( 'themeplate_settings_' . $page . '_normal' ); ?>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>

		<?php

	}


	public function save( $options ) {

		return ThemePlate_Helpers::preprare_save( $options );

	}

}
