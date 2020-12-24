/* global jQuery, come_back_params */

'use strict';

jQuery( document ).ready( function ( $ ) {
	$('body').on('click', '.come-back-test-email', function(e) {
		e.preventDefault();
		console.log('hi');
	 	var value = $(this).val();

		$(this).val( come_back_params.sending );

		var email = $(this).prevAll('input').val();

		var data = {
			action: 'come_back_send_test_email',
			email: email,
			security: come_back_params.cb_nonce
		};

		$.post( come_back_params.ajax_url, data, function( response ) {
			var parent_td = $(this).parent('td');

			console.log( parent_td );

		}).fail( function( xhr ) {
			window.console.log( xhr.responseText );
		});

	});
} );
