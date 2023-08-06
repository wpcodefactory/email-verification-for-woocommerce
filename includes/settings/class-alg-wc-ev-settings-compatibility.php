<?php
/**
 * Email Verification for WooCommerce - Compatibility Section Settings.
 *
 * @version 2.5.3
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
		 * get_villatheme_email_customizer_placeholders.
		 *
		 * @version 2.3.9
		 * @since   2.3.9
		 *
		 * @return array
		 */
		function get_villatheme_email_customizer_placeholders() {
			$user                                         = wp_get_current_user();
			$placeholders                                 = alg_wc_ev_generate_placeholders_for_villatheme_email_customizer( $user );
			$placeholders['{alg_wc_ev_verification_url}'] = get_home_url() . '?alg_wc_ev_verify_email=999999';
			return array_keys($placeholders);
		}

		/**
		 * get_settings.
		 *
		 * @version 2.5.3
		 * @since   2.1.3
		 * @todo    (maybe) remove `alg_wc_ev_prevent_login_after_checkout_notice` (i.e. make it always enabled)
		 */
		function get_settings() {
			$general =  array(
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
						sprintf( __( 'You need to use this shortcode in your template: %s.', 'emails-verification-for-woocommerce' ), '<code>[ec_woo_custom_code type="alg_wc_ev_activation_email"]</code>' ),
						sprintf( __( 'It\'s necessary to enable %s option and use %s option as %s.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Emails > Activation email > Fine tune activation email placement', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Emails > Activation email > Email template', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Plain', 'emails-verification-for-woocommerce' ) . '</strong>' ),
						$this->separate_email_option_msg( 'disabled' ),
					), array( 'glue' => '<br />', 'item_template' => '{value}' ) ),
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
			$polylang_opts = array(
				array(
					'title' => __( 'Polylang', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with the %s plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://wordpress.org/plugins/polylang/', __( 'Polylang', 'emails-verification-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_ev_compatibility_polylang_options',
				),
				array(
					'title'             => __( 'Activation link', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Try to translate the activation link', 'emails-verification-for-woocommerce' ), '<code>alg_wc_ev_ec_email_content</code>' ),
					'desc_tip'          => sprintf( __( 'Tries sending the activation link and the emails with the correct language.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="https://help.themehigh.com/hc/en-us/articles/4405390768025-Add-New-Email-Template#h_01FDS804XTGFN0S9F2W736GKD6" target="_blank">%s</a>', __( 'Custom Hook', 'emails-verification-for-woocommerce' ) ) ),
					'id'                => 'alg_wc_ev_polylang_translate_activation_link',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_compatibility_polylang_options',
				),
			);
			$email_customizer_themehigh_opts = array(
				array(
					'title' => __( 'Email Customizer for WooCommerce by Themehigh', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with the %s plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://themehigh.com/product/woocommerce-email-customizer', __( 'Email Customizer for WooCommerce by Themehigh', 'emails-verification-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_ev_compatibility_email_customizer_options',
				),
				array(
					'title'             => __( 'Activation email content', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Display the activation email content on a custom template using the %s action hook', 'emails-verification-for-woocommerce' ), '<code>alg_wc_ev_ec_email_content</code>' ),
					'desc_tip'          => sprintf( __( 'The action hook should be used in a %s block from the Email Customizer plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="https://help.themehigh.com/hc/en-us/articles/4405390768025-Add-New-Email-Template#h_01FDS804XTGFN0S9F2W736GKD6" target="_blank">%s</a>', __( 'Custom Hook', 'emails-verification-for-woocommerce' ) ) ) . '<br />' .
					                       sprintf( __( 'It\'s necessary to enable %s option and use %s option as %s.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Emails > Activation email > Fine tune activation email placement', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Emails > Activation email > Email template', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Plain', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />' .
					                       $this->separate_email_option_msg( 'disabled' ),
					'id'                => 'alg_wc_ev_email_customizer_hook_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_compatibility_email_customizer_options',
				),
			);
			$email_customizer_villatheme_opts = array(
				array(
					'title' => __( 'Email Customizer for WooCommerce by VillaTheme', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with the %s plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://villatheme.com/extensions/woocommerce-email-template-customizer/', __( 'Email Customizer for WooCommerce by VillaTheme', 'emails-verification-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_ev_comp_email_customizer_vt_options',
				),
				array(
					'title'             => __( 'Activation email content', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Display the activation email content on the %s template using the %s special text', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'New account', 'emails-verification-for-woocommerce' ) . '</strong>', '<code>{alg_wc_ev_viwec}</code>' ),
					'desc_tip'          => sprintf( __( 'The action hook should be used in a %s from the Email Customizer plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="https://docs.villatheme.com/woocommerce-email-template-customizer/#configuration_child_menu_4653" target="_blank">%s</a>', __( 'Text element', 'emails-verification-for-woocommerce' ) ) ) . '<br />' .
					                       sprintf( __( 'It\'s necessary to enable %s option.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Emails > Activation email > Fine tune activation email placement', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Emails > Activation email > Email template', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />' .
					                       $this->separate_email_option_msg( 'disabled' ),
					'id'                => 'alg_wc_ev_comp_email_customizer_vt_special_text_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Placeholders', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Enable placeholders on the Email Customizer templates related to the Email Verification plugin', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'New account', 'emails-verification-for-woocommerce' ) . '</strong>', '<code>{alg_wc_ev_viwec}</code>' ),
					'desc_tip'          => sprintf( __( 'In order to enable the %s and %s email types, it\'s necessary to set the option %s as %s.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Activation', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Confirmation', 'emails-verification-for-woocommerce' ) . '</strong>', '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev&section=emails' ) . '">' . __( 'Email > WC email template', 'emails-verification-for-woocommerce' ) . '</a>', '<code>' . __( 'WooCommerce > Emails', 'emails-verification-for-woocommerce' ) . '</code>' ) . '<br />' .
					                       '<strong>' . __( 'Note:', 'emails-verification-for-woocommerce' ) . '</strong>' . ' ' . sprintf( __( 'The %s placeholder will only be available to the <strong>Activation email</strong> template.', 'emails-verification-for-woocommerce' ), '<code>{alg_wc_ev_verification_url}</code>' ),
					'id'                => 'alg_wc_ev_comp_email_customizer_vt_placeholders_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_comp_email_customizer_vt_options',
				),
			);
			$elementor_essential_addons_opts = array(
				array(
					'title' => __( 'Essential Addons for Elementor', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with the %s plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://essential-addons.com/elementor/', __( __( 'Essential Addons for Elementor', 'emails-verification-for-woocommerce' ), 'emails-verification-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_ev_compatibility_elementor_ea_options',
				),
				array(
					'title'             => __( 'Login Register form', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf( __( 'Verify users who register or log in from %s element', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Login Register form', 'emails-verification-for-woocommerce' ) . '</strong>' ),
					'desc_tip'          => sprintf( __( 'If you have issues, try to enable %s and set the %s option as %s.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'General > Account verification > Verify account for current users', 'emails-verification-for-woocommerce' ) . '</strong>','<strong>'.__( 'Advanced > Authenticate filter', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>'.__( 'Authenticate', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />' .
					                       $this->separate_email_option_msg( 'enabled' ),
					'id'                => 'alg_wc_ev_compatibility_elementor_ea_login_register_form',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_compatibility_elementor_ea_options',
				),
			);
			$paid_memberships_pro_opts = array(
				array(
					'title' => __( 'Paid Memberships Pro', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with the %s plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://wordpress.org/plugins/paid-memberships-pro/', __( 'Paid Memberships Pro', 'emails-verification-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_ev_compatibility_paid_memberships_pro_options',
				),
				array(
					'title'             => __( 'Account verification', 'emails-verification-for-woocommerce' ),
					'desc'              => __( 'Verify users that signs up via Paid Memberships Pro registration process', 'emails-verification-for-woocommerce' ),
					'id'                => 'alg_wc_ev_compatibility_pmpro_auto_verify_registration',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => __( 'Verify users that already have a valid membership', 'emails-verification-for-woocommerce' ),
					'id'                => 'alg_wc_ev_compatibility_pmpro_auto_verify_valid_membership',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'end',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_compatibility_paid_memberships_pro_options',
				),
			);
			$yaymail_opts = array(
				array(
					'title' => __( 'YayMail - WooCommerce Email Customizer', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with the %s plugin.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://wordpress.org/plugins/yaymail/', __( 'YayMail - WooCommerce Email Customizer', 'emails-verification-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_ev_compatibility_yaymail_options',
				),
				array(
					'title'             => __( 'Customer new account email', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf(__( 'Append the Activation email message to the "Customer new account" email using the %s shortcode', 'emails-verification-for-woocommerce' ),'<code>[yaymail_custom_shortcode_alg_wc_ev_aem]</code>'),
					'desc_tip'          => alg_wc_ev_array_to_string( array(
						sprintf( __( 'It\'s necessary to enable %s option and use %s option as %s.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Emails > Activation email > Fine tune activation email placement', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Emails > Activation email > Email template', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Plain', 'emails-verification-for-woocommerce' ) . '</strong>' ),
						$this->separate_email_option_msg( 'disabled' ),
					), array( 'glue' => '<br />', 'item_template' => '{value}' ) ),
					'id'                => 'alg_wc_ev_yaymail_activation_email_msg_sc',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_compatibility_yaymail_options',
				),
			);
			$woodmart_opts = array(
				array(
					'title' => __( 'WoodMart', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with the %s theme.', 'emails-verification-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://themeforest.net/item/woodmart-woocommerce-wordpress-theme/20264492', __( 'WoodMart', 'emails-verification-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_ev_compatibility_woodmart_options',
				),
				array(
					'title'             => __( 'Social authentication', 'emails-verification-for-woocommerce' ),
					'desc'              => sprintf(__( 'Auto verify users from WoodMart social authentication', 'emails-verification-for-woocommerce' ),'<code>[yaymail_custom_shortcode_alg_wc_ev_aem]</code>'),
					'desc_tip'          => sprintf( __( 'If you want to block login from unverified WoodMart users, try the option %s set as %s.', 'emails-verification-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev&section=advanced' ) . '">' . __( 'Authenticate filter', 'emails-verification-for-woocommerce' ) . '</a>', '<code>' . __( 'Send auth cookies', 'emails-verification-for-woocommerce' ) . '</code>' ),
					'id'                => 'alg_wc_ev_woodmart_auth_auto_verify',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_compatibility_woodmart_options',
				),
			);
			return array_merge(
				$general, $polylang_opts,
				$elementor_essential_addons_opts,
				$email_customizer_themehigh_opts,
				$email_customizer_villatheme_opts,
				$paid_memberships_pro_opts,
				$yaymail_opts,
				$woodmart_opts
			);
		}



	}

endif;

return new Alg_WC_Email_Verification_Settings_Compatibility();
