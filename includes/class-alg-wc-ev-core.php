<?php
/**
 * Email Verification for WooCommerce - Core Class.
 *
 * @version 2.3.8
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Core' ) ) :

class Alg_WC_Email_Verification_Core {

	/**
	 * @var Alg_WC_Email_Verification_Emails
	 */
	public $emails;

	/**
	 * success_message_displayed.
	 *
	 * @since   2.1.4
	 *
	 * @var bool
	 */
	protected $success_message_displayed = false;

	/**
	 * Constructor.
	 *
	 * @version 2.3.7
	 * @since   1.0.0
	 * @todo    [next] (maybe) `[alg_wc_ev_translate]` to description in readme.txt
	 */
	function __construct() {
		// Functions
		require_once( 'alg-wc-ev-functions.php' );
		// Verification actions
		add_action( 'init', array( $this, 'verify' ),   PHP_INT_MAX );
		add_action( 'wp', array( $this, 'activate_message' ), PHP_INT_MAX );
		add_action( 'init', array( $this, 'resend' ),   PHP_INT_MAX );
		// Verification info widget
		require_once( 'class-alg-wc-ev-verification-info-widget.php' );
		// Prevent login
		require_once( 'class-alg-wc-ev-logouts.php' );
		// Emails
		$this->emails = require_once( 'class-alg-wc-ev-emails.php' );
		// Messages
		$this->messages = require_once( 'class-alg-wc-ev-messages.php' );
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
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'login_user_automatically_on_success_activation' ), 10, 2 );
		// Redirect on success activation
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'redirect_on_success_activation' ), 100, 2 );
		// Success activation message
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'maybe_display_success_activation_message_via_hook' ), 10, 2 );
		add_action( 'init', array( $this, 'maybe_display_success_activation_message_via_query_string' ) );
		add_filter( 'wp_redirect', array( $this, 'remove_success_activation_message' ) );
		// Error message
		add_action( 'init', array( $this, 'display_error_activation_message' ) );
		// Redirects on failure
		add_action( 'wp_login_failed', array( $this, 'redirect_on_failure' ), 10, 2 );
		// Add verification info to my account page
		add_action( 'woocommerce_account_dashboard', array( $this, 'add_verification_info_to_my_account_page' ) );
		// Verification info widget
		add_action( 'widgets_init', array( $this, 'add_verification_info_widget' ) );
		// Blocks content for unverified users
		add_action( 'template_redirect', array( $this, 'block_pages_for_unverified_users' ) );
		add_action( 'init', array( $this, 'show_blocked_content_notice' ) );
		add_action( 'wp', array( $this, 'redirect_to_resend_verification_url' ) );
		add_action( 'wp', array( $this, 'save_my_account_page_referer_url' ) );
		$this->handle_shortcodes();
	}

	/**
	 * save_my_account_page_referer_url.
	 *
	 * @version 2.3.7
	 * @since   2.3.5
	 */
	function save_my_account_page_referer_url() {
		if (
			! is_admin() &&
			! wp_doing_ajax() &&
			! wp_doing_cron() &&
			! is_user_logged_in() &&
			'my_account_referer' === get_option( 'alg_wc_ev_redirect_to_my_account_on_success', 'yes' ) &&
			get_queried_object_id() === ( $my_account_id = wc_get_page_id( 'myaccount' ) ) &&
			! empty( $referer_url = wp_get_referer() ) &&
			$referer_url !== get_permalink( $my_account_id )
		) {
			wc_setcookie( 'alg_wc_ev_my_account_referer_url', $referer_url );
		}
	}
	
	/**
	 * block_pages_for_unverified_users.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 */
	function block_pages_for_unverified_users() {
		if (
			! is_admin()
			&& ! empty( $blocked_pages = get_option( 'alg_wc_ev_blocked_pages', array() ) )
			&& is_page( $blocked_pages )
			&&
			(
				! is_user_logged_in()
				|| ! alg_wc_ev_is_user_verified_by_user_id( get_current_user_id() )
			)
		) {
			$redirect_url = add_query_arg( array(
				'alg_wc_ev_blocked_content' => true
			), get_option( 'alg_wc_ev_block_content_redirect', home_url() ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * show_blocked_content_notice.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 */
	function show_blocked_content_notice() {
		if ( isset( $_GET['alg_wc_ev_blocked_content'] ) ) {
			$error_msg_options = array(
				'alg_wc_ev_block_content_notice_guests' => __( 'You need to verify your account to access this content.', 'emails-verification-for-woocommerce' ),
				'alg_wc_ev_block_content_notice'        => __( 'You need to verify your account to access this content.', 'emails-verification-for-woocommerce' ) . ' ' . __( 'You can resend the email with verification link by clicking <a href="%resend_verification_url%">here</a>.', 'emails-verification-for-woocommerce' )
			);
			$error_msg_option  = ! is_user_logged_in() ? 'alg_wc_ev_block_content_notice_guests' : 'alg_wc_ev_block_content_notice';
			$msg               = get_option( $error_msg_option, $error_msg_options[ $error_msg_option ] );
			$replace           = array(
				'%myaccount_url%' => wc_get_page_permalink( 'myaccount' )
			);
			if ( is_user_logged_in() ) {
				$replace['%resend_verification_url%'] = alg_wc_ev()->core->messages->get_resend_verification_url( get_current_user_id() );
			}
			$msg = str_replace( array_keys( $replace ), $replace, $msg );
			if ( ! empty( $msg ) ) {
				alg_wc_ev_add_notice( $msg );
			}
		}
	}

	/**
	 * add_verification_info_widget.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 */
	function add_verification_info_widget() {
		if ( 'yes' === get_option( 'alg_wc_ev_verification_info_widget', 'no' ) ) {
			register_widget( 'Alg_WC_Email_Verification_Info_Widget' );
		}
	}

	/**
	 * handle_shortcodes.
	 *
	 * @version 2.3.5
	 * @since   2.1.1
	 */
	function handle_shortcodes(){
		// Language translate shortcode
		add_shortcode( 'alg_wc_ev_translate', array( $this, 'language_shortcode' ) );
		// Verification status shortcode
		add_shortcode( 'alg_wc_ev_verification_status', array( $this, 'alg_wc_ev_verification_status' ) );
		// Resend verification url shortcode
		add_shortcode( 'alg_wc_ev_resend_verification_url', array( $this, 'alg_wc_ev_resend_verification_url' ) );
		// Display new user information
		add_shortcode( 'alg_wc_ev_new_user_info', array( $this, 'alg_wc_ev_new_user_info' ) );
		// Resend verification form
		add_shortcode( 'alg_wc_ev_resend_verification_form', array( $this, 'alg_wc_ev_resend_verification_form' ) );
	}

	/**
	 * add_verification_status_to_my_account_page.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 */
	function add_verification_info_to_my_account_page() {
		if ( 'yes' === get_option( 'alg_wc_ev_verification_info_my_account', 'no' ) ) {
			echo do_shortcode( get_option( 'alg_wc_ev_verification_info_customization', $this->get_verification_info_default() ) );
		}
	}

	/**
	 * get_verification_info_default.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	function get_verification_info_default( $args = null ) {
		$args   = wp_parse_args( $args, array(
			'tabs_to_remove' => 3
		) );
		$option =
			'<div class="alg-wc-ev-verification-info">
				[alg_wc_ev_verification_status content_template="' . __( 'Verification status: ', 'emails-verification-for-woocommerce' ) . '{verification_status}' . '"]
			</div>';
		if ( is_int( $args['tabs_to_remove'] ) && $args['tabs_to_remove'] > 0 ) {
			$option = preg_replace( '/\t{' . $args['tabs_to_remove'] . '}/', '', $option );
		}
		return $option;
	}

	/**
	 * display_error_activation_message.
	 *
	 * @version 2.3.7
	 * @since   2.1.0
	 */
	function display_error_activation_message() {
		if (
			isset( $_GET['alg_wc_ev_email_verified_error'] )
			&& ! empty( $user_id = $_GET['alg_wc_ev_email_verified_error'] )
			&& ! empty( $user = get_user_by( 'ID', $user_id ) )
		) {
			$message = apply_filters( 'alg_wc_ev_block_unverified_user_login_error_message', alg_wc_ev()->core->messages->get_error_message( $user->ID ), $user );
			alg_wc_ev_add_notice( $message );
		}

		if (
			isset( $_GET['alg_wc_ev_resend_status_code'] )
			&& ! empty( $resend_status_code = sanitize_text_field( $_GET['alg_wc_ev_resend_status_code'] ) )
			&& $resend_status_code != '1'
		) {
			$resend_message         = alg_wc_ev()->core->messages->get_resend_message( $resend_status_code );
			$resend_message_string  = isset( $resend_message[ 'msg' ] ) ? sanitize_text_field( $resend_message[ 'msg' ] ) : '';
			$resend_message_type    = isset( $resend_message[ 'type' ] ) ? sanitize_text_field( $resend_message[ 'type' ] ) : '';

			alg_wc_ev_add_notice( $resend_message_string, $resend_message_type );
		}
	}

	/**
	 * redirect_on_failure.
	 *
	 * @version 2.2.2
	 * @since   2.1.0
	 *
	 * @param $username
	 */
	function redirect_on_failure( $username ) {
		if (
			'yes' === get_option( 'alg_wc_ev_redirect_on_failure', 'no' ) &&
			2 == func_num_args() &&
			! empty( $error = func_get_arg( 1 ) )
			&& in_array( 'alg_wc_ev_email_verified_error', $error->get_error_codes() )
		) {
			$user = get_user_by( 'email', $username );
			if ( ! $user ) {
				$user = get_user_by( 'login', $username );
			}
			if ( $user ) {
				wp_redirect( add_query_arg( array(
					'alg_wc_ev_email_verified_error' => $user->ID
				), get_option( 'alg_wc_ev_redirect_on_failure_url', '' ) ) );
				exit;
			}
		}
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
	 * @version 2.2.6
	 * @since   2.0.0
	 *
	 * @param $user_id
	 */
	function login_user_automatically_on_success_activation( $user_id, $args ) {
		if (
			'yes' === get_option( 'alg_wc_ev_login_automatically_on_activation', 'yes' ) &&
			$args['directly']
		) {
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id );
		}
	}

	/**
	 * maybe_display_success_activation_message_via_query_string.
	 *
	 * @version 2.1.4
	 * @since   2.1.4
	 */
	function maybe_display_success_activation_message_via_query_string() {
		if ( isset( $_GET['alg_wc_ev_success_activation_message'] ) ) {
			$this->output_success_activation_message();
		}
	}

	/**
	 * maybe_display_success_activation_message.
	 *
	 * @version 2.2.6
	 * @since   2.0.0
	 */
	function maybe_display_success_activation_message_via_hook( $user_id, $args ) {
		if ( $args['directly'] ) {
			$this->output_success_activation_message();
		}
	}

	/**
	 * output_success_activation_message.
	 *
	 * @version 2.1.4
	 * @since   2.1.4
	 */
	function output_success_activation_message() {
		if ( ! $this->success_message_displayed ) {
			alg_wc_ev_add_notice( $this->messages->get_success_message() );
			$this->success_message_displayed = true;
		}
	}

	/**
	 * redirect_on_success_activation.
	 *
	 * @version 2.3.7
	 * @since   2.0.0
	 *
	 */
	function redirect_on_success_activation( $user_id, $args ) {
		$args = wp_parse_args( $args, array(
			'directly' => true
		) );
		if ( $args['directly'] ) {
			$redirect_url = false !== ( $url = $this->get_redirect_url_on_success_activation( $args ) ) ? $url : '';
			$redirect_url = empty( $referer_url = get_user_meta( $user_id, 'alg_wc_ev_my_account_referer_url', true ) ) ? $redirect_url : $referer_url;
			$redirect_url = add_query_arg( array( 'alg_wc_ev_success_activation_message' => 1 ), $redirect_url );
			$redirect_url = remove_query_arg( 'alg_wc_ev_verify_email', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * get_redirect_url_on_success_activation.
	 *
	 * @version 2.3.5
	 * @since   2.2.8
	 *
	 * @param null $args
	 *
	 * @return bool|string
	 */
	function get_redirect_url_on_success_activation( $args = null ) {
		$args         = wp_parse_args( $args, array(
			'directly' => true
		) );
		$redirect_url = false;
		if (
			'no' !== ( $redirect = get_option( 'alg_wc_ev_redirect_to_my_account_on_success', 'yes' ) ) &&
			$args['directly']
		) {
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
				case 'my_account_referer':
					if (
						isset( $_COOKIE['alg_wc_ev_my_account_referer_url'] ) &&
						! empty( $referer_cookie = $_COOKIE['alg_wc_ev_my_account_referer_url'] ) &&
						! is_admin()
					) {
						wc_setcookie( 'alg_wc_ev_my_account_referer_url', '',1 );
						$redirect_url = $referer_cookie;
					}
					break;
				default: // 'yes'
					$redirect_url = wc_get_page_permalink( 'myaccount' );
			}
			$redirect_url = add_query_arg( array( 'alg_wc_ev_success_activation_message' => 1 ), $redirect_url );
		}
		return $redirect_url;
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
	 * @version 2.1.1
	 * @since   1.1.0
	 */
	function is_user_verified( $user, $is_guest_verified = false ) {
		if ( ! $user || is_wp_error( $user ) || 0 == $user->ID || empty( $user->roles ) ) {
			return apply_filters( 'alg_wc_ev_is_user_verified', $is_guest_verified, null );
		}
		$do_verify_already_registered = ( 'yes' === get_option( 'alg_wc_ev_verify_already_registered', 'no' ) );
		$is_user_email_activated      = get_user_meta( $user->ID, 'alg_wc_ev_is_activated', true );
		if (
			(
				( $do_verify_already_registered && ! $is_user_email_activated ) ||
				( ! $do_verify_already_registered && '0' === $is_user_email_activated )
			) &&
			! $this->is_user_role_skipped( $user )
		) {
			return apply_filters( 'alg_wc_ev_is_user_verified', false, $user->ID );
		}
		return apply_filters( 'alg_wc_ev_is_user_verified', true, $user->ID );
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
	 * save_activation_info
	 *
	 * Save first_login_time for now.
	 *
	 * @version 2.1.4
	 * @since   1.9.5
	 *
	 * @param $code
	 * @param $user_id
	 */
	function save_activation_info( $code, $user_id ) {
		$this->update_activation_code_data( $user_id, $code, array( 'activation_time' => time() ) );
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
	 * @version 2.2.6
	 * @since   1.6.0
	 *
	 * @param null $args
	 *
	 * @return bool
	 */
	function verify( $args = null ) {
		$args = wp_parse_args( $args, array(
			'verify_code' => isset( $_GET['alg_wc_ev_verify_email'] ) ? $_GET['alg_wc_ev_verify_email'] : '',
			'directly'    => true
		) );
		if (
			! empty( $args['verify_code'] ) &&
			! empty( $verify_code = wc_clean( $args['verify_code'] ) ) &&
			! empty( $data = json_decode( alg_wc_ev()->core->base64_url_decode( $verify_code ), true ) )
		) {
			if (
				! empty( $user_id = intval( $data['id'] ) ) &&
				! empty( $code = get_user_meta( $user_id, 'alg_wc_ev_activation_code', true ) ) &&
				$code === $data['code'] &&
				! alg_wc_ev_is_user_verified_by_user_id( $user_id )
			) {
				if ( apply_filters( 'alg_wc_ev_verify_email', true, $user_id, $code, $args ) ) {
					$this->activate_user( array(
						'user_id'     => $user_id,
						'code'        => $code,
						'directly'    => $args['directly'],
						'verify_args' => $args
					) );
					return true;
				} else {
					do_action( 'alg_wc_ev_verify_email_error', $user_id, $args );
					return false;
				}
			} else {
				if ( $args['directly'] ) {
					alg_wc_ev_add_notice( $this->messages->get_failed_message( $user_id ), 'error', $args );
				}
				return false;
			}
		}
		return false;
	}

	/**
	 * activate_user.
	 *
	 * @version 2.3.7
	 * @since   2.2.6
	 *
	 * @param null $args
	 */
	function activate_user( $args = null ) {
		$args = wp_parse_args( $args, array(
			'user_id'     => '',
			'code'        => '',
			'directly'    => true, // Should be false when the user account is activated indirectly, like if the user is auto activated after its order is paid. Should be true when user account is directly activated, like if the user has accessed the activation link.
			'verify_args' => array()
		) );
		$user_id = $args['user_id'];
		$code    = $args['code'];
		update_user_meta( $user_id, 'alg_wc_ev_is_activated', '1' );
		if ( ! empty( $code ) ) {
			$this->save_activation_info( $code, $user_id );
		}
		// update redirect url from cookie into meta
		if( isset( $_COOKIE['alg_wc_ev_redirect_referer_url'] ) && ! empty( $_COOKIE['alg_wc_ev_redirect_referer_url'] ) ) {
			update_user_meta( $user_id, 'alg_wc_ev_redirect_referer_url', sanitize_url( $_COOKIE['alg_wc_ev_redirect_referer_url'] ) );
		}
		do_action( 'alg_wc_ev_user_account_activated', $user_id, $args );
	}

	/**
	 * Deactivate or Unverify user.
	 *
	 * @version 2.3.8
	 * @since   2.3.8
	 *
	 * @param null $args
	 */
	function deactivate_user( $args = array() ) {
		$args       = wp_parse_args( $args, array(
			'user_id' => '',
		) );
		$user_id    = $args['user_id'];

		update_user_meta( $user_id, 'alg_wc_ev_is_activated', '0' );
		delete_user_meta( $user_id, 'alg_wc_ev_customer_new_account_email_sent' );
		delete_user_meta( $user_id, 'alg_wc_ev_admin_email_sent' );

		do_action( 'alg_wc_ev_user_account_deactivated', $user_id, $args );
	}

	/**
	 * activate.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 * @todo    [next] (maybe) custom `alg_wc_ev_add_notice()`
	 * @todo    (maybe) rename `alg_wc_ev_activate_account_message`
	 */
	function activate_message() {
		if ( isset( $_GET['alg_wc_ev_activate_account_message'] ) ) {
			alg_wc_ev_add_notice( $this->messages->get_activation_message( intval( $_GET['alg_wc_ev_activate_account_message'] ) ) );
		}
	}

	/**
	 * resend.
	 *
	 * @version 2.1.7
	 * @since   1.6.0
	 * @todo    (maybe) rename `alg_wc_ev_user_id`
	 */
	function resend() {
		if (
			isset( $_GET['alg_wc_ev_user_id'] ) &&
			(
				(
					( $nonce_required = true ) &&
					! empty( $resend_timestamp = get_user_meta( $_GET['alg_wc_ev_user_id'], 'alg_wc_ev_activation_email_sent', true ) ) &&
					isset( $_GET['alg_wc_ev_nonce'] ) && wp_verify_nonce( $_GET['alg_wc_ev_nonce'], 'resend-' . $_GET['alg_wc_ev_user_id'] . '-' . $resend_timestamp )
				) ||
				! $nonce_required
			)
		) {
			$this->emails->reset_and_mail_activation_link( $_GET['alg_wc_ev_user_id'] );
			alg_wc_ev_add_notice( $this->messages->get_resend_message()['msg'] );
		}
	}


	/**
	 * Receive email address and send verification email from verification form.
	 *
	 * @version 2.3.7
	 * @since   2.3.5
	 */
	function redirect_to_resend_verification_url() {
		// Check if the nonce is okay.
		if( isset( $_POST['alg_wc_ev_nonce'] ) && wp_verify_nonce( $_POST['alg_wc_ev_nonce'], 'alg_wc_ev_resend_verification_form_nonce' ) ) {

			global $wp;

			$email_address  = isset( $_POST['email_address'] ) ? sanitize_email( $_POST['email_address'] ) : '';
			$user_to_verify = get_user_by( 'email', $email_address );
			$current_url    = site_url( $wp->request );

			// If no user found with this email.
			if( ! $user_to_verify instanceof WP_User ) {
				wp_safe_redirect( add_query_arg( array( 'alg_wc_ev_resend_status_code' => 2 ), $current_url ) );
				exit;
			}

			// If the user is already verified.
			if( alg_wc_ev_is_user_verified_by_user_id( $user_to_verify->ID ) ) {
				wp_safe_redirect( add_query_arg( array( 'alg_wc_ev_resend_status_code' => 3 ), $current_url ) );
				exit;
			}

			// Ready to resend verification email to this user.
			wp_safe_redirect( alg_wc_ev()->core->messages->get_resend_verification_url( $user_to_verify->ID, array( 'alg_wc_ev_resend_status_code' => 1 ) ) );
			exit;
		}
	}

	/**
	 * Return resend verification form.
	 *
	 * @version 2.3.7
	 * @since   2.3.5
	 *
	 * @param null $atts
	 *
	 * @return string
	 */
	function alg_wc_ev_resend_verification_form( $atts = null ) {

		$atts                               = shortcode_atts( array(
			'email_address'             => true,
			'show_to_logged_in_users'   => false,
			'template'                  => '<span>{user_email_input}</span><span>{resend_verification_btn}</span>',
			'submit_btn_template'       => '<button type="submit">'. esc_html__('Submit', 'emails-verification-for-woocommerce' ) .'</button>',
			'wrapper_template'          => '<form class="alg-wc-ev-resend-verification-form" method="post" action="">{nonce_field}{template}</form>',
		), $atts, 'alg_wc_ev_resend_verification_form' );
		$atts['show_to_logged_in_users']    = filter_var( $atts['show_to_logged_in_users'], FILTER_VALIDATE_BOOLEAN );

		if( ( is_user_logged_in() && ! $atts['show_to_logged_in_users'] ) ) {
			return '';
		}

		$email_address          = isset( $atts['email_address'] ) ? sanitize_email( $atts['email_address'] ) : '';
		$form_template          = array(
			'{user_email_input}'          => sprintf('<input type="email" name="email_address" value="%s" placeholder="%s" required>', $email_address, esc_html__('Email address', 'emails-verification-for-woocommerce' ) ),
			'{resend_verification_btn}'   => $atts['submit_btn_template'],
		);
		$nonce_field            = wp_nonce_field('alg_wc_ev_resend_verification_form_nonce', 'alg_wc_ev_nonce', false );
		$content                = str_replace( array_keys( $form_template ), $form_template, $atts['template'] );
		$output                 = str_replace( array( '{template}', '{nonce_field}' ), array( $content, $nonce_field ), $atts['wrapper_template'] );
		$allowed_html           = wp_kses_allowed_html( 'post' );
		$allowed_attributes     = array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'type'  => array(),
		);
		$allowed_html['input']  = $allowed_attributes;
		$allowed_html['form']   = array_merge( $allowed_attributes, array(
			'method' => array(),
			'action' => array(),
		) );

		return wp_kses( $output, $allowed_html );
	}

	/**
	 * Display user data.
	 *
	 * @version 2.3.5
	 * @since   2.3.5
	 *
	 * @param null $atts
	 *
	 * @return string
	 */
	function alg_wc_ev_new_user_info( $atts = null ) {
		$atts        = shortcode_atts( array(
			'info_type'     => 'user_email',
			'not_found_msg' => '',
		), $atts, 'alg_wc_ev_new_user_info' );
		$info_type   = isset( $atts['info_type'] ) ? $atts['info_type'] : '';
		$new_user_id = isset( $_GET['alg_wc_ev_activate_account_message'] ) ? sanitize_text_field( $_GET['alg_wc_ev_activate_account_message'] ) : '';
		$new_user    = get_user_by( 'ID', $new_user_id );

		if ( $new_user instanceof WP_User ) {
			return isset( $new_user->{$info_type} ) ? $new_user->{$info_type} : __( 'User data not found.', 'emails-verification-for-woocommerce' );
		}

		return esc_html( $atts['not_found_msg'] );
	}

	/**
	 * alg_wc_ev_resend_verification_url.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 */
	function alg_wc_ev_resend_verification_url( $atts = null ) {
		$atts                     = shortcode_atts( array(
			'wrapper_template' => '<div class="alg-wc-ev-resend-verification-url">{content_template}</div>',
			'hide_if_verified' => true,
			'content_template' => __( 'You can resend the email with verification link by clicking <a href="{resend_verification_url}">here</a>.', 'emails-verification-for-woocommerce' ),
		), $atts, 'alg_wc_ev_verification_status' );
		$atts['hide_if_verified'] = filter_var( $atts['hide_if_verified'], FILTER_VALIDATE_BOOLEAN );
		$atts['hide_for_guests']  = filter_var( $atts['hide_for_guests'], FILTER_VALIDATE_BOOLEAN );
		if (
			( ! is_user_logged_in() && $atts['hide_for_guests'] )
			|| ( ( $is_user_verified = alg_wc_ev_is_user_verified_by_user_id( get_current_user_id() ) ) && $atts['hide_if_verified'] )
		) {
			return '';
		}
		$from_to = array(
			'{resend_verification_url}' => esc_url( alg_wc_ev()->core->messages->get_resend_verification_url( get_current_user_id() ) ),
		);
		$content = str_replace( array_keys( $from_to ), $from_to, $atts['content_template'] );
		$output  = str_replace( '{content_template}', $content, $atts['wrapper_template'] );
		return wp_kses_post( $output );
	}

	/**
	 * alg_wc_ev_verification_status.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @param null $atts
	 *
	 * @return string
	 */
	function alg_wc_ev_verification_status( $atts = null ) {
		$atts                     = shortcode_atts( array(
			'wrapper_template'  => '<div class="alg-wc-ev-verification-status">{content_template}</div>',
			'content_template'  => __( 'Verification status: ', 'emails-verification-for-woocommerce' ) . '{verification_status}',
			'verified_status'   => '<strong>' . __( 'Verified', 'emails-verification-for-woocommerce' ) . '</strong>',
			'unverified_status' => '<strong>' . __( 'Unverified', 'emails-verification-for-woocommerce' ) . '</strong>',
			'hide_if_verified'  => false,
			'hide_for_guests'   => false
		), $atts, 'alg_wc_ev_verification_status' );
		$atts['hide_if_verified'] = filter_var( $atts['hide_if_verified'], FILTER_VALIDATE_BOOLEAN );
		$atts['hide_for_guests']  = filter_var( $atts['hide_for_guests'], FILTER_VALIDATE_BOOLEAN );
		if (
			( ! is_user_logged_in() && $atts['hide_for_guests'] )
			|| ( ( $is_user_verified = alg_wc_ev_is_user_verified_by_user_id( get_current_user_id() ) ) && $atts['hide_if_verified'] )
		) {
			return '';
		}
		$user    = wp_get_current_user();
		$from_to = array(
			'{verification_status}' => $is_user_verified ? $atts['verified_status'] : $atts['unverified_status'],
			'{user_display_name}'   => $user->display_name,
			'{user_nicename}'       => $user->user_nicename,
		);
		$content = str_replace( array_keys( $from_to ), $from_to, $atts['content_template'] );
		$output  = str_replace( '{content_template}', $content, $atts['wrapper_template'] );
		return wp_kses_post( $output );
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
	 * base64_url_encode.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @param $input
	 *
	 * @return string
	 */
	function base64_url_encode( $input ) {
		return strtr( base64_encode( $input ), '+/=', '._-' );
	}

	/**
	 * base64_url_encode.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @param $input
	 *
	 * @return bool|string
	 */
	function base64_url_decode( $input ) {
		return base64_decode( strtr( $input, '._-', '+/=' ) );
	}

}

endif;

return new Alg_WC_Email_Verification_Core();
