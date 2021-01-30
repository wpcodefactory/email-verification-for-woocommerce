<?php
/**
 * Email Verification for WooCommerce - Messages Class
 *
 * @version 1.6.0
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
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function get_error_message( $user_id ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_error_message',
			__( 'Your account has to be activated before you can login. You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ) ) );
		return str_replace( '%resend_verification_url%', $this->get_resend_verification_url( $user_id ), $notice );
	}

	/**
	 * get_resend_message.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function get_resend_message() {
		return do_shortcode( get_option( 'alg_wc_ev_email_resend_message',
			__( '<strong>Success:</strong> Your activation email has been resent. Please check your email.', 'emails-verification-for-woocommerce' ) ) );
	}

	/**
	 * get_failed_message.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function get_failed_message( $user_id ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_failed_message',
			__( '<strong>Error:</strong> Activation failed, please contact our administrator. You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ) ) );
		return str_replace( '%resend_verification_url%', $this->get_resend_verification_url( $user_id ), $notice );
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
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function get_activation_message( $user_id ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_activation_message',
			__( 'Thank you for your registration. Your account has to be activated before you can login. Please check your email.', 'emails-verification-for-woocommerce' ) ) );
		return str_replace( '%resend_verification_url%', $this->get_resend_verification_url( $user_id ), $notice );
	}

	/**
	 * get_resend_verification_url.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 * @todo    (maybe) `wc_get_page_permalink( 'myaccount' )` instead of current URL
	 */
	function get_resend_verification_url( $user_id ) {
		return add_query_arg( 'alg_wc_ev_user_id', $user_id );
	}

	/**
	 * get_invalid_url_message.
	 *
	 * @version 2.0.5
	 * @since   2.0.5
	 */
	function get_invalid_url_message() {
		return do_shortcode( get_option( 'get_invalid_url_message',
			__( '<strong>Error:</strong> Invalid activation url, please contact our administrator.', 'emails-verification-for-woocommerce' ) ) );
	}

	/**
	 * get_profile_already_activated_message.
	 *
	 * @version 2.0.5
	 * @since   2.0.5
	 */
	function get_profile_already_activated_message() {
		return do_shortcode( get_option( 'get_profile_already_activated_message',
			__( '<strong>Error:</strong> Your profile already activated, please contact our administrator if you need further assistance.', 'emails-verification-for-woocommerce' ) ) );
	}

}

endif;

return new Alg_WC_Email_Verification_Messages();
