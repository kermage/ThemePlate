<?php

namespace ThemePlate\Cleaner;

abstract class BaseFeature {

	public const PREFIX = 'tpc_';

	public function register() {

		if ( current_theme_supports( $this->feature() ) ) {
			$this->action();
		}

	}

	protected function arguments() {

		return get_theme_support( $this->feature() );

	}

	public function feature(): string {

		return self::PREFIX . $this->key();

	}

	protected function enabled( string $option ): bool {

		$args = $this->arguments();

		return empty( $args[0] ) || in_array( $option, $args[0], true );

	}

	abstract public function key(): string;

	abstract public function action(): void;

}
