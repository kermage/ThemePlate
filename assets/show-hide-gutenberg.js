/* global wp */

(function( $, TP ) {

	'use strict';


	var changesHolder = {};


	$.extend( TP.checkCallbacks, {
		'template': function( value ) {
			var current = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'template' );

			if ( TP.compareValue( current, '/', 'contains' ) ) {
				current = current.substr( current.lastIndexOf( '/' ) + 1 );
			}

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
		'format': function( value ) {
			var current = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'format' );
			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
		'parent': function( value ) {
			var current = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'parent' );
			current = parseInt( current );

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
	});


	function listenDataChanges() {
		if ( ! wp.data ) {
			return;
		}

		wp.data.subscribe( function() {
			var currentChanges = wp.data.select( 'core/editor' ).getPostEdits();

			if ( changesHolder !== currentChanges ) {
				changesHolder = currentChanges;
				applyCurrentChanges();
			}
		} );
	}

	function applyCurrentChanges() {
		$( '.themeplate-options' ).each( function() {
			var $this = $( this );
			var $container = TP.getContainer( $this );
			var conditions;

			if ( $this.data( 'show' ) ) {
				conditions = $this.data( 'show' );
				TP.maybeShowHide( $container, 'show', conditions );
			}

			if ( $this.data( 'hide' ) ) {
				conditions = $this.data( 'hide' );
				TP.maybeShowHide( $container, 'hide', conditions );
			}
		});
	}


	$( window ).on( 'load', listenDataChanges );

}( jQuery, window.ThemePlate ));
