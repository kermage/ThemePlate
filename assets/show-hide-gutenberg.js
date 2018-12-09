(function( $ ) {

	'use strict';


	function listenDataChanges() {
		if ( ! wp.data ) {
			return;
		}

		wp.data.subscribe( function() {
			var currentChanges = wp.data.select( 'core/editor' ).getPostEdits();

			console.log( currentChanges );
		} );
	}

	listenDataChanges();

}( jQuery ));
