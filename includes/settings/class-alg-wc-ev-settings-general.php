<?php
/**
 * Email Verification for WooCommerce - General Section Settings
 *
 * @version 2.0.4
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
	 * get_user_roles_options.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function get_user_roles_options() {
		global $wp_roles;
		$roles = apply_filters( 'editable_roles', ( isset( $wp_roles ) && is_object( $wp_roles ) ? $wp_roles->roles : array() ) );
		return wp_list_pluck( $roles, 'name' );
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.4
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
				'title'    => __( 'Login automatically', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Login the user automatically after the account is verified', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_login_automatically_on_activation',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Send as a separate email', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Send verification as a separate email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Disable if you want to append it to the standard WooCommerce "Customer new account" email.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_send_as_separate_email',
				'checkboxgroup' => 'start',
				'default'  => 'yes',
			),
			array(
				'desc'          => __( 'Delay WooCommerce "Customer new account" email', 'emails-verification-for-woocommerce' ),
				'desc_tip'      => __( '"Customer new account" email is only sent on successful verification.', 'emails-verification-for-woocommerce' ) . ' ' .
				                   $this->separate_email_option_msg(),
				'type'          => 'checkbox',
				'id'            => 'alg_wc_ev_delay_wc_email',
				'checkboxgroup' => 'end',
				'default'       => 'no',
			),
			array(
				'title'    => __( 'Skip email verification for user roles', 'emails-verification-for-woocommerce' ),
				'type'     => 'multiselect',
				'options'  => $this->get_user_roles_options(),
				'id'       => 'alg_wc_ev_skip_user_roles',
				'default'  => array( 'administrator' ),
				'class'    => 'chosen_select',
			),
			array(
				'title'    => __( 'Enable email verification for already registered users', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'If enabled, all your current users will have to verify their emails when logging to your site.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_verify_already_registered',
				'default'  => 'no',
			),
			array(
				'title'             => __( 'Unverify email changing accounts', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Unverify, logout and resend activation link to accounts that changed the emails', 'emails-verification-for-woocommerce' ),
				'desc_tip'          => $this->pro_msg( '' ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ), 'min', array( 0 ) ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_ev_unverify_email_changing',
				'default'           => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_general_options',
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
					'no'     => __( 'Do not redirect', 'emails-verification-for-woocommerce' ),
					'yes'    => __( 'Redirect to "My account" page', 'emails-verification-for-woocommerce' ),
					'shop'   => __( 'Redirect to "Shop" page', 'emails-verification-for-woocommerce' ),
					'home'   => __( 'Redirect to home page', 'emails-verification-for-woocommerce' ),
					'custom' => __( 'Redirect to custom URL', 'emails-verification-for-woocommerce' ),
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
				'title'    => __( 'Activation link', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'A link sent via email where users can verify their accounts.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_activation_link_options',
			),
			array(
				'title'    => __( 'One-time activation link', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'After using the activation link for the first time, it won\'t work anymore.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_one_time_activation_link',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Expire time', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Ignored if set to zero.', 'emails-verification-for-woocommerce' ) . ' ' .
				              __( 'Please note that all activation codes generated before installing the plugin v1.7.0 will be automatically expired.', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Expiration time based on the %s option below.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Expire time unit', 'emails-verification-for-woocommerce' ) . '</strong>' ) .
				              $this->pro_msg(),
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
				'title'    => __( 'Email sending trigger', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Leave the default value if unsure.', 'emails-verification-for-woocommerce' ) . ' ' .
				              $this->separate_email_option_msg(),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_new_user_action',
				'default'  => 'user_register',
				'options'  => array(
					'user_register'                => __( 'On "user register"', 'emails-verification-for-woocommerce' ),
					'woocommerce_created_customer' => __( 'On "WooCommerce created customer"', 'emails-verification-for-woocommerce' ),
					//'on_order_payment'             => __( 'On order payment', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Activation email delay', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Delay the activation email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Try to enable it if the activation emails are getting sent to authenticated users.', 'emails-verification-for-woocommerce' ) . '<br />' .
				              $this->separate_email_option_msg(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delay_activation_email',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_activation_link_options',
			),
			array(
				'title'    => __( 'Logout unverified users', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Logouts unverified users in some specific situations.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_logout_options',
			),
			array(
				'title'    => __( 'Logout unverified users on "My Account" page', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Will check if logged user is verified on "My Account" page.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_myaccount',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Logout unverified users on every page', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Will check if logged user is verified on every page of your site.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_always',
				'default'  => 'no',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Redirect', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Redirect to the activate account notice after logout.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_always_redirect',
				'default'  => 'yes',
				'checkboxgroup' => 'end',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_logout_options',
			),
			array(
				'title'    => __( 'Block checkout', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Blocks checkout process for unverified users.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_block_checkout_options',
			),
			array(
				'title'    => __( 'Block checkout', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Blocks checkout process for unverified users (including guests).', 'emails-verification-for-woocommerce' ) .
				              $this->pro_msg(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_block_checkout_process',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Error notice', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_block_checkout_process_notice',
				'default'  => __( 'You need to log in and verify your email to place an order.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_block_checkout_options',
			),
			array(
				'title'    => __( 'Block adding products to cart', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Blocks guests from adding products to the cart.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_block_guests_add_to_cart_options',
			),
			array(
				'title'    => __( 'Block adding products to cart', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Blocks guests from adding any products to the cart.', 'emails-verification-for-woocommerce' ) .
					$this->pro_msg(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_block_guest_add_to_cart',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'         => __( 'Custom redirect URL', 'emails-verification-for-woocommerce' ),
				'desc_tip'      => __( 'Redirects to a custom URL after the customer is blocked.', 'emails-verification-for-woocommerce' ) . '<br />' .
				                   __( 'Leave it empty if you don\'t want to redirect.', 'emails-verification-for-woocommerce' ),
				'type'          => 'text',
				'id'            => 'alg_wc_ev_block_guest_add_to_cart_custom_redirect_url',
				'default'       => '',
				'css'           => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Error notice', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( array( '%myaccount_url%' ) ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_block_guest_add_to_cart_notice',
				'default'  => __( 'You need to <a href="%myaccount_url%" target="_blank">register</a> and verify your email before adding products to the cart.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_block_guests_add_to_cart_options',
			),
			array(
				'title'    => __( 'Block non-paying users', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Prevents non-paying users from activating their accounts until they become paying customers, considering the %s option.', 'emails-verification-for-woocommerce' ) . '<br />' . '<strong>' . __( 'Note:', 'emails-verification-for-woocommerce' ) . '</strong>' . ' ' . __( 'Probably this option will make more sense if users register only on checkout, or else they won\'t be able to purchase to activate their accounts using the same email.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Role checking', 'emails-verification-for-woocommerce' ) . '</strong>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_block_nonpaying_users_activation_options',
			),
			array(
				'title'         => __( 'Block non-paying users', 'emails-verification-for-woocommerce' ),
				'desc'          => __( 'Block activation link until the customer places an order and its status is considered as paid', 'emails-verification-for-woocommerce' ),
				'desc_tip'      => __( 'Won\'t block users already verified.', 'emails-verification-for-woocommerce' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => 'start',
				'id'            => 'alg_wc_ev_block_nonpaying_users_activation',
			),
			array(
				'title'             => __( 'Verify paying customers automatically', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Activate the account automatically when the order is paid', 'emails-verification-for-woocommerce' ),
				'desc_tip'          => __( 'The activation email won\'t be sent if the order cost is not free.', 'emails-verification-for-woocommerce' ),
				'type'              => 'checkbox',
				'default'           => 'no',
				'checkboxgroup'     => 'start',
				'id'                => 'alg_wc_ev_auto_verify_paying_user',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'             => __( 'Send activation email only on payment', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Send the activation email only when order status is considered as paid', 'emails-verification-for-woocommerce' ),
				'desc_tip'          => sprintf( __( 'Will only send the email if the %s option is disabled or if the corresponding order cost is free.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Verify paying customers automatically', 'emails-verification-for-woocommerce' ) . '</strong>' ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_ev_block_nonpaying_users_activation_email_on_payment',
				'default'           => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Role checking', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Blocks non-paying users with one of the following roles.', 'emails-verification-for-woocommerce' ).'<br />'.__( 'Probably you just want to mark the "Customer" role.', 'emails-verification-for-woocommerce' ).'<br />'.__( 'If empty, will work for any role.', 'emails-verification-for-woocommerce' ),
				'type'     => 'multiselect',
				'options'  => $this->get_user_roles_options(),
				'id'       => 'alg_wc_ev_block_nonpaying_users_activation_role',
				'default'  => array( 'customer' ),
				'class'    => 'chosen_select',
			),
			array(
				'title'    => __( 'Error notice', 'emails-verification-for-woocommerce' ),
				'desc'     => $this->available_placeholders_desc( array( '%resend_verification_url%' ) ),
				'desc_tip' => __( 'The notice will be displayed when user is blocked after trying to login or to verify its email.', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_block_nonpaying_users_activation_error_notice',
				'default'  => __( 'You need to become a paying customer in order to activate your account.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_block_nonpaying_users_activation_options',
			),
			array(
				'title'    => __( 'Block account verification by email', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Prevents account verification by email.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_block_nonpaying_users_activation_options',
			),
			array(
				'title'    => __( 'Email denylist', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Ignored if empty.', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Separate emails with a comma and/or with a new line. You can also use wildcard (%s) here, for example: %s', 'emails-verification-for-woocommerce' ),
						'<code>*</code>', '<code>*@example.com,email@example.net</code>' ) .
				              $this->pro_msg(),
				'type'     => 'textarea',
				'css'      => 'width:100%;height:100px;',
				'id'       => 'alg_wc_ev_email_blacklist',
				'default'  => '',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'    => __( 'Error notice', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Notice will appear when user will try to verify his email by clicking the email activation link.', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_blacklisted_message',
				'default'  => __( 'Your email is blacklisted.', 'emails-verification-for-woocommerce' ),
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_block_activation_by_email_options',
			),
		);
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_General();
