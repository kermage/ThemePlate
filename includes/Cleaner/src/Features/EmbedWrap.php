<?php

namespace ThemePlate\Cleaner\Features;

use ThemePlate\Cleaner\BaseFeature;

class EmbedWrap extends BaseFeature {

	public function key(): string {

		return 'embed_wrap';

	}


	public function action(): void {

		// Wrap embedded media for easier responsive styling
		add_filter( 'embed_oembed_html', array( $this, 'embed_wrap' ), 10, 3 );

	}


	public function embed_wrap( $cache, $url, $attr ): string {

		return '<div class="embed-responsive embed-responsive-' . $this->calculate_ratio( $attr ) . '">' . $cache . '</div>';

	}


	private function calculate_ratio( $attr ): string {

		$ratio    = '1by1';
		$dividend = $attr['width'];
		$divisor  = $attr['height'];

		if ( isset( $attr['width'], $attr['height'] ) && $attr['width'] !== $attr['height'] ) {
			if ( $attr['height'] > $attr['width'] ) {
				$dividend = $attr['height'];
				$divisor  = $attr['width'];
			}

			$gcd = -1;

			while ( -1 === $gcd ) {
				$remainder = $dividend % $divisor;

				if ( 0 === $remainder ) {
					$gcd = $divisor;
				} else {
					$dividend = $divisor;
					$divisor  = $remainder;
				}
			}

			$hr    = $attr['width'] / $gcd;
			$vr    = $attr['height'] / $gcd;
			$ratio = $hr . 'by' . $vr;
		}

		return $ratio;

	}

}
