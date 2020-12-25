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

			if ( response.status === 'OK') {
				var status = 'success';
			} else {
				var status = 'error';
			}

			var closest = $( '.come-back-send-test-email' ).closest( 'tr' );
			var clone = '<tr valign="top"><th></th><td><div class="cb-cloned-td notice notice-'+ status +'"><p><strong>'+ response.message +'</strong></p></div></td>>/tr>';

			closest.after( clone );

			$( '.come-back-send-test-email' ).val(value);

		}).fail( function( xhr ) {
			window.console.log( xhr.responseText );
		});

	});
} );
