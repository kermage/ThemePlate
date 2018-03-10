(function( $ ) {

	'use strict';


	$( document ).on( 'click', '.clone-add', function( e ) {
		e.preventDefault();

		var index = $( this ).siblings( '.themeplate-clone' ).length - 1;
		var $field = $( this ).siblings( '.hidden' );
		var $cloned = $field.clone( true );

		setIndex( $cloned, index );
		$cloned.removeClass( 'hidden' ).insertBefore( $field );
	});

	$( document ).on( 'click', '.themeplate-clone .attachment-close', function( e ) {
		e.preventDefault();

		var index = 0;
		var $field = $( this ).parents( '.themeplate-clone' );
		var $input = $( this ).parents( '.field-input' );

		$field.remove();
		$input.children( '.themeplate-clone' ).not( '.hidden' ).each( function() {
			setIndex( $( this ), index );
			index++;
		});
	});

	$( '.field-input.repeatable' ).each( function () {
		$( this ).sortable( {
			handle: '.themeplate-handle',
			items: '> .themeplate-clone',
			placeholder: 'themaplate-clone clone-placeholder',
			start: function ( e, ui ) {
				ui.placeholder.height( ui.item.height() );
			},
			stop: function() {
				var index = 0;

				$( this ).children( '.themeplate-clone' ).not( '.hidden' ).each( function() {
					setIndex( $( this ), index );
					index++;
				});
			}
		} );
	} );

	function setIndex( $input, index ) {
		var attributes = ['id','name'];

		$input.find( '[' + attributes.join( '],[' ) + ']' ).each( function() {
			for ( var i in attributes ) {
				if ( $( this ).attr( attributes[i] ) == undefined ) {
					continue;
				}

				var value = $( this ).attr( attributes[i] ).replace( /-(\d|x)/g, '-' + index );
				$( this ).attr( attributes[i], value );
			}
		});
	}

}( jQuery ));
