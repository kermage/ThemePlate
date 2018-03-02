(function( $ ) {

	'use strict';


	var $pageTemplate = $( '#page_template' ),
		$postFormat = $( 'input[name="post_format"]' );

	$( '.themeplate-show' ).each( function() {
		var $this = $( this );

		if ( $this.data( 'template' ) ) {
			var template = $this.data( 'template' );

			maybeShowHide( $this.parents( '.themeplate' ), 'show', 'template', template );

			$pageTemplate.on( 'change', function() {
				maybeShowHide( $this.parents( '.themeplate' ), 'show', 'template', template );
			});
		}

		if ( $this.data( 'format' ) ) {
			var format = $this.data( 'format' );

			maybeShowHide( $this.parents( '.themeplate' ), 'show', 'format', format );

			$postFormat.on( 'change', function() {
				maybeShowHide( $this.parents( '.themeplate' ), 'show', 'format', format );
			});
		}

	});

	$( '.themeplate-hide' ).each( function() {
		var $this = $( this );

		if ( $this.data( 'template' ) ) {
			var template = $this.data( 'template' );

			maybeShowHide( $this.parents( '.themeplate' ), 'hide', 'template', template );

			$pageTemplate.on( 'change', function() {
				maybeShowHide( $this.parents( '.themeplate' ), 'hide', 'template', template );
			});
		}

		if ( $this.data( 'format' ) ) {
			var format = $this.data( 'format' );

			maybeShowHide( $this.parents( '.themeplate' ), 'hide', 'format', format );

			$postFormat.on( 'change', function() {
				maybeShowHide( $this.parents( '.themeplate' ), 'hide', 'format', format );
			});
		}

	});

	function isMet( check, value ) {
		var current;

		if ( check == 'template' ) {
			current = $pageTemplate.val();
			current = current.substr( current.lastIndexOf( '/' ) + 1 );
		} else {
			current = $postFormat.filter( ':checked' ).val();

			if ( current == 0 ) {
				current = 'standard';
			}
		}

		return $.inArray( current, value ) > -1;
	}

	function maybeShowHide( $metabox, type, check, condition ) {
		if ( type == 'show' ) {
			isMet( check, condition ) ? $metabox.show() : $metabox.hide();
		} else {
			isMet( check, condition ) ? $metabox.hide() : $metabox.show();
		}
	}

}( jQuery ));
