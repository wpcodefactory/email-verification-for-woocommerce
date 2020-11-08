<?php
/**
 * Email Verification for WooCommerce - Emails Section Settings
 *
 * @version 1.9.7
 * @since   1.3.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Emails' ) ) :

class Alg_WC_Email_Verification_Settings_Emails extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function __construct() {
		$this->id   = 'emails';
		$this->desc = __( 'Emails', 'emails-verification-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_default_email_placeholders.
	 *
	 * @version 1.9.7
	 * @since   1.9.7
	 *
	 * @return array
	 */
	function get_default_email_placeholders(){
		return array(
			'%user_id%',
			'%user_login%',
			'%user_nicename%',
			'%user_email%',
			'%user_url%',
			'%user_registered%',
			'%user_display_name%',
			'%user_roles%',
			'%user_first_name%',
			'%user_last_name%',
			'%admin_user_profile_url%',
		);
	}

	/**
	 * get_settings.
	 *
	 * @version 1.9.5
	 * @since   1.3.0
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Activation email', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_activation_email',
				'desc'     => __( 'An email sent to the user with an activation link.', 'emails-verification-for-woocommerce' ).'<br />'.$this->pro_msg( '<strong>', 'You will need %s plugin to change email settings.', '</strong>' ),
			),
			array(
				'title'    => __( 'Email subject', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_email_subject',
				'default'  => __( 'Please activate your account', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'    => __( 'Email content', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Placeholders: %s', 'emails-verification-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
						'%verification_url%',
						'%user_id%',
						'%user_first_name%',
						'%user_last_name%',
						'%user_login%',
						'%user_nicename%',
						'%user_email%',
						'%user_display_name%',
					) ) . '</code>' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_email_content',
				'default'  => __( 'Please click the following link to verify your email:<br><br><a href="%verification_url%">%verification_url%</a>', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;height:150px;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'    => __( 'Email template', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Possible values: Plain, WooCommerce.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_email_template',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'default'  => 'plain',
				'options'  => array(
					'plain' => __( 'Plain', 'emails-verification-for-woocommerce' ),
					'wc'    => __( 'WooCommerce', 'emails-verification-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'If "WooCommerce" is selected as "Email template", set email heading here.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_email_template_wc_heading',
				'type'     => 'textarea',
				'default'  => __( 'Activate your account', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_activation_email',
			),
			array(
				'title'    => __( 'Admin email', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'An email that can be sent to the admin when a new user verifies his email.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_email_options',
			),
			array(
				'title'    => __( 'Admin email', 'emails-verification-for-woocommerce' ),
				$this->pro_msg(),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_admin_email',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Recipient', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Leave empty to send to %s.', 'emails-verification-for-woocommerce' ), get_bloginfo( 'admin_email' ) ),
				'type'     => 'text',
				'id'       => 'alg_wc_ev_admin_email_recipient',
				'default'  => '',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Subject', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( $this->get_default_email_placeholders() ),
				'type'     => 'text',
				'id'       => 'alg_wc_ev_admin_email_subject',
				'default'  => __( 'User email has been verified', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Heading', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( $this->get_default_email_placeholders() ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_admin_email_heading',
				'default'  => __( 'User account has been activated', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Content', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( $this->get_default_email_placeholders() ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_admin_email_content',
				'default'  => sprintf( __( 'User %s has just verified his email (%s).', 'emails-verification-for-woocommerce' ),
					'<a href="%admin_user_profile_url%">%user_login%</a>', '%user_email%' ),
				'css'      => 'width:100%;height:100px;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_email_options',
			),
		);
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Emails();
