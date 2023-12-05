<?php
/**
 * Email Verification for WooCommerce - General Section Settings
 *
 * @version 2.6.0
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_General' ) ) :

class Alg_WC_Email_Verification_Settings_General extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.1.1
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'emails-verification-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.6.0
	 * @since   1.0.0
	 * @todo    [next] Logout unverified users on every page: better description
	 * @todo    [next] (maybe) `alg_wc_ev_delay_wc_email`: default to `yes`?
	 * @todo    `alg_wc_ev_expiration_time`: remove v1.7.0 note
	 * @todo    (maybe) more subsections?
	 * @todo    (maybe) `show_if_checked`
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'General Options', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_general_options',
			),
			array(
				'title'    => __( 'Login on activation', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Login the user automatically after the account is verified', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_login_automatically_on_activation',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Block unverified login', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Block login from unverified users', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'If disabled, account verification will be optional. In that case you\'ll probably want to:', 'emails-verification-for-woocommerce' ) . '<br />' . alg_wc_ev_array_to_string( array(
						sprintf( __( 'Change your %s option', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Message > Activation', 'emails-verification-for-woocommerce' ) . '</strong>' ),
						sprintf( __( 'Disable login options on the %s section', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Advanced', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Emails > Activation email > Email template', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Plain', 'emails-verification-for-woocommerce' ) . '</strong>' ),
					), array( 'glue' => '<br />', 'item_template' => '&#8226; {value}' ) ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_block_unverified_login',
				'default'  => 'yes',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_general_options',
			),
			array(
				'title'    => __( 'Account verification', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_account_verification',
			),
			array(
				'title'    => __( 'Ignore user roles', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'The following user roles won\'t need to be berified', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Skipped user roles', 'emails-verification-for-woocommerce' ),
				'type'     => 'multiselect',
				'options'  => $this->get_user_roles_options(),
				'id'       => 'alg_wc_ev_skip_user_roles',
				'default'  => array( 'administrator' ),
				'class'    => 'chosen_select',
			),
			array(
				'title'    => __( 'Guest users', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Verify guest users', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'If enabled, checkout as guest user billing address will be verified.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_verify_guest_email',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Current registered users', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Verify account for current users', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'If enabled, all your current unverified users will have to verify their accounts.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_verify_already_registered',
				'default'  => 'no',
			),
			array(
				'title'             => __( 'Password reset', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Verify the user on password reset', 'emails-verification-for-woocommerce' ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_ev_verify_account_on_password_reset',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				'default'           => 'no',
			),
			array(
				'title'             => __( 'Paying customers', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Verify customers as soon as any of their non-free orders are considered paid', 'emails-verification-for-woocommerce' ),
				'desc_tip'          => $this->get_paid_statuses_msg(),
				//'desc_tip'          => __( 'The activation email won\'t be sent if the order cost is not free.', 'emails-verification-for-woocommerce' ),
				'type'              => 'checkbox',
				'default'           => 'no',
				'checkboxgroup'     => 'start',
				'id'                => 'alg_wc_ev_auto_verify_paying_user',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'             => __( 'Email changing', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Unverify, logout and resend activation link to accounts that changed the emails', 'emails-verification-for-woocommerce' ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_ev_unverify_email_changing',
				'default'           => 'no',
			),
			array(
				'desc'              => 'Message displayed after the email has been changed.',
				'custom_attributes' => '',
				'type'              => 'text',
				'id'                => 'alg_wc_ev_unverify_email_changing_msg',
				'default'           => __( 'Your email has been changed. In order to verify your account please check the activation email that was sent to your new email.', 'emails-verification-for-woocommerce' ),
			),
			array(
				'title'             => __( 'Verification parameter', 'emails-verification-for-woocommerce' ),
				'desc'              => sprintf( __( 'Ex: <code>%s</code>', 'emails-verification-for-woocommerce' ), alg_wc_ev()->core->emails->get_verification_url( array( 'user_id' => get_current_user_id(), 'code' => 'a' ) ) ),
				'desc_tip'          => __( 'The parameter used on the URL to verify the user account.', 'emails-verification-for-woocommerce' ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				'type'              => 'text',
				'id'                => 'alg_wc_ev_verification_parameter',
				'default'           => 'alg_wc_ev_verify_email',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_account_verification',
			),
			array(
				'title'    => __( 'Verification info', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Prepare a verification info to your customers like the verification status and a link to resend the verification email.', 'emails-verification-for-woocommerce' ).'<br />'.
				$this->get_block_unverify_login_option_warning(),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_verification_info',
			),
			array(
				'title'    => __( 'My Account page', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Show account verification info on <a href="%s">%s</a> page', 'emails-verification-for-woocommerce' ), wc_get_page_permalink( 'myaccount' ), __( 'My Account', 'emails-verification-for-woocommerce' ) ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_verification_info_my_account',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Widget', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Add account verification info widget', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_verification_info_widget',
				'default'  => 'no',
			),
			array(
				'title'             => __( 'Customization', 'emails-verification-for-woocommerce' ),
				'desc'              => sprintf( __( 'Documentation on <a target="_blank" href="%s">plugin\'s FAQ</a>.', 'emails-verification-for-woocommerce' ), 'https://wordpress.org/plugins/emails-verification-for-woocommerce/' ),
				'desc_tip'          => sprintf( __( 'You can use %s and %s shortcodes here and any other one you\'d like.', 'emails-verification-for-woocommerce' ), '<strong>' . '[alg_wc_ev_verification_status]' . '</strong>', '<strong>' . '[alg_wc_ev_resend_verification_url]' . '</strong>' ),
				'type'              => 'textarea',
				'css'               => 'width:100%;height:83px;',
				'id'                => 'alg_wc_ev_verification_info_customization',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				'alg_wc_ev_raw'     => true,
				'default'           => alg_wc_ev()->core->get_verification_info_default(),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_verification_info',
			),
			array(
				'title'    => __( 'Redirect on success', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'Redirects customers after successful verification.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_redirect_on_success_options',
			),
			array(
				'title'    => __( 'Redirect on success', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Redirects customers to the selected page after successful verification.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_redirect_to_my_account_on_success', // mislabeled, should be `alg_wc_ev_redirect_on_success`
				'default'  => 'yes',
				'options'  => array(
					'no'      => __( 'Do not redirect', 'emails-verification-for-woocommerce' ),
					'yes'     => __( 'Redirect to "My account" page', 'emails-verification-for-woocommerce' ),
					'shop'    => __( 'Redirect to "Shop" page', 'emails-verification-for-woocommerce' ),
					'home'    => __( 'Redirect to home page', 'emails-verification-for-woocommerce' ),
					'custom'  => __( 'Redirect to custom URL', 'emails-verification-for-woocommerce' ),
					'my_account_referer' => __( 'Redirect to the previous url before "my account" page', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Custom redirect URL', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( '"%s" must be selected for the "%s" option above.', 'emails-verification-for-woocommerce' ),
					__( 'Redirect to custom URL', 'emails-verification-for-woocommerce' ), __( 'Redirect on success', 'emails-verification-for-woocommerce' ) ),
				'type'     => 'text',
				'id'       => 'alg_wc_ev_redirect_on_success_url',
				'default'  => '',
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_redirect_on_success_options',
			),
			array(
				'title' => __( 'Redirect on failure', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Redirects customers after unsuccessful verification.', 'emails-verification-for-woocommerce' ) . '<br />' .
				           sprintf( __( 'This will also append a %s argument to the URL that could help you displaying the error message in case you have issues with that.', 'emails-verification-for-woocommerce' ), '<code>' . '?alg_wc_ev_email_verified_error' . '</code>' ),
				'id'    => 'alg_wc_ev_redirect_on_failure_options',
			),
			array(
				'title'    => __( 'Redirect on failure', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Redirects to a custom URL if an unverified user tries to login', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_redirect_on_failure',
				'default'  => 'no'
			),
			array(
				'title'    => __( 'Custom redirect URL', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'If empty will redirect to the same page', 'emails-verification-for-woocommerce' ),
				'type'     => 'text',
				'id'       => 'alg_wc_ev_redirect_on_failure_url',
				'default'  => '',
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_redirect_on_failure_options',
			),
			array(
				'title'    => __( 'Activation link', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'A link sent via email where users can verify their accounts.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_activation_link_options',
			),
			array(
				'title'    => __( 'One-time activation link', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Prevent activation link from working after the first use', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_one_time_activation_link',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Expire time', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Ignored if set to zero.', 'emails-verification-for-woocommerce' ) . ' ' .
				              __( 'Please note that all activation codes generated before installing the plugin v1.7.0 will be automatically expired.', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Expiration time based on the %s option below.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Expire time unit', 'emails-verification-for-woocommerce' ) . '</strong>' ),
				'type'     => 'number',
				'id'       => 'alg_wc_ev_expiration_time',
				'default'  => 0,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ), 'min', array( 0 ) ),
			),
			array(
				'title'   => __( 'Expire time unit', 'emails-verification-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'seconds' => __( 'Seconds', 'emails-verification-for-woocommerce' ),
					'days'    => __( 'Days', 'emails-verification-for-woocommerce' ),
				),
				'id'      => 'alg_wc_ev_expiration_time_unit',
				'default' => 'seconds',
			),
			array(
				'title'    => __( 'Expire notice', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( array( '%resend_verification_url%' ) ),
				'desc_tip' => __( 'Notice will appear when user will try to verify his email by clicking the email activation link.', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_activation_code_expired_message',
				'default'  => __( 'Link has expired. You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_activation_link_options',
			),
		);
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_General();
