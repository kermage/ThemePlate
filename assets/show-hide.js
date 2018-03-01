(function( $ ) {

	'use strict';


	var $pageTemplate = $( '#page_template' );

	$( '.themeplate-show' ).each( function() {
		var $this = $( this );

		if ( $this.data( 'template' ) ) {
			var template = $this.data( 'template' );

			maybeShowHide( $this.parents( '.themeplate' ), 'show', template );

			$pageTemplate.on( 'change', function() {
				maybeShowHide( $this.parents( '.themeplate' ), 'show', template );
			});
		}

	});

	$( '.themeplate-hide' ).each( function() {
		var $this = $( this );

		if ( $this.data( 'template' ) ) {
			var template = $this.data( 'template' );

			maybeShowHide( $this.parents( '.themeplate' ), 'hide', template );

			$pageTemplate.on( 'change', function() {
				maybeShowHide( $this.parents( '.themeplate' ), 'hide', template );
			});
		}

	});

	function isMet( value ) {
		var current = $pageTemplate.val();
		current = current.substr( current.lastIndexOf( '/' ) + 1 );

		return $.inArray( current, value ) > -1;
	}

	function maybeShowHide( $metabox, type, condition ) {
		if ( type == 'show' ) {
			isMet( condition ) ? $metabox.show() : $metabox.hide();
		} else {
			isMet( condition ) ? $metabox.hide() : $metabox.show();
		}
	}

}( jQuery ));
