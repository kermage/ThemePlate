jQuery.noConflict();

(function( $ ) {

	'use strict';


	var post_formats = $( 'input[name=post_format]' ).map( function() {
		return this.value
	}).get()

	function HideAll() {
		$.each( post_formats, function( i, val ) {
			if ( val == 0 ) {
				val = 'standard';
			}
			$( 'div[id^="themeplate_"][id $="' + val + '_post"]' ).hide();
		});
	};

	HideAll();

	$( 'div[id^="themeplate_"][id $="' + $( 'input[name=post_format]:checked' ).val() + '_post"]' ).show();

	$( '#post-formats-select input' ).change( function() {
		HideAll();
		if ( $( 'div[id^="themeplate_"][id $="' + $( this ).val() + '_post"]' ).length ) {
			$( 'div[id^="themeplate_"][id $="' + $( this ).val() + '_post"]' ).show();

			$( 'html,body' ).animate({
				scrollTop: $( 'div[id^="themeplate_"][id $="' + $( this ).val() + '_post"]').offset().top - 50
			});
		}
	});


	$( '.themeplate-color-picker' ).wpColorPicker();


	var meta_media_frame, isMultiple;
	var parent, selection, selected, attachment;
	var src, centered, filename, fieldname, field, close, preview, order;

	$( document ).on( 'click', '.themeplate-file .attachment-add', function( e ) {
		e.preventDefault();

		parent = $( this ).parents( '.themeplate-file' );

		isMultiple = false;
		if ( parent.hasClass( 'multiple' ) ) {
			isMultiple = true;
		}

		fieldname = parent.data( 'key' ) + '[' + parent.attr( 'id' ) + ']' + ( isMultiple ? '[]' : '' );

		meta_media_frame = wp.media.frames.meta_media_frame = wp.media({
			title: 'Select Media',
			multiple: isMultiple
		});

		meta_media_frame.on( 'select', function() {
			selection = meta_media_frame.state().get( 'selection' ).toJSON();

			if ( ! isMultiple ) {
				parent.find( '.attachment-add' ).addClass( 'hidden' );
			}

			parent.find( '> input[type="hidden"]' ).remove();

			selection.forEach( function( media ) {
				src = ( media.type == 'image' ? media.url : media.icon );
				centered = '<div class="centered"><img src="' + src + '"/></div>';
				filename = '<div class="filename"><div>' + media.filename + '</div></div>';
				field = '<input type="hidden" name="' + fieldname + '" value="' + media.id + '">';
				close = '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';

				preview = '<div class="attachment"><div class="attachment-preview landscape"><div class="thumbnail">' + centered + filename +'</div></div>' + close + field + '</div>';
				parent.find( '.preview-holder' ).append( preview );
			});

			parent.find( '.attachments-clear' ).removeClass( 'hidden' );
		});

		meta_media_frame.open();
	});

	$( document ).on( 'click', '.themeplate-file .attachments-clear', function( e ) {
		e.preventDefault();

		parent = $( this ).parents( '.themeplate-file' );

		isMultiple = false;
		if ( parent.hasClass( 'multiple' ) ) {
			isMultiple = true;
		}

		fieldname = parent.data( 'key' ) + '[' + parent.attr( 'id' ) + ']' + ( isMultiple ? '[]' : '' );
		field = '<input type="hidden" name="' + fieldname + '" value="">';

		parent.find( '.preview-holder' ).html( '' )
		parent.append( field );
		$( this ).addClass( 'hidden' );
	});

	$( document ).on( 'click', '.themeplate .attachment-close', function( e ) {
		e.preventDefault();

		parent = $( this ).parents( '.themeplate-file' );

		isMultiple = false;
		if ( parent.hasClass( 'multiple' ) ) {
			isMultiple = true;
		}

		attachment = $( this ).parents( '.attachment' );

		if ( ! isMultiple ) {
			attachment.siblings( '.placeholder' ).find( '.attachment-add' ).removeClass( 'hidden' );
		}

		attachment.remove();

		if ( ! parent.find( '.preview-holder' ).html().length ) {
			parent.find( '.attachments-clear' ).addClass( 'hidden' );
		}

		if ( ! parent.find( '.preview-holder' ).html().length || ! isMultiple ) {
			fieldname = parent.data( 'key' ) + '[' + parent.attr( 'id' ) + ']' + ( isMultiple ? '[]' : '' );
			field = '<input type="hidden" name="' + fieldname + '" value="">';
			parent.append( field );
		}
	});

	$( '.themeplate-file.multiple' ).sortable( {
		items: '.attachment',
		opacity: 0.75,
		update: function( event, ui ) {
			order = $( this ).sortable( 'toArray' ).toString().replace( /file-/g, '' );
			field = $( this ).attr( 'id' ).replace( '_preview', '' );
			$( '#' + field ).val( order );
		}
	});


	$( document ).on( 'ready', function() {
		$( '.themeplate-select2' ).each( function() {
			var $select = $( this );

			$select.select2( {
				allowClear: true,
				placeholder: '',
				dropdownCssClass: 'themeplate',
				containerCssClass: 'themeplate'
			});

			if ( $select.attr( 'multiple' ) ) {
				var $ul = $select.next( '.select2-container' ).find( 'ul' );

				$ul.sortable( {
					stop: function() {
						$ul.find( '.select2-selection__choice' ).each( function() {
							var $option = $( $( this ).data( 'data' ).element );
							$option.detach().appendTo( $select );
						});
					}
				});
			}
		});
	});

}( jQuery ));
