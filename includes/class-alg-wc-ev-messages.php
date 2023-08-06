<?php
/**
 * Email Verification for WooCommerce - Messages Class.
 *
 * @version 2.4.3
 * @since   1.6.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Messages' ) ) :

class Alg_WC_Email_Verification_Messages {

	/**
	 * Constructor.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_error_message.
	 *
	 * @version 2.0.7
	 * @since   1.6.0
	 */
	function get_error_message( $user_id ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_error_message',
			__( 'Your account has to be activated before you can login. You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ) ) );
		return str_replace( '%resend_verification_url%', esc_url( $this->get_resend_verification_url( $user_id ) ), $notice );
	}

	/**
	 * get_resend_message.
	 *
	 * @version 2.3.7
	 * @since   1.6.0
	 */
	function get_resend_message( $code = '1' ) {

		$success_message = do_shortcode( get_option( 'alg_wc_ev_email_resend_message',
			__( '<strong>Success:</strong> Your activation email has been resent. Please check your email.', 'emails-verification-for-woocommerce' ) ) );
		$resend_messages = apply_filters( 'alg_wc_ev_resend_status_codes',
			array(
				'1' => array(
					'type'  => 'success',
					'msg'   => $success_message,
				),
				'2' => array(
					'type'  => 'error',
					'msg'   => __( '<strong>Error:</strong> No registration found under the email.', 'emails-verification-for-woocommerce' )
				),
				'3' => array(
					'type'  => 'error',
					'msg'   => __( '<strong>Error:</strong> This user is already verified.', 'emails-verification-for-woocommerce' )
				),
			)
		);

		return isset( $resend_messages[ $code ] ) ? $resend_messages[ $code ] : '';
	}

	/**
	 * get_failed_message.
	 *
	 * @version 2.0.7
	 * @since   1.6.0
	 */
	function get_failed_message( $user_id ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_failed_message',
			__( '<strong>Error:</strong> Activation failed, please contact our administrator. You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ) ) );
		return str_replace( '%resend_verification_url%', esc_url( $this->get_resend_verification_url( $user_id ) ), $notice );
	}

	/**
	 * get_success_message.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function get_success_message() {
		return do_shortcode( get_option( 'alg_wc_ev_success_message',
			__( '<strong>Success:</strong> Your account has been activated!', 'emails-verification-for-woocommerce' ) ) );
	}

	/**
	 * get_activation_message.
	 *
	 * @version 2.0.7
	 * @since   1.5.0
	 */
	function get_activation_message( $user_id ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_activation_message',
			__( 'Thank you for your registration. Your account has to be activated before you can login. Please check your email.', 'emails-verification-for-woocommerce' ) ) );
		return str_replace( '%resend_verification_url%', esc_url( $this->get_resend_verification_url( $user_id ) ), $notice );
	}

	/**
	 * get_activation_message.
	 *
	 * @version 2.4.3
	 * @since   2.4.3
	 */
	function get_already_verified_message( $user_id ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_already_verified_message',
			__( 'Your account is already verified.', 'emails-verification-for-woocommerce' ) ) );
		return $notice;
	}

	/**
	 * get_resend_verification_url.
	 *
	 * @version 2.4.2
	 * @since   1.4.0
	 * @todo    (maybe) `wc_get_page_permalink( 'myaccount' )` instead of current URL
	 *
	 * @param $user_id
	 * @param $url_params
	 *
	 * @return string
	 */
	function get_resend_verification_url( $user_id, $url_params = array() ) {
		$resend_timestamp = get_user_meta( $user_id, 'alg_wc_ev_activation_email_sent', true );
		$url_params       = wp_parse_args( (array) $url_params, array( 'alg_wc_ev_user_id' => $user_id ) );
		if ( ! empty( $resend_timestamp ) ) {
			$url_params['alg_wc_ev_nonce'] = wp_create_nonce( "resend-{$user_id}-{$resend_timestamp}" );
		} elseif (
			empty( $resend_timestamp ) &&
			'yes' === get_option( 'alg_wc_ev_verify_already_registered', 'no' )
		) {
			$url_params['alg_wc_ev_nonce'] = wp_create_nonce( "resend-{$user_id}-old-user" );
		}
		return add_query_arg( $url_params, get_option( 'alg_wc_ev_resend_verification_url', '' ) );
	}

	/**
	 * get_guest_verified_message.
	 *
	 * @version 2.5.8
	 * @since   2.5.8
	 */
	function get_guest_verified_message( $user_id = 0 ) {
		$notice = do_shortcode( __( 'Your email is verified.', 'emails-verification-for-woocommerce' ) );
		return $notice;
	}
	
	
	
	/**
	 * get_activation_message.
	 *
	 * @version 2.5.8
	 * @since   2.5.8
	 */
	function get_guest_unverified_message( $user_id = 0 ) {
		$notice = do_shortcode( __( 'Your email is not verified. Please click on verify link sent on your email.', 'emails-verification-for-woocommerce' ) );
		return $notice;
	}

}

endif;

return new Alg_WC_Email_Verification_Messages();
