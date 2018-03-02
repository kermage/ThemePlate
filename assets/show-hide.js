(function( $ ) {

	'use strict';


	var $pageTemplate = $( '#page_template' ),
		$postFormat = $( 'input[name="post_format"]' );

	var checkersElements = {
		'template': $pageTemplate,
		'format': $postFormat
	};

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

		var conditions = $this.data( 'show' );

		maybeShowHide( $this.parents( '.themeplate' ), 'show', conditions );
		addEventListener( $this.parents( '.themeplate' ), 'show', conditions );
	});

	$( '.themeplate-hide' ).each( function() {
		var $this = $( this );

		if ( ! $this.data( 'hide' ) ) {
			return;
		}

		var conditions = $this.data( 'hide' );

		maybeShowHide( $this.parents( '.themeplate' ), 'hide', conditions );
		addEventListener( $this.parents( '.themeplate' ), 'hide', conditions );
	});

	function isAvailable( checker ) {
		if ( checkersElements[checker].length ) {
			return true;
		}

		return false;
	}

	function isMet( conditions ) {
		var result;

		for ( var i in conditions ) {
			if ( ! isAvailable( conditions[i]['key'] ) ) {
				continue;
			}

			result = result || checkCallbacks[conditions[i]['key']]( conditions[i]['value'] );
		}

		return result;
	}

	function maybeShowHide( $metabox, type, conditions ) {
		if ( type == 'show' ) {
			isMet( conditions ) ? $metabox.show() : $metabox.hide();
		} else {
			isMet( conditions ) ? $metabox.hide() : $metabox.show();
		}
	}

	function addEventListener( $metabox, type, conditions ) {
		for ( var i in conditions ) {
			if ( ! isAvailable( conditions[i]['key'] ) ) {
				continue;
			}

			eventListeners[conditions[i]['key']]( function() {
				maybeShowHide( $metabox, type, conditions );
			});
		}
	}

}( jQuery ));
