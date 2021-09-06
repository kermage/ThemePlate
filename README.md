# ThemePlate NavWalker

> A clean navwalker

## Extend / Customize

### Simplest (need custom classes)

```php
class Clean_Navbar extends ThemePlate\NavWalker {
	public $classes = array(
		'sub-menu' => 'dropdown-menu',
		'has-sub'  => 'dropdown',
		'active'   => 'active',
		'item'     => 'nav-item',
	);
}
```

### Complex (more control?)

```php
class Clean_Navbar extends ThemePlate\NavWalker {
	public function submenu_css_class( $classes, $args, $depth ) {
		$classes[] = 'sub-' . $depth;

		return $classes;
	}

	public function css_class( $classes, $item, $args ) {
		if ( '_blank' === $item->target ) {
			$classes[] = 'external';
		}

		return $classes;
	}

	public function item_id( $id, $item, $args, $depth ) {
		if ( 10 === $item->ID ) {
			$id = 'i-ten';
		}

		return $id;
	}

	public function link_attributes( $atts, $item, $args, $depth ) {
		if ( in_array( 'icon', $item->classes, true ) ) {
			$atts['aria-hidden'] = true;
		}

		return $atts;
	}
}
```
