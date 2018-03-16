(function( $ ) {

	'use strict';


	var $pageTemplate = $( '#page_template' ),
		$postFormat = $( 'input[name="post_format"]' ),
		$parent = $( '#parent' ).length ? $( '#parent' ) : $( '#parent_id' ),
		$role = $( '#role' );

	var checkersElements = {
		'template': $pageTemplate,
		'format': $postFormat,
		'parent': $parent,
		'role': $role
	};

	var checkCallbacks = {
		template: function( value ) {
			var current = $pageTemplate.val();
			current = current.substr( current.lastIndexOf( '/' ) + 1 );

			return $.inArray( current, sureArray( value ) ) > -1;
		},
		format: function( value ) {
			var current = $postFormat.filter( ':checked' ).val();

			if ( current == 0 ) {
				current = 'standard';
			}

			return $.inArray( current, sureArray( value ) ) > -1;
		},
		parent: function( value ) {
			var current = $parent.val();
			current = parseInt( current );

			if ( isNaN( current ) ) {
				current = -1;
			}

			return $.inArray( current, sureArray( value ) ) > -1;
		},
		role: function( value ) {
			var current = $role.val();

			return $.inArray( current, sureArray( value ) ) > -1;
		},
		term: function( taxonomy, value ) {
			var $checker = $( '#' + taxonomy + 'checklist :checked' );
			var current = [];

			$checker.each( function() {
				current.push( parseInt( $( this ).val() ) );
			});

			for ( var i in current ) {
				if ( $.inArray( current[i], sureArray( value ) ) > -1 ) {
					return true;
				}
			}

			return false;
		}
	};

	var eventListeners = {
		template: function( callback ) {
			$pageTemplate.on( 'change', callback );
		},
		format: function( callback ) {
			$postFormat.on( 'change', callback );
		},
		parent: function( callback ) {
			$parent.on( 'change', callback );
		},
		role: function( callback ) {
			$role.on( 'change', callback );
		},
		term: function( taxonomy, callback ) {
			$( '#' + taxonomy + 'checklist' ).on( 'change', callback );
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

	function sureArray( value ) {
		if ( $.isArray( value ) ) {
			return value;
		}

		var array = [];
		array.push( value );

		return array;
	}

	function isAvailable( checker ) {
		if ( ! checkersElements.hasOwnProperty( checker ) ) {
			return false;
		}

		if ( checkersElements[checker].length ) {
			return true;
		}

		return false;
	}

	function isMet( conditions, relation = 'OR' ) {
		var result;
		var maybeTerms = [];

		for ( var i in conditions ) {
			if ( $.isArray( conditions[i] ) ) {
				result = result || isMet( conditions[i], 'AND' );
				continue;
			}

			if ( ! isAvailable( conditions[i]['key'] ) ) {
				maybeTerms.push( conditions[i] );
				continue;
			}

			if ( relation == 'OR' ) {
				result = result || checkCallbacks[conditions[i]['key']]( conditions[i]['value'] );
			} else {
				result = result && checkCallbacks[conditions[i]['key']]( conditions[i]['value'] );
			}
		}

		if ( ! maybeTerms.length ) {
			return result;
		}

		for ( var i in maybeTerms ) {
			result = result || checkCallbacks['term']( maybeTerms[i]['key'], maybeTerms[i]['value'] );
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

	function addEventListener( $metabox, type, conditions, origConditions = conditions ) {
		var maybeTerms = [];

		for ( var i in conditions ) {
			if ( $.isArray( conditions[i] ) ) {
				addEventListener( $metabox, type, conditions[i], conditions );
				continue;
			}

			if ( ! isAvailable( conditions[i]['key'] ) ) {
				maybeTerms.push( conditions[i] );
				continue;
			}

			eventListeners[conditions[i]['key']]( function() {
				maybeShowHide( $metabox, type, origConditions );
			});
		}

		if ( ! maybeTerms.length ) {
			return;
		}

		for ( var i in maybeTerms ) {
			eventListeners['term']( maybeTerms[i]['key'], function() {
				maybeShowHide( $metabox, type, maybeTerms );
			});
		}
	}

}( jQuery ));
