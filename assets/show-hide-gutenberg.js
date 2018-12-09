(function( $ ) {

	'use strict';


	var changesHolder = {};


	function listenDataChanges() {
		if ( ! wp.data ) {
			return;
		}

		wp.data.subscribe( function() {
			var currentChanges = wp.data.select( 'core/editor' ).getPostEdits();

			if ( changesHolder !== currentChanges ) {
				console.log( currentChanges );
				changesHolder = currentChanges;
				applyCurrentChanges();
			}
		} );
	}

	function applyCurrentChanges() {
		console.log( changesHolder );
	}


	listenDataChanges();

}( jQuery ));
