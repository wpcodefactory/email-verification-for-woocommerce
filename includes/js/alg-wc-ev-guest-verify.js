/**
 * alg-wc-ev-guest-verify.js
 *
 * @version 2.9.7
 * @since   2.7.4
 */

jQuery( function ( $ ) {

	var billing_email_input_js = $( 'input[name="billing_email"]' );

	var current_billing_email = billing_email_input_js.val();
	var billing_email_paragraph = $( 'p[id="billing_email_field"]' );
	billing_email_paragraph.append( '<div id="alg_wc_ev_activation_guest_verify"></div>' );

	var guest_email_verify_text = $( 'div[id="alg_wc_ev_activation_guest_verify"]' );

	send_alg_wc_ev_guest_verification_email( "new", current_billing_email );

	billing_email_input_js.on( 'input change paste keyup blur', function () {

		guest_email_verify_text.html( '' );
		var regexo = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var isValido = regexo.test( $( this ).val() );
		if ( isValido ) {
			guest_email_verify_text.html( email_verification_options.send );
		}

	} );

	function send_alg_wc_ev_guest_verification_email( send, email ) {

		if ( send === 'resend' ) {
			guest_email_verify_text.html( email_verification_options.resend );
		}
		else if ( send === 'new_send' ) {
			send = 'new';
			guest_email_verify_text.html( email_verification_options.sending );
		} else {
			guest_email_verify_text.html( '' );
		}

		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var isValid = regex.test( email );

		if ( isValid ) {

			var alg_wc_ev_data = {
				'action': 'alg_wc_ev_send_guest_verification_email_action',
				'alg_wc_ev_email': email,
				'send': send,
				'security_nonce': email_verification_options.security_nonce
			};

			$.ajax( {
				type: "POST",
				url: woocommerce_params.ajax_url,
				data: alg_wc_ev_data,
				success: function ( response ) {
					if ( 'invalid_nonce' === response ) {
						guest_email_verify_text.html( email_verification_options.error_nonce_message );
						guest_email_verify_text.removeClass();
						guest_email_verify_text.addClass( 'alg-wc-ev-guest-verify-error-color' );
					} else if ( 'sent' === response ) {
						guest_email_verify_text.html( email_verification_options.sent );
						guest_email_verify_text.removeClass();
						guest_email_verify_text.addClass( 'alg-wc-ev-guest-verify-button' );
					} else if ( 'already_verified' === response ) {
						guest_email_verify_text.html( email_verification_options.already_verified );
						guest_email_verify_text.removeClass();
						guest_email_verify_text.addClass( 'alg-wc-ev-guest-verify-button' );
					} else if ( 'email_exists' ) {
						guest_email_verify_text.html( email_verification_options.email_exists );
						guest_email_verify_text.removeClass();
						guest_email_verify_text.addClass( 'alg-wc-ev-guest-verify-error-color' );
					}
				}
			} );

		}
	}

	$( "body" ).on( "click", "#alg_wc_ev_resend_verify", function () {
		send_alg_wc_ev_guest_verification_email( "resend", $( 'input[name="billing_email"]' ).val() );
	} );

	$( "body" ).on( "click", "#alg_wc_ev_send_verify", function () {
		send_alg_wc_ev_guest_verification_email( "new_send", $( 'input[name="billing_email"]' ).val() );
	} );
} );