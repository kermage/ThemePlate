// window.ThemePlate = window.ThemePlate || {};

(function( $, TP ) {

	'use strict';


	$.extend( TP.checkersElements, {
		'template': $( '#page_template' ),
		'format': $( 'input[name="post_format"]' ),
		'parent': $( '#parent' ).length ? $( '#parent' ) : $( '#parent_id' ),
	});

	$.extend( TP.checkCallbacks, {
		template: function( value ) {
			var current = TP.checkersElements['template'].val();
			current = current.substr( current.lastIndexOf( '/' ) + 1 );

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
		format: function( value ) {
			var current = TP.checkersElements['format'].filter( ':checked' ).val();

			if ( current === 0 ) {
				current = 'standard';
			}

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
		parent: function( value ) {
			var current = TP.checkersElements['parent'].val();
			current = parseInt( current );

			if ( isNaN( current ) ) {
				current = -1;
			}

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
	});

	$.extend( TP.eventListeners, {
		template: function( callback ) {
			TP.checkersElements['template'].on( 'change', callback );
		},
		format: function( callback ) {
			TP.checkersElements['format'].on( 'change', callback );
		},
		parent: function( callback ) {
			TP.checkersElements['parent'].on( 'change', callback );
		},
	});


	$( document ).ready( function() {
		$( '.themeplate-options' ).each( function() {
			var $this = $( this );
			var $container = TP.getContainer( $this );
			var conditions;

			if ( $this.data( 'show' ) ) {
				conditions = $this.data( 'show' );
				TP.maybeShowHide( $container, 'show', conditions );
				TP.addEventListener( $container, 'show', conditions );
			}

			if ( $this.data( 'hide' ) ) {
				conditions = $this.data( 'hide' );
				TP.maybeShowHide( $container, 'hide', conditions );
				TP.addEventListener( $container, 'hide', conditions );
			}
		});
	});

}( jQuery, window.ThemePlate ));
