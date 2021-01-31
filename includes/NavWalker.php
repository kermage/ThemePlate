<?php

/**
 * Clean Nav Walker
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

class NavWalker extends \Walker_Nav_Menu {

	public function __construct() {

		add_filter( 'nav_menu_submenu_css_class', array( $this, 'submenu_css_class' ), 0 );
		add_filter( 'nav_menu_css_class', array( $this, 'css_class' ), 0, 4 );
		add_filter( 'nav_menu_item_id', array( $this, 'item_id' ), 0, 2 );
		add_filter( 'nav_menu_link_attributes', array( $this, 'link_attributes' ), 0, 3 );

	}


	public $class = array(
		'sub-menu' => 'sub-menu',
		'has-sub'  => 'has-sub',
		'active'   => 'active',
		'item'     => '',
	);


	public function attributes( $item, $args ) {

		return array();

	}


	public function submenu_css_class() {

		return array( $this->class['sub-menu'] );

	}


	public function css_class( $classes, $item, $args ) {

		$classes = array( $this->class['item'] );

		if ( $args->walker->has_children ) {
			$classes[] = $this->class['has-sub'];
		}

		if ( $item->current ) {
			$classes[] = $this->class['active'];
		}

		return $classes;

	}


	public function item_id( $id, $item ) {

		if ( 'menu-item-' . $item->ID === $id ) {
			$id = '';
		}

		return $id;

	}


	public function link_attributes( $atts, $item, $args ) {

		$atts = array_merge( $atts, $this->attributes( $item, $args ) );

		return array_filter( $atts );

	}


	public static function fallback( $args ) {

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return false;
		}

		$output = '';

		if ( $args['container'] ) {
			$output .= '<' . $args['container'];

			if ( $args['container_id'] ) {
				$output .= ' id="' . $args['container_id'] . '"';
			}

			if ( $args['container_class'] ) {
				$output .= ' class="' . $args['container_class'] . '"';
			}

			$output .= '>';
		}

		$output .= '<ul';

		if ( $args['menu_id'] ) {
			$output .= ' id="' . $args['menu_id'] . '"';
		}

		if ( $args['menu_class'] ) {
			$output .= ' class="' . $args['menu_class'] . '"';
		}

		$output .= '>';
		$output .= '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">Click here</a></li>';
		$output .= '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">to add</a></li>';
		$output .= '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">a menu</a></li>';
		$output .= '</ul>';

		if ( $args['container'] ) {
			$output .= '</' . $args['container'] . '>';
		}

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}

		return true;

	}

}
