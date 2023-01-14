<?php

/**
 * Clean Nav Walker
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use Walker_Nav_Menu;

if ( ! class_exists( 'Walker_Nav_Menu' ) ) {
	return;
}

class NavWalker extends Walker_Nav_Menu {

	private $defaults = array(
		'sub-menu' => 'sub-menu',
		'has-sub'  => 'has-sub',
		'active'   => 'active',
		'item'     => '',
		'depth'    => '',
	);

	public static $fallback = array(
		'Click here',
		'to add',
		'a menu',
	);

	public $classes = array();
	public $class   = array();


	public function __construct() {

		$this->classes = array_merge( $this->defaults, $this->class, $this->classes );

		add_filter( 'nav_menu_submenu_css_class', array( $this, 'submenu_css_class' ), PHP_INT_MAX, 3 );
		add_filter( 'nav_menu_css_class', array( $this, 'css_class' ), PHP_INT_MAX, 4 );
		add_filter( 'nav_menu_item_id', array( $this, 'item_id' ), PHP_INT_MAX, 4 );
		add_filter( 'nav_menu_link_attributes', array( $this, 'link_attributes' ), PHP_INT_MAX, 4 );

	}


	public function submenu_css_class( $classes, $args, $depth ) {

		if ( ! $args->walker instanceof $this ) {
			return $classes;
		}

		$classes = array( $this->classes['sub-menu'] );

		if ( ! empty( $this->classes['depth'] ) ) {
			$classes[] = $this->classes['depth'] . $depth;
		}

		return $classes;

	}


	public function css_class( $classes, $menu_item, $args, $depth ) {

		if ( ! $args->walker instanceof $this ) {
			return $classes;
		}

		$classes = array( $this->classes['item'] );

		if ( $args->walker->has_children ) {
			$classes[] = $this->classes['has-sub'];
		}

		if ( isset( $menu_item->current ) && $menu_item->current ) {
			$classes[] = $this->classes['active'];
		}

		if ( ! empty( $this->classes['depth'] ) ) {
			$classes[] = $this->classes['depth'] . $depth;
		}

		return array_filter( $classes );

	}


	public function item_id( $menu_id, $menu_item, $args, $depth ) {

		if ( ! $args->walker instanceof $this ) {
			return $menu_id;
		}

		if ( 'menu-item-' . $menu_item->ID === $menu_id ) {
			$menu_id = '';
		}

		return $menu_id;

	}


	public function link_attributes( $atts, $menu_item, $args, $depth ) {

		if ( ! $args->walker instanceof $this ) {
			return $atts;
		}

		if ( method_exists( $this, 'attributes' ) ) {
			$atts = array_merge( $atts, call_user_func_array( array( $this, 'attributes' ), func_get_args() ) );
		}

		return array_filter( $atts );

	}


	public static function fallback( $args ) {

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
		$classes = array();

		if ( $args['walker'] instanceof self ) {
			$classes = array( $args['walker']->classes['item'] );
			$classes = array_filter( $classes );

			if ( ! empty( $args['walker']->classes['depth'] ) ) {
				$classes[] = $args['walker']->classes['depth'] . 0;
			}
		}

		$links = static::$fallback;

		if ( ! is_array( $links ) ) {
			$links = (array) $links;
		}

		$links[] = 'theme_location: ' . $args['theme_location'];

		foreach ( $links as $link_text ) {
			$output .= sprintf(
				'<li%1$s><a href="%2$s" target="%3$s">%4$s</a></li>',
				$classes ? ' class="' . implode( ' ', $classes ) . '"' : '',
				admin_url( 'nav-menus.php' ),
				current_user_can( 'edit_theme_options' ) ? '_self' : '_blank',
				$link_text
			);
		}

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
