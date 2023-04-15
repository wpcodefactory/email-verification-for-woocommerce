<?php
/**
 * Email Verification for WooCommerce - Advanced Section Settings.
 *
 * @version 2.5.3
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
	 * @version 2.5.3
	 * @since   1.6.0
	 * @todo    (maybe) remove `alg_wc_ev_prevent_login_after_checkout_notice` (i.e. make it always enabled)
	 */
	function get_settings() {
		$advanced_opts = array(
			array(
				'title' => __( 'Advanced Options', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_advanced_options',
			),
			array(
				'title'    => __( 'Authenticate filter', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'WordPress filter used to check user authentication.', 'emails-verification-for-woocommerce' ) . '<br />' .
				              sprintf( __( 'Use the %s filter to priorize the %s message over the %s message.', 'emails-verification-for-woocommerce' ), 'authenticate', __( 'verification error', 'emails-verification-for-woocommerce' ), __( 'incorrect password', 'emails-verification-for-woocommerce' ) ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_auth_filter',
				'default'  => 'wp_authenticate_user',
				'options'  => array(
					'wp_authenticate_user' => sprintf( __( '%s filter', 'emails-verification-for-woocommerce' ), '"wp_authenticate_user"' ),
					'authenticate'         => sprintf( __( '%s filter', 'emails-verification-for-woocommerce' ), '"authenticate"' ),
					'send_auth_cookies'    => sprintf( __( '%s filter', 'emails-verification-for-woocommerce' ), '"send_auth_cookies"' ),
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
				'desc_tip' => __( 'Enable if you have problem trying to save settings.', 'emails-verification-for-woocommerce' ) . '<br />' .
				              __( 'Update the settings page containing HTML to see the value refreshed.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_replace_html_tags',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Session start params', 'emails-verification-for-woocommerce' ),
				'type'     => 'textarea',
				'id'       => 'alg_wc_ev_session_start_params',
				'desc'     => $this->get_session_start_params_desc(),
				'desc_tip' => __( 'Only going to be used in case an option needs Session, like "Prevent login after register > Force redirect". You can leave it empty if you don\'t know what parameters to use.', 'emails-verification-for-woocommerce' ),
				'default'  => wp_json_encode( alg_wc_ev_get_default_session_start_params(), JSON_PRETTY_PRINT ),
				'css'      => $this->get_session_start_params_css()
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_advanced_options',
			)
		);

		$encoding_options = array(
			array(
				'title' => __( 'Encoding options', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_encoding_options',
			),
			array(
				'title'   => __( 'Encoding method', 'emails-verification-for-woocommerce' ),
				'type'    => 'select',
				'class'   => 'chosen_select',
				'default' => 'base64_encode',
				'options' => array(
					'base64_encode' => __( 'Base64 encode', 'emails-verification-for-woocommerce' ),
					'hashids'       => __( 'Hashids', 'emails-verification-for-woocommerce' ),
				),
				'id'      => 'alg_wc_ev_encoding_method',
			),
			array(
				'title'    => __( 'Hashids', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Hashids salt.', 'emails-verification-for-woocommerce' ) . $this->get_empty_warning_msg( array(
						'option_id'   => $hashids_salt_id = 'alg_wc_ev_hashids_salt',
						'default_opt' => alg_wc_ev()->core->get_default_hashids_salt_opt(),
						'msg'         => __( 'Please, do not leave the option empty. Set an unpredictable random value.', 'emails-verification-for-woocommerce' ),
					) ),
				'desc_tip' => __( 'Salt adds random data to the input of a hash function to guarantee a unique output, even when the inputs are the same.', 'emails-verification-for-woocommerce' ) . ' ' . __( 'Set an unpredictable random value here.', 'emails-verification-for-woocommerce' ),
				'default'  => alg_wc_ev()->core->get_default_hashids_salt_opt(),
				'css'      => $this->set_red_border_if_empty( $hashids_salt_id, alg_wc_ev()->core->get_default_hashids_salt_opt() ),
				'type'     => 'text',
				'id'       => $hashids_salt_id,
			),
			array(
				'desc'     => __( 'Hashids Alphabet.', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'The alphabet is used to setup the possible characters of the output.', 'emails-verification-for-woocommerce' ),
				'type'     => 'text',
				'default'  => 'abcdefghijklmnopqrstuvwxyz1234567890',
				'id'       => 'alg_wc_ev_hashids_alphabet',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_encoding_options',
			)
		);

		$prevent_login_after_register_opts = array(
			array(
				'title' => __( 'Prevent login after register', 'emails-verification-for-woocommerce' ),
				//'desc'     => __( 'Prevents users from login automatically before their accounts are verified.', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_prevent_login_after_register_options',
			),
			// Prevent login after register
			array(
				'title'   => __( 'Prevent login after register', 'emails-verification-for-woocommerce' ),
				'desc'    => __( 'Prevent automatic user login after registration on "My Account" page', 'emails-verification-for-woocommerce' ),
				'type'    => 'checkbox',
				'id'      => 'alg_wc_ev_prevent_login_after_register',
				'default' => 'yes',
			),
			array(
				'title'   => __( 'Login prevention method', 'emails-verification-for-woocommerce' ),
				'desc'    => sprintf( __( 'Note: The %s method sets the %s filter as %s.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'login filter from WooCommerce', 'emails-verification-for-woocommerce' ) . '</strong>', '<code>woocommerce_registration_auth_new_customer</code>', '<code>false</code>' ),
				'type'    => 'select',
				'class'   => 'chosen_select',
				'id'      => 'alg_wc_ev_prevent_login_after_register_method',
				'options' => array(
					'logout_after_login'            => __( 'Logout right after login', 'emails-verification-for-woocommerce' ),
					'prevent_login_using_wc_filter' => __( 'Use login filter from WooCommerce', 'emails-verification-for-woocommerce' ),
				),
				'default' => 'logout_after_login',
			),
			array(
				'title'   => __( 'Redirect', 'emails-verification-for-woocommerce' ),
				'type'    => 'select',
				'class'   => 'chosen_select',
				'id'      => 'alg_wc_ev_prevent_login_after_register_redirect',
				'default' => 'no',
				'options' => array(
					'no'     => __( 'No redirect', 'emails-verification-for-woocommerce' ),
					'yes'    => __( 'Force redirect to the "My Account" page', 'emails-verification-for-woocommerce' ),
					'custom' => __( 'Custom redirect', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'         => __( 'Custom redirect URL', 'emails-verification-for-woocommerce' ),
				'desc_tip'      => sprintf( __( '"%s" must be selected for the "%s" option above.', 'emails-verification-for-woocommerce' ),
						__( 'Custom redirect', 'emails-verification-for-woocommerce' ), __( 'Redirect', 'emails-verification-for-woocommerce' ) ) . ' ' .
				                   __( 'Must be a local URL.', 'emails-verification-for-woocommerce' ),
				'type'          => 'text',
				'id'            => 'alg_wc_ev_prevent_login_after_register_redirect_url',
				'default'       => '',
				'css'           => 'width:100%;',
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
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_prevent_login_after_register_options',
			),
		);

		$prevent_login_after_checkout_opts = array(
			array(
				'title' => __( 'Prevent login after checkout', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_prevent_login_after_checkout_options',
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
				'title'   => __( 'Prevent login notice', 'emails-verification-for-woocommerce' ),
				'desc'    => __( 'Add "Activate" notice to the WooCommerce "Thank you" (i.e. "Order received") page', 'emails-verification-for-woocommerce' ),
				'type'    => 'checkbox',
				'id'      => 'alg_wc_ev_prevent_login_after_checkout_notice',
				'default' => 'yes',
			),
			array(
				'title'             => __( 'Block thank you page', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Block "Thank you" (i.e. "Order received") page access for non-verified users', 'emails-verification-for-woocommerce' ),
				'desc_tip'          => __( 'Users will be redirected to the "My account" page.', 'emails-verification-for-woocommerce' ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_ev_prevent_login_after_checkout_block_thankyou',
				'default'           => 'no',
				//'doc'      => array( 'dynamic_params' => array( 'pro' => array( 'active' => true ) ) ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_prevent_login_after_checkout_options',
			),
		);

		$bkg_processing_opts = array(
			array(
				'title' => __( 'Background processing', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_advanced_bkg_process_options',
			),
			array(
				'title'    => __( 'Minimum amount', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'The minimum amount of results from a query in order to trigger a background processing.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_bkg_process_min_amount',
				'default'  => 20,
				'type'     => 'number',
			),
			array(
				'title'   => __( 'Send email', 'emails-verification-for-woocommerce' ),
				'desc'    => __( 'Send email when a background processing is complete', 'emails-verification-for-woocommerce' ),
				'id'      => 'alg_wc_ev_bkg_process_send_email',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'desc'        => __( 'Email to.', 'emails-verification-for-woocommerce' ),
				'desc_tip'    => __( 'The email address that is going to receive the email when a background processing task is complete.', 'emails-verification-for-woocommerce' ) . '<br />' . __( 'Requires the "Send email" option enabled in order to work.', 'emails-verification-for-woocommerce' ),
				'id'          => 'alg_wc_ev_bkg_process_email_to',
				'placeholder' => get_option( 'admin_email' ),
				'default'     => get_option( 'admin_email' ),
				'type'        => 'text',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_advanced_bkg_process_options',
			),
		);

		$rest_api_options = array(
			array(
				'title' => __( 'REST API options', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_rest_api_options',
			),
			array(
				'title'             => __( 'Verify endpoint', 'emails-verification-for-woocommerce' ),
				'desc'              => sprintf( __( 'Add %s endpoint allowing to verify users via REST API', 'emails-verification-for-woocommerce' ), '<code>' . 'alg_wc_ev/v1/verify' . '</code>' ),
				'desc_tip'          => sprintf( __( 'Full URL example: %s', 'emails-verification-for-woocommerce' ), '<code>' . get_home_url() . '/wp-json/alg_wc_ev/v1/verify?verify_code=eyJpZCI6NCwiY29kZSI6Ijg0Mzk5ZjcwMzVkM2EwMjcxNGE3N2MxMTcxNjExNmQ3In0-' . '</code>' ),
				'type'              => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				'id'                => 'alg_wc_ev_rest_api_verify_endpoint',
				'default'           => 'no',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_rest_api_options',
			),
		);

		$delete_opts = array(
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_admin_delete_options',
			),

			array(
				'title' => __( 'Delete options', 'emails-verification-for-woocommerce' ),
				'desc'  => __( 'Some notes regarding how this tool works:', 'emails-verification-for-woocommerce' ) . '<br />'
				           . '- ' . sprintf( __( 'It will only delete unverified users which roles are not set in the %s option.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Skip email verification for user roles', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				           . '- ' . sprintf( __( 'If the %s option is set it will only delete unverified users whose activation have expired, so it\'s more safe to use it.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Expire time', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				           . '- ' . sprintf( __( 'If the %s option is enabled it may delete the current users who have not verified their account yet, so it\'s more safe to leave it <strong>disabled</strong>.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Enable email verification for already registered users', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				           . '- ' . '<span style="font-weight: bold; color: red;">' . __( 'Deleted users can only be restored if you have a backup.', 'emails-verification-for-woocommerce' ) . '</span>',
				'type'  => 'title',
				'id'    => 'alg_wc_ev_admin_delete_options',
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
		);
		return array_merge(
			$advanced_opts,
			$encoding_options,
			$prevent_login_after_register_opts,
			$prevent_login_after_checkout_opts,
			$bkg_processing_opts,
			$rest_api_options,
			$delete_opts
		);
	}

	/**
	 * get_sanitization_content_desc.
	 *
	 * @version 2.3.4
	 * @since   2.3.4
	 *
	 * @return string
	 */
	function get_session_start_params_desc() {
		$desc = ! $this->is_session_start_params_option_valid() ? '<span style="color:red">' . __( 'JSON not valid. Please check the content.', 'emails-verification-for-woocommerce' ) . '</span>' : '';
		return $desc;
	}

	/**
	 * get_sanitization_content_css.
	 *
	 * @version 2.3.4
	 * @since   2.3.4
	 *
	 * @return string
	 */
	function get_session_start_params_css() {
		$css = 'min-height:110px;';
		if ( ! $this->is_session_start_params_option_valid() ) {
			$css .= 'border:1px solid red;';
		}
		return $css;
	}

	/**
	 * sanitization_content_valid.
	 *
	 * @version 2.3.4
	 * @since   2.3.4
	 *
	 * @return bool
	 */
	function is_session_start_params_option_valid() {
		$allowed_html = get_option( 'alg_wc_ev_session_start_params', wp_json_encode( alg_wc_ev_get_default_session_start_params() ) );
		$ob           = json_decode( $allowed_html );
		if ( $ob === null ) {
			return false;
		} else {
			return true;
		}
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
