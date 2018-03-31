<?php

/**
 * Clean Nav Walker
 *
 * @package ThemePlate
 * @since 0.1.0
 */


class ThemePlate_NavWalker extends Walker {

	public $db_fields = array(
		'parent' => 'menu_item_parent',
		'id'     => 'db_id',
	);


	public $class = array(
		'sub-menu' => 'sub-menu',
		'has-sub'  => 'has-sub',
		'active'   => 'active',
	);


	public function attributes( $item, $args ) {

		$atts = array();
		return $atts;

	}


	public function start_lvl( &$output, $depth = 0, $args = array() ) {

		$output .= '<ul class="' . $this->class['sub-menu'] . '">';

	}


	public function end_lvl( &$output, $depth = 0, $args = array() ) {

		$output .= '</ul>';

	}


	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = preg_replace( '/current[-_](menu|page)[-_](item|parent|ancestor)|(menu|page)[-_\w+]+/', '', $classes );
		if ( $args->walker->has_children ) {
			$classes[] = $this->class['has-sub'];
		}
		if ( $item->current ) {
			$classes[] = $this->class['active'];
		}
		$classes = join( ' ', array_filter( $classes ) );
		$output .= '<li' . ( ( $classes ) ? ' class="' . esc_attr( $classes ) . '"' : '' ) . '>';

		$atts = array();

		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		$atts = array_merge( $atts, $this->attributes( $item, $args ) );
		$atts = array_filter( $atts );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );

				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$output .= $args->before;
		$output .= '<a' . $attributes . '>';
		$output .= $args->link_before . $item->title . $args->link_after;
		$output .= '</a>';
		$output .= $args->after;

	}


	public function end_el( &$output, $item, $depth = 0, $args = array() ) {

		$output .= '</li>';

	}
}
