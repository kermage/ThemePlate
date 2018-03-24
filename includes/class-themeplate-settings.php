<?php

/**
 * Setup settings
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_Settings {

	private $config;
	private $tpmb;


	public function __construct( $config ) {

		try {
			$this->tpmb = new ThemePlate_MetaBox( 'options', $config );
		} catch( Exception $e ) {
			return false;
		}

		$this->config = $config;

		add_action( 'admin_init', array( $this, 'add' ) );

	}


	public function add() {

		$config = $this->config;
		$page = ThemePlate()->key . '-' . ( isset( $config['page'] ) ? $config['page'] : ThemePlate()->slug );
		$this->tpmb->object_id( $page );
		$page .= '-' . ( isset( $config['context'] ) ? $config['context'] : 'normal' );

		add_action( 'themeplate_settings_' . $page, array( $this->tpmb, 'layout_postbox' ) );

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

							<?php do_action( 'themeplate_settings_' . $page . '-side' ) ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php do_action( 'themeplate_settings_' . $page . '-normal' ) ?>
						</div>
					</div>
				</div>
			</form>
		</div>

		<?php

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
