jQuery( document ).ready( function( $ ) {

	function HideAll() {
		$( 'div[id^="themeplate_"][id $="_post"]' ).hide();
	};

	HideAll();

	$( '#themeplate_' + $( 'input[name=post_format]:checked' ).val() + '_post' ).show();

	$( '#post-formats-select input' ).change( function() {
		HideAll();
		if ( $( '#themeplate_' + $( this ).val() + '_post' ).length ) {
			$( '#themeplate_' + $( this ).val() + '_post' ).show();

			$( 'html,body' ).animate({
				scrollTop: $( '#themeplate_' + $( this ).val() + '_post' ).offset().top
			});
		}
	});

	$( '.wp-color-picker' ).wpColorPicker();

	var meta_media_frame;
	$( 'input[id^="themeplate_"][id $="_button"]' ).click( function( e ) {
		e.preventDefault();

		// if ( meta_media_frame ) {
		// 	meta_media_frame.open();
		// 	return;
		// }

		var isMultiple = false;
		if ( $( this ).attr( 'multiple' ) ) {
			isMultiple = true;
		}

		meta_media_frame = wp.media.frames.meta_media_frame = wp.media({
			title: 'Select Media',
			multiple: isMultiple
		});

		meta_media_frame.on( 'open', function() {
			var selection = meta_media_frame.state().get( 'selection' );
			var selected = $( '#' + e.target.id.replace( '_button', '' ) ).val();

			if ( selected && isMultiple ) {
				selected = selected.split( ',' );
				selected.forEach( function( id ) {
					attachment = wp.media.attachment( id );
					selection.add( attachment );
				});
			} else if ( selected && !isMultiple ) {
				selection.add( wp.media.attachment( selected ) );
			}
		});

		meta_media_frame.on( 'select', function() {
			var selection = meta_media_frame.state().get( 'selection' ).toJSON();
			var selected = [];

			selection.map( function( media ) {
				selected.push( media.id );
			});

			$( '#' + e.target.id.replace( '_button', '_preview' ) + ( isMultiple ? '.multiple' : '' ) ).html( '' );

			selection.forEach( function( media ) {
				src = ( media.type == 'image' ? media.url : media.icon );
				centered = '<div class="centered"><img src="' + src + '"/></div>';
				filename = '<div class="filename"><div>' + media.filename + '</div></div>';
				fieldname = 'themeplate[' + e.target.id.replace( 'themeplate_', '' ).replace( '_button', '' ) + ']' + ( isMultiple ? '[]' : '' );
				field = '<input type="hidden" name="' + fieldname + '" value="' + media.id + '">';

				preview = '<div id="file-' + media.id + '" class="attachment"><div class="attachment-preview landscape"><div class="thumbnail">' + centered + filename +'</div></div>' + field + '</div>';
				$( '#' + e.target.id.replace( '_button', '_preview' ) + ( isMultiple ? '.multiple' : '' ) ).append( preview );
			});

			$( '#' + e.target.id.replace( '_button', '' ) ).val( selected.toString() );
			$( '#' + e.target.id ).val( 'Re-select' );
			$( '#' + e.target.id.replace( '_button', '_remove' ) ).attr( 'type', 'button' );
		});

		meta_media_frame.open();
	});

	$( 'input[id^="themeplate_"][id $="_remove"]' ).click( function( e ) {
		e.preventDefault();

		var isMultiple = false;
		if ( $( this ).attr( 'multiple' ) ) {
			isMultiple = true;
		}

		$( '#' + e.target.id.replace( '_remove', '_preview' ) + ( isMultiple ? '.multiple' : '' ) ).html( '' );
		$( '#' + e.target.id.replace( '_remove', '' ) ).val('');
		$( '#' + e.target.id.replace( '_remove', '_button' ) ).val( 'Select' );
		$( '#' + e.target.id ).attr( 'type', 'hidden' );
	});

	$( 'div[id^="themeplate_"][id $="_preview"].multiple' ).sortable( {
		items: '.attachment',
		opacity: 0.75,
		update: function( event, ui ) {
			order = $( this ).sortable( 'toArray' ).toString().replace( /file-/g, '' );
			field = $( this ).attr( 'id' ).replace( '_preview', '' );
			$( '#' + field ).val( order );
		}
	});

});