(function( $ ) {

	'use strict';


	var changesHolder = {};
	var initialized = false;


	function listenDataChanges() {
		if ( ! wp.data ) {
			return;
		}

		wp.data.subscribe( function() {
			var currentChanges = wp.data.select( 'core/editor' ).getPostEdits();

			if ( ! initialized ) {
				changesHolder = wp.data.select( 'core/editor' ).getCurrentPost();
				initialized = true;
			}

			if ( changesHolder !== currentChanges ) {
				changesHolder = currentChanges;
				applyCurrentChanges();
			}
		} );
	}

	function applyCurrentChanges() {
		$( '.themeplate-options' ).each( function() {
			var $this = $( this );
			var $container = getContainer( $this );
			var conditions;

			if ( $this.data( 'show' ) ) {
				conditions = $this.data( 'show' );
				maybeShowHide( $container, 'show', conditions );
			}

			if ( $this.data( 'hide' ) ) {
				conditions = $this.data( 'hide' );
				maybeShowHide( $container, 'hide', conditions );
			}
		});
	}

	function getContainer( element ) {
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
	}

	function maybeShowHide( $container, type, conditions ) {
		if ( type == 'show' ) {
			isMet( conditions ) ? $container.show() : $container.hide();
		} else {
			isMet( conditions ) ? $container.hide() : $container.show();
		}
	}

	function isMet( conditions, logic = 'OR' ) {
		var result = ( logic != 'OR' );

		for ( var i in conditions ) {
			if ( ! conditions.hasOwnProperty( i ) ) {
				continue;
			}

			var condition = conditions[i];

			if ( $.isArray( condition ) ) {
				result = result || isMet( condition, 'AND' );
				continue;
			}

			var current = changesHolder[ condition['key'] ];
			var wanted = condition['value'];
			var operator = ( condition['operator'] !== undefined ) ? condition['operator'] : '=';
			var invert = false;

			if ( compareValue( operator, '!', 'contains' ) || compareValue( operator, 'not', 'contains' ) ) {
				invert = true;
				operator = operator.replace( '!', '' ).replace( 'not', '' );
				operator = operator ? operator : '=';
			}

			var returned = compareValue( current, wanted, operator );

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
	}

	function sureArray( value ) {
		if ( $.isArray( value ) ) {
			return value;
		}

		var array = [];
		array.push( value );

		return array;
	}

	function compareValue( have, want, operator ) {
		var result = false;

		operator = operator.trim();

		switch ( operator ) {
			default:
			case '=':
				result = ( have == want );
				break;
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
				result = compareValue( have, want[0], '>=' ) && compareValue( have, want[1], '<=' );
				break;
		}

		return result;
	}


	$( window ).on( 'load', listenDataChanges );

}( jQuery ));
