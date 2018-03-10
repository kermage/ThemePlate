(function( $ ) {

	'use strict';


	$( document ).on( 'click', '.clone-add', function( e ) {
		e.preventDefault();

		var index = $( this ).siblings( '.themeplate-clone' ).length - 1;
		var $field = $( this ).siblings( '.hidden' );
		var $cloned = $field.clone( true );
		var attributes = ['id','name'];

		$cloned.find( '[' + attributes.join( '],[' ) + ']' ).each( function() {
			for ( var i in attributes ) {
				if ( $( this ).attr( attributes[i] ) == undefined ) {
					continue;
				}

				var value = $( this ).attr( attributes[i] ).replace( '-x', '-' + index );
				$( this ).attr( attributes[i], value );
			}
		});

		$cloned.removeClass( 'hidden' ).insertBefore( $field );
	});

	$( document ).on( 'click', '.themeplate-clone .attachment-close', function( e ) {
		e.preventDefault();

		var $field = $( this ).parents( '.themeplate-clone' );

		$field.remove();
	});

}( jQuery ));
