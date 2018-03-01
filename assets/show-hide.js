(function( $ ) {

	'use strict';


	var $pageTemplate = $( '#page_template ' );

	$( '.themeplate-show' ).each( function() {
		var $this = $( this );

		if ( $this.data( 'template' ) ) {
			var template = $this.data( 'template' );

			check( $this.parents( '.themeplate' ), template );

			$pageTemplate.on( 'change', function() {
				check( $this.parents( '.themeplate' ), template );
			});
		}

	});

	function check( $metabox, value ) {
		var current = $pageTemplate.val();
		current = current.substr( current.lastIndexOf( '/' ) + 1 );

		if ( $.inArray( current, value ) > -1 ) {
			$metabox.show();
		} else {
			$metabox.hide();
		}
	}

}( jQuery ));
