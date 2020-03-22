(function( $ ) {

	'use strict';


	$( document ).on( 'click', '.clone-add', function( e ) {
		e.preventDefault();

		var $field = $( this ).siblings( '.hidden' );
		var $cloned = $field.clone( true );

		setIndex( $cloned, getIndex( $field ) );
		$cloned.removeClass( 'hidden' ).insertBefore( $field ).trigger( 'clone' );
		setRequired( $( this ).parent( '.repeatable' ) );
	});

	$( document ).on( 'click', '.themeplate-clone .attachment-close', function( e ) {
		e.preventDefault();

		setRequired( $( this ).parents( '.repeatable' ), true );
		$( this ).closest( '.themeplate-clone' ).remove();
	});

	$( '.field-input.repeatable' ).each( function () {
		var $this = $( this );
		var index = $this.children( '.themeplate-clone' ).length;

		$this.data( 'index', index - 1 );
		$this.sortable( {
			handle: '.themeplate-handle',
			opacity: 0.65,
			items: '> .themeplate-clone:not( .hidden )',
			placeholder: 'themeplate-clone clone-placeholder',
			start: function ( e, ui ) {
				ui.placeholder.height( ui.item.height() );
			},
			update: function() {
				setRequired( $this );
			}
		} );

		setRequired( $this );
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
				if ( ! Object.prototype.hasOwnProperty.call( indexAttributes, i ) ) {
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


	function setRequired( $field, $delayed = false ) {
		$field.find( '.themeplate-clone' ).removeClass( 'required' )
			.slice( 0, $field.data( 'min' ) ).addClass( 'required' );

		if ( $field.data( 'max' ) > 0 && $field.find( '.themeplate-clone' ).length - 1 >= $field.data( 'max' ) + $delayed ) {
			$field.addClass( 'maxed' ).find( '.clone-add' ).hide();
		} else {
			$field.removeClass( 'maxed' ).find( '.clone-add' ).show();
		}
	}

}( jQuery ));
