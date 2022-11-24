<?php
/**
 * Email Verification for WooCommerce - Blocking Section Settings.
 *
 * @version 2.4.7
 * @since   2.1.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Blocking' ) ) :

	class Alg_WC_Email_Verification_Settings_Blocking extends Alg_WC_Email_Verification_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 2.1.1
		 * @since   2.1.1
		 */
		function __construct() {
			$this->id   = 'blocking';
			$this->desc = __( 'Blocking', 'emails-verification-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_products_options.
		 *
		 * @version 2.1.1
		 * @since   2.1.1
		 *
		 * @param null $args
		 *
		 * @return array
		 */
		function get_products_options( $args = null ) {
			$args     = wp_parse_args( $args, array(
				'option_id' => '',
				'default'   => array()
			) );
			$products = get_option( $args['option_id'], $args['default'] );
			$options  = array();
			foreach ( $products as $product_id ) {
				$product                       = wc_get_product( $product_id );
				$options[ $product->get_id() ] = wp_kses_post( $product->get_formatted_name() );
			}
			return $options;
		}

		/**
		 * get_blocked_products_custom_attributes.
		 *
		 * @version 2.1.1
		 * @since   2.1.1
		 *
		 * @return array
		 */
		function get_blocked_products_custom_attributes() {
			$attributes = array(
				'data-action'      => 'woocommerce_json_search_products',
				'data-allow_clear' => "true",
				'aria-hidden'      => "true",
				'data-sortable'    => "true",
			);
			if ( true === apply_filters( 'alg_wc_ev_settings', true ) ) {
				$attributes['disabled'] = 'disabled';
			}
			return $attributes;
		}

		/**
		 * get_settings.
		 *
		 * @version 2.4.7
		 * @since   2.1.1
		 */
		function get_settings() {
			return array(
				array(
					'title' => __( 'Block content', 'emails-verification-for-woocommerce' ),
					'desc'  => __( 'Blocks content for unverified users (including guests) by limiting access to the content URL.', 'emails-verification-for-woocommerce' ) . '<br />' .
					           $this->get_block_unverify_login_option_warning(),
					'type'  => 'title',
					'id'    => 'alg_wc_ev_block_content_options',
				),
				array(
					'title'    => __( 'Blocked pages', 'emails-verification-for-woocommerce' ),
					'type'     => 'multiselect',
					'id'       => 'alg_wc_ev_blocked_pages',
					'class'    => 'chosen_select',
					'css'      => 'width:100%;',
					'default'  => array(),
					'options'  => wp_list_pluck( get_pages(), 'post_title', 'ID' ),
				),
				array(
					'title'             => __( 'Blocked products', 'emails-verification-for-woocommerce' ),
					'type'              => 'multiselect',
					'id'                => $blocked_products_id = 'alg_wc_ev_blocked_products',
					'class'             => 'wc-product-search',
					'css'      => 'width:100%;',
					'custom_attributes' => $this->get_blocked_products_custom_attributes(),
					'default'           => $blocked_products_default = array(),
					'options'           => $this->get_products_options( array( 'option_id' => $blocked_products_id, 'default' => $blocked_products_default ) ),
				),
				array(
					'title'             => __( 'Conditionals', 'emails-verification-for-woocommerce' ),
					'desc_tip'          => __( 'Blocks content by checking conditionals like if is shop, is product, etc.', 'emails-verification-for-woocommerce' ),
					'type'              => 'multiselect',
					'id'                => 'alg_wc_ev_blocked_conditionals',
					'class'             => 'chosen_select',
					'css'      => 'width:100%;',
					'default'           => array(),
					'options'           => array(
						'is_woocommerce'      => __( 'Is WooCommerce', 'popup-notices-for-woocommerce' ),
						'is_shop'             => __( 'Is Shop', 'popup-notices-for-woocommerce' ),
						'is_product_category' => __( 'Is Product Category', 'popup-notices-for-woocommerce' ),
						'is_product_tag'      => __( 'Is Product Tag', 'popup-notices-for-woocommerce' ),
						'is_product'          => __( 'Is Product', 'popup-notices-for-woocommerce' ),
						'is_cart'             => __( 'Is Cart', 'popup-notices-for-woocommerce' ),
						'is_checkout'         => __( 'Is Checkout', 'popup-notices-for-woocommerce' ),
						'is_account_page'     => __( 'Is Account Page', 'popup-notices-for-woocommerce' ),
						'is_wc_endpoint_url'  => __( 'Is WC Endpoint URL', 'popup-notices-for-woocommerce' ),
						'is_ajax'             => __( 'Is AJAX', 'popup-notices-for-woocommerce' ),
					),
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'                  => __( 'Blocked URL(s)', 'emails-verification-for-woocommerce' ),
					'desc_tip'               => __( 'Blocks content by checking the current URL. Use 1 URL per line.', 'emails-verification-for-woocommerce' ),
					'type'                   => 'textarea',
					'id'                     => 'alg_wc_ev_blocked_urls',
					'default'                => '',
					'css'                    => 'width:100%;min-height:70px;',
					'alg_wc_ev_textarea_url' => true,
					'custom_attributes'      => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'    => __( 'Redirect', 'emails-verification-for-woocommerce' ),
					'type'     => 'text',
					'id'       => 'alg_wc_ev_block_content_redirect',
					'default'  => home_url(),
					'css'      => 'width:100%;',
					'alg_wc_ev_raw' => true,
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				),
				array(
					'title'    => __( 'Error notice', 'emails-verification-for-woocommerce' ),
					'type'     => 'textarea',
					'id'       => 'alg_wc_ev_block_content_notice',
					'desc'     => __( 'Error for logged in unverified users.', 'emails-verification-for-woocommerce' ).' '.$this->available_placeholders_desc( array( '%myaccount_url%','%resend_verification_url%' ) ),
					'default'  => __( 'You need to <a href="%myaccount_url%">verify your account</a> to access this content.', 'emails-verification-for-woocommerce' ) . ' ' . __( 'You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' ),
					'css'      => 'width:100%;',
					'alg_wc_ev_raw' => true,
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				),
				array(
					'type'     => 'textarea',
					'id'       => 'alg_wc_ev_block_content_notice_guests',
					'desc'     => __( 'Error for guest users.', 'emails-verification-for-woocommerce' ).' '.$this->available_placeholders_desc( array( '%myaccount_url%' ) ),
					'default'  => __( 'You need to <a href="%myaccount_url%">verify your account</a> to access this content.', 'emails-verification-for-woocommerce' ),
					'css'      => 'width:100%;',
					'alg_wc_ev_raw' => true,
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_ev_block_content_options',
				),
				array(
					'title'    => __( 'Block checkout', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Blocks checkout process for unverified users.', 'emails-verification-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_ev_block_checkout_options',
				),
				array(
					'title'    => __( 'Block checkout', 'emails-verification-for-woocommerce' ),
					'desc' => __( 'Blocks checkout process for unverified users (including guests).', 'emails-verification-for-woocommerce' ),
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
					'desc' => __( 'Blocks guests from adding any products to the cart', 'emails-verification-for-woocommerce' ),
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
					'desc'          => __( 'Block activation link until the customer places an order and its status is considered paid', 'emails-verification-for-woocommerce' ),
					'desc_tip'      => __( 'Won\'t block users already verified.', 'emails-verification-for-woocommerce' ).'<br />'.
					                   sprintf( __( 'The order status should be marked as %s to be considered paid.', 'emails-verification-for-woocommerce' ), '<strong>' . wc_get_order_status_name( 'wc-completed' ) . '</strong>' ),
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => 'start',
					'id'            => 'alg_wc_ev_block_nonpaying_users_activation',
				),
				array(
					'title'             => __( 'Activation email', 'emails-verification-for-woocommerce' ),
					'desc'              => __( 'Send the activation email only when a non-free order is considered paid', 'emails-verification-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'Won\'t send the activation email to already verified users.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Verify paying customers automatically', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />' .
					                       $this->get_paid_statuses_msg(),
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
					'title'    => __( 'Block order emails', 'emails-verification-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_ev_block_emails_options',
				),
				array(
					'title'    => __( 'Block order emails', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Block WooCommerce order emails for all non-verified users (including guests)', 'emails-verification-for-woocommerce' ),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_ev_block_customer_order_emails',
					'default'  => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'    => __( 'Blocked emails', 'emails-verification-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Consider adding the %s email if you want to prevent the admin from receiving the new order notification.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'New Order', 'emails-verification-for-woocommerce' ) . '</strong>' ),
					'type'     => 'multiselect',
					'options'  => $this->get_emails(),
					'default'  => array( 'customer_on_hold_order', 'customer_processing_order', 'customer_completed_order' ),
					'class'    => 'chosen_select',
					'id'       => 'alg_wc_ev_block_customer_order_emails_email_ids',
				),
				array(
					'title'    => __( 'Unblock emails', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Send blocked emails to users who have just verified accounts', 'emails-verification-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Will only send the email related to the current order status and the %s email if set on the %s option.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'New Order', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Blocked emails', 'emails-verification-for-woocommerce' ) . '</strong>' ),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_ev_block_customer_order_emails_unblock',
					'default'  => 'yes',
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_ev_block_emails_options',
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
						'<code>*</code>', '<code>*@example.com,email@example.net</code>' ),
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
					'default'  => __( 'Your email is denied.', 'emails-verification-for-woocommerce' ),
					'css'      => 'width:100%;',
					'alg_wc_ev_raw' => true,
					'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_ev_block_activation_by_email_options',
				),
				array(
					'title'    => __( 'Log out unverified users', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Logs out unverified users in some specific situations.', 'emails-verification-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_ev_logout_options',
				),
				array(
					'title'    => __( 'Log out unverified users on "My Account" page', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Check if logged user is verified on "My Account" page', 'emails-verification-for-woocommerce' ),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_ev_prevent_login_myaccount',
					'default'  => 'no',
				),
				array(
					'title'    => __( 'Log out unverified users on every page', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Check if logged user is verified on every page of your site', 'emails-verification-for-woocommerce' ),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_ev_prevent_login_always',
					'default'  => 'no',
					'checkboxgroup' => 'start',
				),
				array(
					'desc'     => __( 'Redirect to the activate account notice after logout', 'emails-verification-for-woocommerce' ),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_ev_prevent_login_always_redirect',
					'default'  => 'yes',
					'checkboxgroup' => 'end',
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_ev_logout_options',
				),
			);
		}

		/**
		 * get_emails.
		 *
		 * @version 2.0.8
		 * @since   2.0.8
		 *
		 * @return array
		 */
		function get_emails() {
			$emails = wc()->mailer()->get_emails();
			return wp_list_pluck( $emails, 'title', 'id' );
		}

	}

endif;

return new Alg_WC_Email_Verification_Settings_Blocking();
