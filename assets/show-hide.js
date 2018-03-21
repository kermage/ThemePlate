(function( $ ) {

	'use strict';


	var $pageTemplate = $( '#page_template' ),
		$postFormat = $( 'input[name="post_format"]' ),
		$parent = $( '#parent' ).length ? $( '#parent' ) : $( '#parent_id' ),
		$role = $( '#role' ),
		$id = $( '#post_ID' ).length ? $( '#post_ID' ) : $( '#tag_ID' ).length ? $( '#tag_ID' ) : $( '#checkuser_id' );

	var checkersElements = {
		'template': $pageTemplate,
		'format': $postFormat,
		'parent': $parent,
		'role': $role,
		'id': $id
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
		id: function( value ) {
			var current = $id.val();
			current = parseInt( current );

			return $.inArray( current, sureArray( value ) ) > -1;
		},
		term: function( argument ) {
			var taxonomy = argument[0];
			var value = argument[1];
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
		},
		field: function( argument ) {
			var element = argument[0];
			var value = argument[1];
			var current = $( element ).val();

			if ( $.isNumeric( current ) ) {
				current = parseInt( current );
			}

			return $.inArray( current, sureArray( value ) ) > -1;
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
		term: function( callback, taxonomy ) {
			$( '#' + taxonomy + 'checklist' ).on( 'change', callback );
		},
		field: function( callback, element ) {
			$( element ).on( 'input', callback );
		}
	}

	$( '.themeplate-show' ).each( function() {
		var $this = $( this );

		if ( ! $this.data( 'show' ) ) {
			return;
		}

		var conditions = $this.data( 'show' );
		var $container = $this.parents( '.field-wrapper' ).length ? $this.parents( '.field-wrapper' ) : $this.parents( '.themeplate' );

		maybeShowHide( $container, 'show', conditions );
		addEventListener( $container, 'show', conditions );
	});

	$( '.themeplate-hide' ).each( function() {
		var $this = $( this );

		if ( ! $this.data( 'hide' ) ) {
			return;
		}

		var conditions = $this.data( 'hide' );
		var $container = $this.parents( '.field-wrapper' ).length ? $this.parents( '.field-wrapper' ) : $this.parents( '.themeplate' );

		maybeShowHide( $container, 'hide', conditions );
		addEventListener( $container, 'hide', conditions );
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
		if ( checker == 'term' ) {
			return true;
		}

		if ( checker == 'field' ) {
			return true;
		}

		if ( checkersElements[checker] === undefined ) {
			return false;
		}

		if ( checkersElements[checker].length === 0	) {
			return false;
		}

		return true;
	}

	function isMet( conditions, logic = 'OR' ) {
		var result = ( logic != 'OR' );

		for ( var i in conditions ) {
			var condition = conditions[i];

			if ( $.isArray( condition ) ) {
				result = result || isMet( condition, 'AND' );
				continue;
			}

			var key = condition['key'];
			var value = condition['value'];
			var operator = ( condition['operator'] !== undefined ) ? condition['operator'] : '=';

			if ( ! checkersElements.hasOwnProperty( key ) ) {
				value = [ condition['key'], condition['value'] ];

				if ( key[0] === '#' ) {
					key = 'field';
				} else {
					key = 'term';
				}
			}

			if ( ! isAvailable( key ) ) {
				continue;
			}

			var returned = checkCallbacks[key]( value );

			if ( operator[0] === '!' ) {
				returned = ! returned;
			}

			if ( logic == 'OR' ) {
				result = result || returned;

				if ( result ) {
					return result;
				}
			} else {
				result = result && returned;

				if ( ! result ) {
					return result;
				}
			}
		}

		return result;
	}

	function maybeShowHide( $container, type, conditions ) {
		if ( type == 'show' ) {
			isMet( conditions ) ? $container.show() : $container.hide();
		} else {
			isMet( conditions ) ? $container.hide() : $container.show();
		}
	}

	function addEventListener( $container, type, conditions, origConditions = conditions ) {
		for ( var i in conditions ) {
			var condition = conditions[i];

			if ( $.isArray( condition ) ) {
				addEventListener( $container, type, condition, conditions );
				continue;
			}

			var key = condition['key'];
			var value = condition['value'];

			if ( ! checkersElements.hasOwnProperty( key ) ) {
				value = condition['key'];

				if ( key[0] === '#' ) {
					key = 'field';
				} else {
					key = 'term';
				}
			}

			if ( ! isAvailable( key ) ) {
				continue;
			}

			if ( key == 'id' ) {
				continue;
			}

			eventListeners[key]( function() {
				maybeShowHide( $container, type, origConditions );
			}, value );
		}
	}

}( jQuery ));
