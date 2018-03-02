(function( $ ) {

	'use strict';


	var $pageTemplate = $( '#page_template' ),
		$postFormat = $( 'input[name="post_format"]' );

	var checkCallbacks = {
		template: function( value ) {
			var current = $pageTemplate.val();
			current = current.substr( current.lastIndexOf( '/' ) + 1 );

			return $.inArray( current, value ) > -1;
		},
		format: function( value ) {
			var current = $postFormat.filter( ':checked' ).val();
			if ( current == 0 ) {
				current = 'standard';
			}

			return $.inArray( current, value ) > -1;
		}
	};

	var eventListeners = {
		template: function( callback ) {
			$pageTemplate.on( 'change', callback );
		},
		format: function( callback ) {
			$postFormat.on( 'change', callback );
		}
	}

	$( '.themeplate-show' ).each( function() {
		var $this = $( this );

		if ( ! $this.data( 'show' ) ) {
			return;
		}

		var condition = $this.data( 'show' );

		maybeShowHide( $this.parents( '.themeplate' ), 'show', condition );
		addEventListener( $this.parents( '.themeplate' ), 'show', condition );
	});

	$( '.themeplate-hide' ).each( function() {
		var $this = $( this );

		if ( ! $this.data( 'hide' ) ) {
			return;
		}

		var condition = $this.data( 'hide' );

		maybeShowHide( $this.parents( '.themeplate' ), 'hide', condition );
		addEventListener( $this.parents( '.themeplate' ), 'hide', condition );
	});

	function isMet( condition ) {
		for ( var key in condition ) {
			return checkCallbacks[key]( condition[key] );
		}
	}

	function maybeShowHide( $metabox, type, condition ) {
		if ( type == 'show' ) {
			isMet( condition ) ? $metabox.show() : $metabox.hide();
		} else {
			isMet( condition ) ? $metabox.hide() : $metabox.show();
		}
	}

	function addEventListener( $metabox, type, condition ) {
		for ( var key in condition ) {
			eventListeners[key]( function() {
				maybeShowHide( $metabox, type, condition );
			});
		}
	}

}( jQuery ));
