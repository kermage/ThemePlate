window.ThemePlate = window.ThemePlate || {};

(function( $, TP ) {

	'use strict';


	var editorSpecific = [ 'template', 'format', 'parent' ];

	TP.checkersElements = {
		'parent': $( '#parent' ),
		'role': $( '#role' ),
		'id': $( '#post_ID' ).length ? $( '#post_ID' ) : $( '#tag_ID' ).length ? $( '#tag_ID' ) : $( '#user_id' ),
	};

	TP.checkCallbacks = {
		parent: function( value ) {
			var current = TP.checkersElements['parent'].val();
			current = parseInt( current );

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
		role: function( value ) {
			var current = TP.checkersElements['role'].val();

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
		id: function( value ) {
			var current = TP.checkersElements['id'].val();
			current = parseInt( current );

			return TP.compareValue( current, TP.sureArray( value ), 'in' );
		},
		field: function( argument, operator ) {
			var element = argument[0];
			var value = argument[1];
			var current = TP.getValue( element );

			if ( $.isNumeric( current ) ) {
				current = parseInt( current );
			}

			return TP.compareValue( current, value, operator );
		}
	};

	TP.eventListeners = {
		parent: function( callback ) {
			TP.checkersElements['parent'].on( 'change', callback );
		},
		role: function( callback ) {
			TP.checkersElements['role'].on( 'change', callback );
		},
		field: function( callback, element ) {
			$( element ).on( 'change input', callback );
		}
	};


	TP.getContainer = function( element ) {
		var selector;

		if ( element.closest( '.field-wrapper' ).length ) {
			selector = element.closest( '.field-wrapper' );
		} else {
			var selectorArray = [];
			var selectorID = element.closest( '.themeplate' ).attr( 'id' );
			var toggler =  document.getElementById( selectorID + '-hide' );

			if ( toggler !== null ) {
				selectorArray.push( toggler.parentNode );
			}

			selectorArray.push( document.getElementById( selectorID ) );

			selector = $( selectorArray );
		}

		return selector;
	};

	TP.getValue = function( element ) {
		var $element = $( element );
		var type = $element.prop( 'tagName' );

		if ( type !== 'FIELDSET' ) {
			return $element.val();
		}

		var $items = $element.find( ':checked' );
		var values = [];

		$items.each( function() {
			values.push( $( this ).val() );
		});

		return values;
	};

	TP.sureArray = function( value ) {
		if ( $.isArray( value ) ) {
			return value;
		}

		var array = [];
		array.push( value );

		return array;
	};

	TP.compareValue = function( have, want, operator ) {
		var result = false;

		if ( have === undefined || want === undefined ) {
			return result;
		}

		operator = operator.trim();

		switch ( operator ) {
			case '>':
				result = ( have > want );
				break;
			case '<':
				result = ( have < want );
				break;
			case '>=':
				result = ( have >= want );
				break;
			case '<=':
				result = ( have <= want );
				break;
			case 'in':
				result = ( $.inArray( have, want ) > -1 );
				break;
			case 'contains':
				result = ( have.indexOf( want ) > -1 );
				break;
			case 'between':
				result = TP.compareValue( have, want[0], '>=' ) && TP.compareValue( have, want[1], '<=' );
				break;
			case '=':
			default:
				result = ( have == want );
				break;
		}

		return result;
	};

	TP.isAvailable = function( checker, $strict = false ) {
		if ( ! $strict && checker == 'term' ) {
			return true;
		}

		if ( ! $strict && checker == 'field' ) {
			return true;
		}

		if ( TP.checkersElements[checker] === undefined ) {
			return false;
		}

		if ( TP.checkersElements[checker].length === 0	) {
			return false;
		}

		return true;
	};

	TP.isMet = function( conditions, logic = 'OR' ) {
		var result = ( logic != 'OR' );

		for ( var i in conditions ) {
			if ( ! conditions.hasOwnProperty( i ) ) {
				continue;
			}

			var condition = conditions[i];

			if ( $.isArray( condition ) ) {
				result = result || TP.isMet( condition, 'AND' );
				continue;
			}

			var key = condition['key'];
			var value = condition['value'];
			var operator = ( condition['operator'] !== undefined ) ? condition['operator'] : '=';
			var invert = false;

			if ( ! TP.checkersElements.hasOwnProperty( key ) && ! TP.compareValue( key, editorSpecific, 'in' ) ) {
				value = [ condition['key'], condition['value'] ];

				if ( key[0] === '#' ) {
					key = 'field';
				} else {
					key = 'term';
				}
			}

			if ( ! TP.isAvailable( key ) && ! TP.compareValue( key, editorSpecific, 'in' ) ) {
				continue;
			}

			if ( TP.compareValue( operator, '!', 'contains' ) || TP.compareValue( operator, 'not', 'contains' ) ) {
				invert = true;
				operator = operator.replace( '!', '' ).replace( 'not', '' );
				operator = operator ? operator : '=';
			}

			var returned = TP.checkCallbacks[key]( value, operator );

			if ( invert ) {
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
	};

	TP.maybeShowHide = function( $container, type, conditions ) {
		if ( type == 'show' ) {
			TP.isMet( conditions ) ? $container.show() : $container.hide();
		} else {
			TP.isMet( conditions ) ? $container.hide() : $container.show();
		}
	};

	TP.addEventListener = function( $container, type, conditions, origConditions = conditions ) {
		for ( var i in conditions ) {
			if ( ! conditions.hasOwnProperty( i ) ) {
				continue;
			}

			var condition = conditions[i];

			if ( $.isArray( condition ) ) {
				TP.addEventListener( $container, type, condition, conditions );
				continue;
			}

			var key = condition['key'];
			var value = condition['value'];

			if ( ! TP.checkersElements.hasOwnProperty( key ) && ! TP.compareValue( key, editorSpecific, 'in' ) ) {
				value = condition['key'];

				if ( key[0] === '#' ) {
					key = 'field';
				} else {
					key = 'term';
				}
			}

			if ( ! TP.isAvailable( key, true ) ) {
				continue;
			}

			if ( key == 'id' ) {
				continue;
			}

			TP.eventListeners[key]( function() {
				TP.maybeShowHide( $container, type, origConditions );
			}, value );
		}
	};


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
