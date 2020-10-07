<?php
/**
 * Email Verification for WooCommerce - Advanced Section Settings
 *
 * @version 1.9.6
 * @since   1.6.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Advanced' ) ) :

class Alg_WC_Email_Verification_Settings_Advanced extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.9.6
	 * @since   1.6.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'emails-verification-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.9.6
	 * @since   1.6.0
	 * @todo    (maybe) remove `alg_wc_ev_prevent_login_after_checkout_notice` (i.e. make it always enabled)
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Advanced Options', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_advanced_options',
			),
			array(
				'title'    => __( 'Mail function', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Function used for sending the plugin\'s emails.', 'emails-verification-for-woocommerce' ) . ' ' .
					__( 'Leave the default value if unsure.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_mail_function',
				'default'  => 'wc_mail',
				'options'  => array(
					'mail'    => sprintf( __( '%s function', 'emails-verification-for-woocommerce' ), 'PHP "mail()"' ),
					'wc_mail' => sprintf( __( '%s function', 'emails-verification-for-woocommerce' ), 'WooCommerce "wc_mail()"' ),
					'wp_mail' => sprintf( __( '%s function', 'emails-verification-for-woocommerce' ), 'WordPress "wp_mail()"' ),
				),
			),
			array(
				'title'    => __( 'Custom "logout" function', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Replaces standard %s function in plugin with custom one. Enable this if you are having issues with "Activate" notice not being displayed after user registration.', 'emails-verification-for-woocommerce' ) . '<br />' . __( 'If your cart is getting cleared after a new account is created, try to enable this option.', 'emails-verification-for-woocommerce' ),
					'<code>wp_logout()</code>' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_custom_logout_function',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Action for "Prevent automatic user login after checkout"', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Change this if you are having issues with "Prevent automatic user login after checkout" option, e.g. product is removed from the cart on checkout.', 'emails-verification-for-woocommerce' ) . ' ' .
					__( 'Leave the default value if unsure.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_action',
				'default'  => 'woocommerce_get_return_url',
				'options'  => array(
					'woocommerce_get_return_url'  => __( 'On "get return URL"', 'emails-verification-for-woocommerce' ),
					'woocommerce_before_thankyou' => __( 'On "before \'thank you\' page"', 'emails-verification-for-woocommerce' ),
					'woocommerce_thankyou'        => __( 'On "\'thank you\' page"', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Notice for "Prevent automatic user login after checkout"', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Add notice', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Adds "Activate" notice to the WooCommerce "Thank you" (i.e. "Order received") page.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_notice',
				'default'  => 'yes',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_advanced_options',
			),
			array(
				'title'    => __( 'Delete options', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_admin_delete_options',
			),
			array(
				'title'    => __( 'Delete users', 'emails-verification-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Delete', 'emails-verification-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Deletes unverified users from the database.', 'emails-verification-for-woocommerce' ) . ' ' .
				              sprintf( __( 'Deleted users list will be affected by "%s", "%s" and "%s" options in "%s" settings section.', 'emails-verification-for-woocommerce' ),
					              __( 'Skip email verification for user roles', 'emails-verification-for-woocommerce' ),
					              __( 'Enable email verification for already registered users', 'emails-verification-for-woocommerce' ),
					              __( 'Expire activation link', 'emails-verification-for-woocommerce' ),
					              __( 'General', 'emails-verification-for-woocommerce' ) ) . ' ' .
				              __( 'The tool will never delete the current user.', 'emails-verification-for-woocommerce' ) . ' ' .
				              __( 'Check the box and save changes to run the tool.', 'emails-verification-for-woocommerce' ) . ' ' .
				              '<span style="font-weight: bold; color: red;">' . __( 'There is no undo for this action!', 'emails-verification-for-woocommerce' ) . '</span>',
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delete_users',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Delete users automatically', 'emails-verification-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'emails-verification-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Deletes unverified users from the database automatically once per week.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delete_users_cron',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_admin_delete_options',
			),
		);
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Advanced();
