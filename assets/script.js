/* global jQuery, come_back_params */

'use strict';

jQuery( document ).ready( function ( $ ) {
	$( '.come-back-send-test-email' ).on( 'click', function(e) {
		e.preventDefault();

	 	var value = $(this).val();

		$(this).val( come_back_params.sending );

		var email = $(this).prevAll('input').val();

		var data = {
			action: 'come_back_send_test_email',
			email: email,
			security: come_back_params.cb_nonce
		};

		$.post( come_back_params.ajax_url, data, function( response ) {
			var parent_td = $(this).parent();

			alert( response.message );

		}).fail( function( xhr ) {
			window.console.log( xhr.responseText );
		});

	});
} );
