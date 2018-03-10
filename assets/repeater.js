(function( $ ) {

	'use strict';


	$( document ).on( 'click', '.clone-add', function( e ) {
		e.preventDefault();

		var index = $( this ).siblings( '.themeplate-clone' ).length - 1;
		var $field = $( this ).siblings( '.hidden' );
		var $cloned = $field.clone( true );

		$cloned.find( '[id]' ).each( function() {
			var id = $( this ).attr( 'id' ).replace( '_x', '_' + index );
			$( this ).attr( 'id', id );
		});

		$cloned.removeClass( 'hidden' ).insertBefore( $field );
	});

	$( document ).on( 'click', '.themeplate-clone .attachment-close', function( e ) {
		e.preventDefault();

		var $field = $( this ).parents( '.themeplate-clone' );

		$field.remove();
	});

}( jQuery ));
