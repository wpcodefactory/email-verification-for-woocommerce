<?php
/**
 * Email Verification for WooCommerce - Messages Section Settings.
 *
 * @version 2.4.3
 * @since   1.3.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Messages' ) ) :

class Alg_WC_Email_Verification_Settings_Messages extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function __construct() {
		$this->id   = 'messages';
		$this->desc = __( 'Messages', 'emails-verification-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.4.3
	 * @since   1.3.0
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Messages options', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_messages_general_options',
			),
			array(
				'title'    => __( 'Clear previous messages', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Clear previous messages before displaying new ones', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Enable if you have issues with duplicated messages.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_clear_previous_messages',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_messages_general_options',
			),

			array(
				'title'    => __( 'Messages', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_messages_options',
			),
			array(
				'title'    => __( 'Success', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_success_message',
				'default'  => __( '<strong>Success:</strong> Your account has been activated!', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Error', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( array( '%resend_verification_url%' ) ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_error_message',
				'default'  => __( 'Your account has to be activated before you can login. You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Failed', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( array( '%resend_verification_url%' ) ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_failed_message',
				'default'  => __( '<strong>Error:</strong> Activation failed, please contact our administrator. You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Already verified', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_already_verified_message',
				'default'  => __( 'Your account is already verified.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Activate', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( array( '%resend_verification_url%' ) ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_activation_message',
				'default'  => __( 'Thank you for your registration. Your account has to be activated before you can login. Please check your email.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Resend', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_email_resend_message',
				'default'  => __( '<strong>Success:</strong> Your activation email has been resent. Please check your email.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_messages_options',
			),
			array(
				'title'    => __( 'Resend verification URL', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_resend_verification_url_options',
			),
			array(
				'title'    => __( 'URL', 'emails-verification-for-woocommerce' ),
				'type'     => 'text',
				'desc_tip' => __( 'If empty the URL will be the current page the user is at the moment the link was clicked.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_resend_verification_url',
				'default'  => '',
				'css'      => 'width:100%;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_resend_verification_url_options',
			),
		);
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Messages();
