/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {

    // Site title
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
            console.log( to );
		} );
	} );

	// Site description
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Add custom-background-image body class when background image is added.
	wp.customize( 'background_image', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).toggleClass( 'custom-background-image', '' !== to );
		} );
	} );

	// Header text color (aka Site Title Color)
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title,  .site-description' ).css( {
					'clip': 'auto',
					'position': 'static'
				} );

				$( '.site-title a' ).css( {
					'color': to
				} );
			}
		} );
	} );

} )( jQuery );
