/* global tinyMCEPreInit, tinymce, quicktags */

(function( $ ) {

	'use strict';

	$( document ).on( 'clone', '.themeplate-clone', function() {
		var $field = $( this ).find( '.themeplate-wysiwyg' );

		if ( $field.length === 0 ) {
			return;
		}

		var fieldID = $field.attr( 'id' );

		if ( tinyMCEPreInit.mceInit[ fieldID ] ) {
			return;
		}

		var $wrapper = $field.closest( '.wp-editor-wrap' );
		var clonerID = $field.closest( '.themeplate-clone' ).siblings( '.hidden' ).find( '.themeplate-wysiwyg' ).attr( 'id' );

		$wrapper.removeClass( 'html-active' ).addClass( 'tmce-active' ).find( '.mce-container' ).remove();
		$wrapper.find( 'button' ).data( 'editor', fieldID ).attr( 'data-editor', fieldID );
		$wrapper.find( '.switch-tmce' ).data( 'wp-editor-id', fieldID ).attr( 'data-wp-editor-id', fieldID );
		$wrapper.find( '.switch-html' ).data( 'wp-editor-id', fieldID ).attr( 'data-wp-editor-id', fieldID );

		var mceSettings = tinyMCEPreInit.mceInit[ clonerID ];
		var qtSettings = tinyMCEPreInit.qtInit[ clonerID ];

		tinyMCEPreInit.mceInit[ fieldID ] = mceSettings;
		qtSettings.id = fieldID;

		var editor = new tinymce.Editor( fieldID, mceSettings, tinymce.EditorManager );

		$field.show();
		editor.render();
		quicktags( qtSettings );
	});

	$( window ).on( 'load', function() {
		if ( ! ( window.wp && wp.data && wp.data.select && wp.data.select( 'core/editor' ) ) ) {
			return;
		}

		$( '.themeplate-wysiwyg' ).each( function() {
			var fieldID = $( this ).attr( 'id' );
			var editor = tinymce.get( fieldID );

			if ( ! editor ) {
				return;
			}

			setTimeout( function() {
				editor.destroy();
				tinymce.init( tinyMCEPreInit.mceInit[ fieldID ] );
			}, 100 );
		});
	});

	$( '.meta-box-sortables' ).on( 'sortstop', function( event, ui ) {
		var $field = $( ui.item ).find( '.themeplate-wysiwyg' );

		if ( $field.length === 0 ) {
			return;
		}

		var isHtml = $field.closest( '.wp-editor-wrap' ).hasClass( 'html-active' );
		var fieldID = $field.attr( 'id' );

		if ( isHtml ) {
			$( '#' + fieldID + '-tmce' ).click();
		}

		tinymce.execCommand( 'mceRemoveEditor', true, fieldID );
		tinymce.execCommand( 'mceAddEditor', true, fieldID );

		if ( isHtml ) {
			$( '#' + fieldID + '-html' ).click();
		}
	});

}( jQuery ));
