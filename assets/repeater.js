(function( $ ) {

	'use strict';


	$( document ).on( 'click', '.clone-add', function( e ) {
		e.preventDefault();

		var $field = $( this ).siblings( '.hidden' );
		var $cloned = $field.clone( true );

		setIndex( $cloned, getIndex( $field ) );
		$cloned.removeClass( 'hidden' ).insertBefore( $field ).trigger( 'clone' );
	});

	$( document ).on( 'click', '.themeplate-clone .attachment-close', function( e ) {
		e.preventDefault();

		$( this ).closest( '.themeplate-clone' ).remove();
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

	var indexAttributes = ['id', 'name', 'for'];

	function setIndex( $field, index ) {
		$field.find( '[' + indexAttributes.join( '],[' ) + ']' ).each( function() {
			for ( var i in indexAttributes ) {
				if ( ! indexAttributes.hasOwnProperty( i ) ) {
					continue;
				}

				if ( $( this ).attr( indexAttributes[i] ) === undefined ) {
					continue;
				}

				var value = $( this ).attr( indexAttributes[i] ).replace( /i-(\d|x)/, index );
				$( this ).attr( indexAttributes[i], value );
			}
		});
	}

}( jQuery ));
