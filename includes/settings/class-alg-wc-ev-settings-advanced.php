<?php
/**
 * Email Verification for WooCommerce - Advanced Section Settings
 *
 * @version 2.0.8
 * @since   1.6.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Advanced' ) ) :

class Alg_WC_Email_Verification_Settings_Advanced extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * @version 2.0.1
	 * @since   2.0.1
	 *
	 * @var string
	 */
	private static $delete_users_cron_output = '';

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
	 * @version 2.0.8
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
				'title'    => __( 'Authenticate filter', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'WordPress filter used to check user authentication.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_auth_filter',
				'default'  => 'wp_authenticate_user',
				'options'  => array(
					'wp_authenticate_user'    => sprintf( __( '%s filter', 'emails-verification-for-woocommerce' ), '"wp_authenticate_user"' ),
					'authenticate' => sprintf( __( '%s filter', 'emails-verification-for-woocommerce' ), '"authenticate"' ),
				),
			),
			array(
				'title'    => __( 'Block auth cookies', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Prevent auth cookies from being sent to unverified users', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Enable if users are being able to login when they are not supposed to.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_block_auth_cookies',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Custom "logout" function', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Replace standard %s function by a custom one', 'emails-verification-for-woocommerce' ), '<code>wp_logout()</code>' ),
				'desc_tip' => __( 'Enable this if you are having issues with "Activate" notice not being displayed after user registration.', 'emails-verification-for-woocommerce' ) . '<br />' .
				              __( 'If your cart is getting cleared after a new account is created, you can also try to enable this option.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_custom_logout_function',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Replace HTML tags', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Replace angle brackets from HTML tags by other characters', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Enable if you have problem trying to save settings.', 'emails-verification-for-woocommerce' ).'<br />'.
				              __( 'Update the settings page containing HTML to see the value refreshed.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_replace_html_tags',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_advanced_options',
			),
			array(
				'title'    => __( 'Prevent login after register', 'emails-verification-for-woocommerce' ),
				//'desc'     => __( 'Prevents users from login automatically before their accounts are verified.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_prevent_login_after_register_options',
			),
			// Prevent login after register
			array(
				'title'    => __( 'Prevent login after register', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Prevent automatic user login after registration on "My Account" page', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_register',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Redirect', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_prevent_login_after_register_redirect',
				'default'  => 'no',
				'options'  => array(
					'no'     => __( 'No redirect', 'emails-verification-for-woocommerce' ),
					'yes'    => __( 'Force redirect to the "My Account" page', 'emails-verification-for-woocommerce' ),
					'custom' => __( 'Custom redirect', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Custom redirect URL', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( '"%s" must be selected for the "%s" option above.', 'emails-verification-for-woocommerce' ),
						__( 'Custom redirect', 'emails-verification-for-woocommerce' ), __( 'Redirect', 'emails-verification-for-woocommerce' ) ) . ' ' .
				              __( 'Must be a local URL.', 'emails-verification-for-woocommerce' ),
				'type'     => 'text',
				'id'       => 'alg_wc_ev_prevent_login_after_register_redirect_url',
				'default'  => '',
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Force redirect', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Force redirect using sessions after the user is registered', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Enable if the redirect is not working.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_register_session_redirect',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_prevent_login_after_register_options',
			),
			// Prevent login after checkout
			array(
				'title'    => __( 'Prevent login after checkout', 'emails-verification-for-woocommerce' ),
				//'desc'     => __( 'Prevents users from login automatically before their accounts are verified.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_options',
			),
			array(
				'title'    => __( 'Prevent login after checkout', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Prevent automatic user login after registration during checkout', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'If this option is not working correctly on your site, please try to change the %s option.', 'emails-verification-for-woocommerce' ),
					"<strong>" . __( 'Prevent login action', 'emails-verification-for-woocommerce' ) . "</strong>"

				),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Prevent login action', 'emails-verification-for-woocommerce' ),
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
				'title'    => __( 'Prevent login notice', 'emails-verification-for-woocommerce' ),
				'desc' => __( 'Add "Activate" notice to the WooCommerce "Thank you" (i.e. "Order received") page', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_notice',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Block thank you page', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Block "Thank you" (i.e. "Order received") page access for non-verified users', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Users will be redirected to the "My account" page.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_block_thankyou',
				'default'  => 'no',
				//'doc'      => array( 'dynamic_params' => array( 'pro' => array( 'active' => true ) ) ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_options',
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
				'desc_tip' => sprintf( __( 'Will only send the email related to the current order status and the %s email, if set on the %s option.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'New Order', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Blocked emails', 'emails-verification-for-woocommerce' ) . '</strong>' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_block_customer_order_emails_unblock',
				'default'  => 'yes',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_block_emails_options',
			),
			array(
				'title'    => __( 'Background processing', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_advanced_bkg_process_options',
			),
			array(
				'title'    => __( 'Minimum amount', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'The minimum amount of results from a query in order to trigger a background processing.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_bkg_process_min_amount',
				'default'  => 20,
				'type'     => 'number',
			),
			array(
				'title'    => __( 'Send email', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Send email when a background processing is complete', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_bkg_process_send_email',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'desc'       => __( 'Email to.', 'emails-verification-for-woocommerce' ),
				'desc_tip'    => __( 'The email address that is going to receive the email when a background processing task is complete.', 'emails-verification-for-woocommerce' ). '<br />' . __( 'Requires the "Send email" option enabled in order to work.', 'emails-verification-for-woocommerce' ),
				'id'          => 'alg_wc_ev_bkg_process_email_to',
				'placeholder' => get_option( 'admin_email' ),
				'default'     => get_option( 'admin_email' ),
				'type'        => 'text',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_advanced_bkg_process_options',
			),
			array(
				'title'    => __( 'Delete options', 'emails-verification-for-woocommerce' ),
				'desc' => __( 'Some notes regarding how this tool works:', 'emails-verification-for-woocommerce' ) . '<br />'
				          . '- ' . sprintf( __( 'It will only delete unverified users which roles are not set in the %s option.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Skip email verification for user roles', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				          . '- ' . sprintf( __( 'If the %s option is set it will only delete unverified users whose activation have expired, so it\'s more safe to use it.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Expire time', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				          . '- ' . sprintf( __( 'If the %s option is enabled it may delete the current users who have not verified their account yet, so it\'s more safe to leave it <strong>disabled</strong>.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Enable email verification for already registered users', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				          . '- ' . '<span style="font-weight: bold; color: red;">' . __( 'Deleted users can only be restored if you have a backup.', 'emails-verification-for-woocommerce' ) . '</span>',
				'type'     => 'title',
				'id'       => 'alg_wc_ev_admin_delete_options',
			),
			array(
				'title'    => __( 'Delete users', 'emails-verification-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Delete unverified users from the database', 'emails-verification-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Check the box and save changes to run the tool.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delete_users',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Delete users automatically', 'emails-verification-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Delete unverified users from the database automatically', 'emails-verification-for-woocommerce' ) . '</strong>',
				'desc_tip' => sprintf( __( 'Deletes based on the frequency option below.', 'emails-verification-for-woocommerce' ) )
				              . $this->get_delete_users_cron_info(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delete_users_cron',
				'default'  => 'no',
			),
			array(
				'desc'     => __( 'Frequency', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'If the frequency is changed after the Delete users automatically option is enabled, it will be necessary to disable and enable it again to see the frequency updated.', 'emails-verification-for-woocommerce' ),
				'options'  => array(
					'hourly'     => __( 'Hourly', 'emails-verification-for-woocommerce' ),
					'daily'      => __( 'Daily', 'emails-verification-for-woocommerce' ),
					'twicedaily' => __( 'Twice daily', 'emails-verification-for-woocommerce' ),
					'weekly'     => __( 'Weekly', 'emails-verification-for-woocommerce' ),
				),
				'id'       => 'alg_wc_ev_delete_users_cron_frequency',
				'default'  => 'weekly',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_admin_delete_options',
			),
			array(
				'title' => __( 'Compatibility', 'emails-verification-for-woocommerce' ),
				'desc'  => __( 'Compatibility with third party plugins or solutions.', 'emails-verification-for-woocommerce' ) . '<br />' .
				           __( 'If you have issues with compatibility settings try to change these other options:', 'emails-verification-for-woocommerce' ) . '<br /><br />' .
				           alg_wc_ev_array_to_string( array(
					           __( 'Advanced > Block auth cookies', 'emails-verification-for-woocommerce' ),
					           __( 'Advanced > Authenticate filter', 'emails-verification-for-woocommerce' ),
					           __( 'General > Activation link > Activation email delay', 'emails-verification-for-woocommerce' )
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
						sprintf( __( 'It\'s necessary to disable %s', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'General > Send verification as a separate email', 'emails-verification-for-woocommerce' ) . '</strong>' )
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

	/**
	 * get_delete_users_cron_info.
	 *
	 * @version 2.0.1
	 * @since   2.0.0
	 *
	 * @return string
	 */
	function get_delete_users_cron_info() {
		$delete_users_cron = 'yes' === get_option( 'alg_wc_ev_delete_users_cron', 'no' );
		if ( empty( self::$delete_users_cron_output ) ) {
			$output = '';
			if (
				( ! $event_timestamp = wp_next_scheduled( 'alg_wc_ev_delete_unverified_users' ) )
				&& isset( $_POST['alg_wc_ev_delete_users_cron'] )
			) {
				$output = '<br />';
				$output .= '<span style="font-weight: bold; color: green;">' . __( 'Please, reload the page to see the next scheduled event info.', 'emails-verification-for-woocommerce' ) . '</span>';
			} elseif ( $event_timestamp && $delete_users_cron ) {
				$output              = '<br />';
				$now                 = current_time( 'timestamp', true );
				$pretty_time_missing = human_time_diff( $now, $event_timestamp );
				$output              .= sprintf( __( 'Next event scheduled to %s', 'emails-verification-for-woocommerce' ), '<strong>' . get_date_from_gmt( date( 'Y-m-d H:i:s', $event_timestamp ), get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) ) . '</strong>' );
				$output              .= ' ' . '(' . $pretty_time_missing . ' left)';
			}
			self::$delete_users_cron_output = $output;
		} else {
			if ( ! $delete_users_cron ) {
				self::$delete_users_cron_output = '';
			}
		}
		return self::$delete_users_cron_output;
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Advanced();
