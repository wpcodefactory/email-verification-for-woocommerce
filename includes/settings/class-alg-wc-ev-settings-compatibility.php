<?php
/**
 * Email Verification for WooCommerce - Compatibility Section Settings.
 *
 * @version 2.1.3
 * @since   2.1.3
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Compatibility' ) ) :

	class Alg_WC_Email_Verification_Settings_Compatibility extends Alg_WC_Email_Verification_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 2.1.3
		 * @since   2.1.3
		 */
		function __construct() {
			$this->id   = 'compatibility';
			$this->desc = __( 'Compatibility', 'emails-verification-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 2.1.3
		 * @since   2.1.3
		 * @todo    (maybe) remove `alg_wc_ev_prevent_login_after_checkout_notice` (i.e. make it always enabled)
		 */
		function get_settings() {
			return array(
				array(
					'title' => __( 'Compatibility', 'emails-verification-for-woocommerce' ),
					'desc'  => __( 'Compatibility with third party plugins or solutions.', 'emails-verification-for-woocommerce' ) . '<br />' .
					           __( 'If you have issues with compatibility settings try to change these other options:', 'emails-verification-for-woocommerce' ) . '<br /><br />' .
					           alg_wc_ev_array_to_string( array(
						           __( 'Advanced > Block auth cookies', 'emails-verification-for-woocommerce' ),
						           __( 'Advanced > Authenticate filter', 'emails-verification-for-woocommerce' ),
						           __( 'Email > Activation email > Activation email delay', 'emails-verification-for-woocommerce' )
					           ), array( 'glue' => '<br />', 'item_template' => '&#8226; <strong>{value}</strong>' ) ),
					'type'  => 'title',
					'id'    => 'alg_wc_ev_compatibility_options',
				),
				array(
					'title'             => __( 'WooCommerce Social Login (SkyVerge)', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Accept verification from "<a target="_blank" href="%s">WooCommerce Social Login</a>" plugin made by Woocommerce author <a href="%s" target="_blank">SkyVerge</a>', 'emails-verification-for-woocommerce' ), 'https://woocommerce.com/products/woocommerce-social-login/', 'https://woocommerce.com/vendor/skyverge/' ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_ev_accept_social_login_skyverge',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'WooCommerce Social Login (wpweb)', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Accept verification from "<a target="_blank" href="%s">WooCommerce Social Login</a>" plugin made by CodeCanyon author <a href="%s" target="_blank">wpweb</a>', 'emails-verification-for-woocommerce' ), 'https://codecanyon.net/item/woocommerce-social-login-wordpress-plugin/8495883', 'https://codecanyon.net/user/wpweb' ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_ev_accept_social_login',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Super Socializer', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Accept verification from "<a target="_blank" href="%s">Super Socializer</a>" plugin', 'emails-verification-for-woocommerce' ), 'https://wordpress.org/plugins/super-socializer/' ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_ev_super_socializer_login',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'My Listing Social Login', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Accept verification from Social Login feature bundled on "<a target="_blank" href="%s">My Listing</a>" theme made by ThemeForest author <a href="%s" target="_blank">27collective</a>', 'emails-verification-for-woocommerce' ), 'https://themeforest.net/item/mylisting-directory-listing-wordpress-theme/20593226', 'https://themeforest.net/user/27collective' ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_ev_my_listing_social_login',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'    => __( 'Nextend Social Login', 'emails-verification-for-woocommerce' ),
					'desc'     => sprintf( __( 'Automatically verifies a user who registers or logins from "<a target="_blank" href="%s">Nextend Social Login</a>" plugin', 'emails-verification-for-woocommerce' ), 'https://wordpress.org/plugins/nextend-facebook-connect/' ),
					'desc_tip' => __( 'Leave it empty if you don\'t want to automatically verify an user from Nextend Social Login.', 'emails-verification-for-woocommerce' ),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'id'       => 'alg_wc_ev_nextend_verify',
					'default'  => array(''),
					'options' => array(
						'nsl_login'             => __( 'On login', 'emails-verification-for-woocommerce' ),
						'nsl_register_new_user' => __( 'On register new user', 'emails-verification-for-woocommerce' ),
					),
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'WooMail - WooCommerce Email Customizer', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Append verification email to the "Customer new account" email customized by the "<a target="_blank" href="%s">WooMail</a>" plugin', 'emails-verification-for-woocommerce' ), 'https://codecanyon.net/item/email-customizer-for-woocommerce-with-drag-drop-builder-woo-email-editor/22400984' ),
					'desc_tip'          => alg_wc_ev_array_to_string( array(
						sprintf( __( 'You need to use this shortcode in your template: %s', 'emails-verification-for-woocommerce' ), '<code>[ec_woo_custom_code type="alg_wc_ev_activation_email"]</code>' ),
						sprintf( __( 'It\'s necessary to enable %s option and use %s option as %s', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Emails > Activation email > Fine tune activation email placement', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Emails > Activation email > Email template', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Plain', 'emails-verification-for-woocommerce' ) . '</strong>' ),
						$this->separate_email_option_msg( 'disabled' ),
					), array( 'glue' => '<br />', 'item_template' => '&#8226; {value}' ) ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_ev_woomail',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_ev_compatibility_options',
				),
			);
		}

	}

endif;

return new Alg_WC_Email_Verification_Settings_Compatibility();
