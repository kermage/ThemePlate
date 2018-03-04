(function( $ ) {

	'use strict';


	$( 'div[id^="themeplate_"].postbox' ).addClass( 'themeplate' );

	$( '.themeplate .form-table.seamless' ).removeClass( 'seamless' )
		.parents( '.themeplate' ).addClass( 'seamless' )
		.find( '.hndle' ).removeClass();

	$( '.themeplate .form-table:empty' ).remove();


	$( '.themeplate-color-picker' ).wpColorPicker();


	var meta_media_frame = wp.media.frames.meta_media_frame;

	$( document ).on( 'click', '.themeplate-file .attachment-add', function( e ) {
		e.preventDefault();

		var $parent = $( this ).parents( '.themeplate-file' );
		var isMultiple = false;

		if ( $parent.hasClass( 'multiple' ) ) {
			isMultiple = true;
		}

		var fieldname = $parent.data( 'key' ) + '[' + $parent.attr( 'id' ) + ']' + ( isMultiple ? '[]' : '' );

		meta_media_frame = wp.media({
			title: 'Select Media',
			multiple: isMultiple
		});

		meta_media_frame.on( 'select', function() {
			var selection = meta_media_frame.state().get( 'selection' ).toJSON();
			var src, centered, filename, field, close, preview;

			if ( isMultiple ) {
				$parent.find( '.attachments-clear' ).removeClass( 'hidden' );
			} else {
				$parent.find( '.attachment-add' ).addClass( 'hidden' );
			}

			$parent.find( '.hidden.placeholder' ).remove();

			selection.forEach( function( media ) {
				src = ( media.type == 'image' ? media.url : media.icon );
				centered = '<div class="centered"><img src="' + src + '"/></div>';
				filename = '<div class="filename"><div>' + media.filename + '</div></div>';
				field = '<input type="hidden" name="' + fieldname + '" value="' + media.id + '">';
				close = '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">Remove</span></button>';

				preview = '<div class="attachment"><div class="attachment-preview landscape"><div class="thumbnail">' + centered + filename +'</div></div>' + close + field + '</div>';
				$parent.find( '.preview-holder' ).append( preview );
			});
		});

		meta_media_frame.open();
	});

	$( document ).on( 'click', '.themeplate-file .attachments-clear', function( e ) {
		e.preventDefault();

		var $parent = $( this ).parents( '.themeplate-file' );
		var isMultiple = false;

		if ( $parent.hasClass( 'multiple' ) ) {
			isMultiple = true;
		}

		var fieldname = $parent.data( 'key' ) + '[' + $parent.attr( 'id' ) + ']';
		var field = '<input type="hidden" class="hidden placeholder" name="' + fieldname + '" value="">';

		$parent.find( '.preview-holder' ).html( '' )
		$parent.append( field );
		$( this ).addClass( 'hidden' );
	});

	$( document ).on( 'click', '.themeplate-file .attachment-close', function( e ) {
		e.preventDefault();

		var $parent = $( this ).parents( '.themeplate-file' );
		var isMultiple = false;
		var $attachment = $( this ).parents( '.attachment' );

		if ( $parent.hasClass( 'multiple' ) ) {
			isMultiple = true;
		}

		if ( ! isMultiple ) {
			$attachment.siblings( '.placeholder' ).find( '.attachment-add' ).removeClass( 'hidden' );
		}

		$attachment.remove();

		if ( ! $parent.find( '.preview-holder' ).html().length ) {
			$parent.find( '.attachments-clear' ).addClass( 'hidden' );
		}

		if ( ! $parent.find( '.preview-holder' ).html().length || ! isMultiple ) {
			var fieldname = $parent.data( 'key' ) + '[' + $parent.attr( 'id' ) + ']';
			var field = '<input type="hidden" class="hidden placeholder" name="' + fieldname + '" value="">';
			$parent.append( field );
		}
	});

	$( '.themeplate-file.multiple' ).sortable( {
		items: '.attachment',
		opacity: 0.75
	});


	$( document ).on( 'ready', function() {
		$( '.themeplate-select2' ).each( function() {
			var $select = $( this );

			$select.select2( {
				allowClear: true,
				placeholder: '',
				dropdownCssClass: 'themeplate-select2',
				containerCssClass: 'themeplate-select2'
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
