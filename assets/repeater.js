(function( $ ) {

	'use strict';


	$( document ).on( 'click', 'input[id^="themeplate_"][id $="_cloner"]', function( e ) {
		e.preventDefault();

		var id = e.target.id.replace( 'themeplate_', '' ).replace( '_cloner', '_' );
		var index = $( this ).siblings( '.themeplate-clone' ).length - 1;
		var $field = $( this ).siblings( '.hidden' );
		var $cloned = $field.clone( true );

		$cloned.find( '[id^="' + id + '"]' ).each( function() {
			$( this ).attr( 'id', id + index );
		});

		$cloned.removeClass( 'hidden' ).insertBefore( $field );
	});

	$( document ).on( 'click', '.themeplate-clone .attachment-close', function( e ) {
		e.preventDefault();

		var $field = $( this ).parents( '.themeplate-clone' );

		$field.remove();
	});

}( jQuery ));
