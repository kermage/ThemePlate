(function( $ ) {

	'use strict';


	$( document ).on( 'click', '.clone-add', function( e ) {
		e.preventDefault();

		var $field = $( this ).siblings( '.hidden' );
		var $cloned = $field.clone( true );
		var index = getIndex( $field );

		setIndex( $cloned, index );
		$cloned.removeClass( 'hidden' ).insertBefore( $field );
	});

	$( document ).on( 'click', '.themeplate-clone .attachment-close', function( e ) {
		e.preventDefault();

		var $field = $( this ).closest( '.themeplate-clone' );

		$field.remove();
	});

	$( '.field-input.repeatable' ).each( function () {
		var $this = $( this );
		var index = $this.children( '.themeplate-clone' ).length;

		$this.data( 'index', index - 1 );
		$this.sortable( {
			handle: '.themeplate-handle',
			items: '> .themeplate-clone',
			placeholder: 'themaplate-clone clone-placeholder',
			start: function ( e, ui ) {
				ui.placeholder.height( ui.item.height() );
			}
		} );
	} );


	function getIndex( $field ) {
		var $input = $field.closest( '.field-input' );
		var index = $input.data( 'index' );

		$input.data( 'index', index + 1 );
		return index;
	}

	function setIndex( $field, index ) {
		var attributes = ['id','name'];

		$field.find( '[' + attributes.join( '],[' ) + ']' ).each( function() {
			for ( var i in attributes ) {
				if ( $( this ).attr( attributes[i] ) == undefined ) {
					continue;
				}

				var value = $( this ).attr( attributes[i] ).replace( /i-(\d|x)/g, index );
				$( this ).attr( attributes[i], value );
			}
		});
	}

}( jQuery ));
