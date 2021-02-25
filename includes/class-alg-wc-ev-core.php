<?php
/**
 * Email Verification for WooCommerce - Core Class
 *
 * @version 2.0.6
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Core' ) ) :

class Alg_WC_Email_Verification_Core {

	/**
	 * Constructor.
	 *
	 * @version 2.0.5
	 * @since   1.0.0
	 * @todo    [next] (maybe) `[alg_wc_ev_translate]` to description in readme.txt
	 */
	function __construct() {
		// Functions
		require_once( 'alg-wc-ev-functions.php' );
		// Verification actions
		add_action( 'init', array( $this, 'verify' ),   PHP_INT_MAX );
		add_action( 'init', array( $this, 'activate' ), PHP_INT_MAX );
		add_action( 'init', array( $this, 'resend' ),   PHP_INT_MAX );
		// Prevent login
		require_once( 'class-alg-wc-ev-logouts.php' );
		// Emails
		$this->emails = require_once( 'class-alg-wc-ev-emails.php' );
		// Messages
		$this->messages = require_once( 'class-alg-wc-ev-messages.php' );
		// Shortcodes
		add_shortcode( 'alg_wc_ev_translate', array( $this, 'language_shortcode' ) );
		// Admin stuff
		require_once( 'class-alg-wc-ev-admin.php' );
		// Non Paying Blocker
		require_once( 'class-alg-wc-ev-non-paying-blocker.php' );
		// HTML tags converter
		$this->setup_html_tags_converter();
		// Background process
		add_action( 'plugins_loaded', array( $this, 'init_bkg_process' ), 9 );
		// Core loaded
		do_action( 'alg_wc_ev_core_loaded', $this );
		// Login the user automatically
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'login_user_automatically_on_success_activation' ) );
		// Redirect on success activation
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'redirect_on_success_activation' ), 100 );
		// Success activation message
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'display_success_activation_message' ) );
		add_action( 'init', array( $this, 'display_success_activation_message' ) );
		add_filter( 'wp_redirect', array( $this, 'remove_success_activation_message' ) );
	}

	/**
	 * init_bkg_process.
	 *
	 * @version 2.0.1
	 * @since   2.0.1
	 */
	function init_bkg_process() {
		require_once( 'background-process/class-alg-wc-ev-bkg-process.php' );
		add_filter( 'alg_wc_ev_bkg_process_email_params', array( $this, 'change_bkg_process_email_params' ) );
		new Alg_WC_Email_Verification_Bkg_Process();
	}

	/**
	 * change_bkg_process_email_params.
	 *
	 * @version 2.0.1
	 * @since   2.0.1
	 *
	 * @param $email_params
	 *
	 * @return mixed
	 */
	function change_bkg_process_email_params( $email_params ) {
		$email_params['send_email_on_task_complete'] = 'yes' === get_option( 'alg_wc_ev_bkg_process_send_email', 'yes' );
		$email_params['send_to']                     = get_option( 'alg_wc_ev_bkg_process_email_to', get_option( 'admin_email' ) );
		return $email_params;
	}

	/**
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @param $redirect_to
	 *
	 * @return string
	 */
	function remove_success_activation_message( $redirect_to ) {
		if ( isset( $_GET['alg_wc_ev_success_activation_message'] ) ) {
			$redirect_to = remove_query_arg( 'alg_wc_ev_success_activation_message', $redirect_to );
		}
		return $redirect_to;
	}

	/**
	 * login_user_automatically_on_success_activation.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @param $user_id
	 */
	function login_user_automatically_on_success_activation( $user_id ) {
		if ( 'yes' == get_option( 'alg_wc_ev_login_automatically_on_activation', 'yes' ) ) {
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id );
		}
	}

	/**
	 * display_success_activation_message.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function display_success_activation_message() {
		$display_message = false;
		if ( 'alg_wc_ev_user_account_activated' == current_filter() ) {
			$display_message = true;
		} elseif ( isset( $_GET['alg_wc_ev_success_activation_message'] ) ) {
			$display_message = true;
		}
		if ( $display_message ) {
			wc_add_notice( $this->messages->get_success_message() );
		}
	}

	/**
	 * redirect_on_success_activation.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 */
	function redirect_on_success_activation() {
		if ( 'no' != ( $redirect = get_option( 'alg_wc_ev_redirect_to_my_account_on_success', 'yes' ) ) ) {
			switch ( $redirect ) {
				case 'home':
					$redirect_url = get_home_url();
					break;
				case 'shop':
					$redirect_url = wc_get_page_permalink( 'shop' );
					break;
				case 'custom':
					$redirect_url = get_option( 'alg_wc_ev_redirect_on_success_url', '' );
					break;
				default: // 'yes'
					$redirect_url = wc_get_page_permalink( 'myaccount' );
			}
			$redirect_url = add_query_arg( array( 'alg_wc_ev_success_activation_message' => 1 ), $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * setup_html_tags_converter.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function setup_html_tags_converter() {
		$this->html_tags_converter = require_once( 'class-alg-wc-ev-html-tags-converter.php' );
		$this->html_tags_converter->init( array(
			'wc_tab_id' => 'alg_wc_ev',
			'database'  => array(
				'converter_id'    => 'alg_wc_ev_replace_html_tags',
				'replacement_ids' => array(
					'alg_wc_ev_error_message',
					'alg_wc_ev_failed_message',
					'alg_wc_ev_activation_message',
					'alg_wc_ev_email_resend_message',
					'alg_wc_ev_email_subject',
					'alg_wc_ev_email_content',
					'alg_wc_ev_email_template_wc_heading',
					'alg_wc_ev_admin_email_heading',
					'alg_wc_ev_admin_email_content',
					'alg_wc_ev_redirect_on_success_url',
					'alg_wc_ev_prevent_login_after_register_redirect_url',
					'alg_wc_ev_activation_code_expired_message',
					'alg_wc_ev_block_checkout_process_notice',
					'alg_wc_ev_block_guest_add_to_cart_notice',
					'alg_wc_ev_block_nonpaying_users_activation_error_notice',
					'alg_wc_ev_blacklisted_message'
				),
			)
		) );
	}

	/**
	 * language_in.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function language_in( $needle, $haystack ) {
		return in_array( strtolower( $needle ), array_map( 'trim', explode( ',', strtolower( $haystack ) ) ) );
	}

	/**
	 * get_language.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 * @todo    [next] (maybe) email: add `lang` param to the `alg_wc_ev_user_id`
	 * @todo    [next] (maybe) email: use `locale` ("Language") field from user profile
	 * @todo    [next] (maybe) email: `billing_country`?
	 * @todo    [next] (maybe) email: `shipping_country` fallback?
	 * @todo    [next] (maybe) email: TLD fallback?
	 */
	function get_language() {
		return ( defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : false );
	}

	/**
	 * language_shortcode.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function language_shortcode( $atts, $content = '' ) {
		$language = $this->get_language();
		// E.g.: `[alg_wc_ev_translate lang="EN,DE" lang_text="Text for EN & DE" not_lang_text="Text for other languages"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! $language || ! $this->language_in( $language, $atts['lang'] ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_ev_translate lang="EN,DE"]Text for EN & DE[/alg_wc_ev_translate][alg_wc_ev_translate not_lang="EN,DE"]Text for other languages[/alg_wc_ev_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! $language || ! $this->language_in( $language, $atts['lang'] ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     $language &&   $this->language_in( $language, $atts['not_lang'] ) )
		) ? '' : $content;
	}

	/**
	 * add_to_log.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function add_to_log( $message ) {
		if ( function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) ) {
			$log->log( 'info', $message, array( 'source' => 'alg-wc-ev' ) );
		}
	}

	/**
	 * is_user_verified_by_user_id.
	 *
	 * @version 1.6.0
	 * @since   1.5.0
	 */
	function is_user_verified_by_user_id( $user_id = false, $is_guest_verified = false ) {
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}
		if ( 0 == $user_id ) {
			return $is_guest_verified;
		}
		$user = new WP_User( $user_id );
		return $this->is_user_verified( $user, $is_guest_verified );
	}

	/**
	 * is_user_verified.
	 *
	 * @version 1.8.0
	 * @since   1.1.0
	 */
	function is_user_verified( $user, $is_guest_verified = false ) {
		if ( ! $user || is_wp_error( $user ) || 0 == $user->ID || empty( $user->roles ) ) {
			return $is_guest_verified;
		}
		if ( apply_filters( 'alg_wc_ev_is_user_verified', false, $user->ID ) ) {
			return true;
		}
		$do_verify_already_registered = ( 'yes' === get_option( 'alg_wc_ev_verify_already_registered', 'no' ) );
		$is_user_email_activated      = get_user_meta( $user->ID, 'alg_wc_ev_is_activated', true );
		if (
			( ( $do_verify_already_registered && ! $is_user_email_activated ) || ( ! $do_verify_already_registered && '0' === $is_user_email_activated ) ) &&
			! $this->is_user_role_skipped( $user )
		) {
			return false;
		}
		return true;
	}

	/**
	 * is_user_role_skipped.
	 *
	 * @version 1.9.3
	 * @since   1.6.0
	 * @todo    [next] (maybe) always include `administrator` (i.e. even if `$skip_user_roles` is empty)?
	 */
	function is_user_role_skipped( $user ) {
		if ( isset( $user->roles ) && ! empty( $user->roles ) ) {
			$user_roles      = $user->roles;
			$skip_user_roles = get_option( 'alg_wc_ev_skip_user_roles', array( 'administrator' ) );
			$user_roles      = ( ! is_array( $user_roles )      ? array( $user_roles )      : $user_roles );
			$skip_user_roles = ( ! is_array( $skip_user_roles ) ? array( $skip_user_roles ) : $skip_user_roles );
			$intersect       = array_intersect( $user_roles, $skip_user_roles );
			if ( ! empty( $intersect ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * save_first_activation_info
	 *
	 * Save first_login_time for now.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 *
	 * @param $code
	 * @param $user_id
	 */
	function save_first_activation_info( $code, $user_id ) {
		$this->update_activation_code_data( $user_id, $code, array( 'first_activation_time' => time() ) );
	}

	/**
	 * update_activation_code_data.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 *
	 * @param $user_id
	 * @param $activation_code
	 * @param $data
	 */
	function update_activation_code_data( $user_id, $activation_code, $data ) {
		$code_opt = ! empty( $code_opt = get_user_meta( $user_id, 'alg_wc_ev_activation_code_data', true ) ) ? $code_opt : array();
		foreach ( $data as $k => $v ) {
			$code_opt[ $activation_code ][ $k ] = $v;
		}
		update_user_meta( $user_id, 'alg_wc_ev_activation_code_data', $code_opt );
	}

	/**
	 * get_activation_code_data.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 *
	 * @param $user_id
	 * @param $activation_code
	 * @param string $key
	 *
	 * @return bool
	 */
	function get_activation_code_data( $user_id, $activation_code, $key = '' ) {
		if (
			empty( $code_opt = get_user_meta( $user_id, 'alg_wc_ev_activation_code_data', true ) )
			|| ! isset( $code_opt[ $activation_code ] )
			|| empty( $result = $code_opt[ $activation_code ] )
			|| ( ! empty( $key ) && ( ! isset( $result[ $key ] ) || empty( $result = $result[ $key ] ) ) )
		) {
			return false;
		}
		return $result;
	}

	/**
	 * verify.
	 *
	 * @version 2.0.6
	 * @since   1.6.0
	 */
	function verify() {
		if ( isset( $_GET['alg_wc_ev_verify_email'] ) ) {
			$data = json_decode( base64_decode( wc_clean( $_GET['alg_wc_ev_verify_email'] ) ), true );
			if (
				! empty( $user_id = $data['id'] )
				&& ! empty( $code = get_user_meta( $user_id, 'alg_wc_ev_activation_code', true ) )
				&& $code === $data['code']
			) {
				if ( apply_filters( 'alg_wc_ev_verify_email', true, $user_id, $code ) ) {
					update_user_meta( $user_id, 'alg_wc_ev_is_activated', '1' );
					$this->emails->maybe_send_wc_customer_new_account_email( $user_id );
					$this->save_first_activation_info( $code, $user_id );
					do_action( 'alg_wc_ev_user_account_activated', $user_id, $code );
				} else {
					do_action( 'alg_wc_ev_verify_email_error', $user_id, $code );
				}
			} else {
				wc_add_notice( $this->messages->get_failed_message( $user_id ), 'error' );
			}
		}
	}

	/**
	 * activate.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 * @todo    [next] (maybe) custom `wc_add_notice()`
	 * @todo    (maybe) rename `alg_wc_ev_activate_account_message`
	 */
	function activate() {
		if ( isset( $_GET['alg_wc_ev_activate_account_message'] ) ) {
			wc_add_notice( $this->messages->get_activation_message( intval( $_GET['alg_wc_ev_activate_account_message'] ) ) );
		}
	}

	/**
	 * resend.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 * @todo    (maybe) rename `alg_wc_ev_user_id`
	 */
	function resend() {
		if ( isset( $_GET['alg_wc_ev_user_id'] ) ) {
			$this->emails->reset_and_mail_activation_link( $_GET['alg_wc_ev_user_id'] );
			wc_add_notice( $this->messages->get_resend_message() );
		}
	}

}

endif;

return new Alg_WC_Email_Verification_Core();
