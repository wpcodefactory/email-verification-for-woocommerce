<?php
/**
 * Email Verification for WooCommerce - Non Paying Blocker.
 *
 * @version 2.4.0
 * @since   1.9.5
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Non_Paying_Blocker' ) ) :

class Alg_WC_Email_Verification_Non_Paying_Blocker {

	/**
	 * Constructor.
	 *
	 * @version 2.2.4
	 * @since   1.9.5
	 */
	function __construct() {
		// Block non paying users
		add_filter( 'alg_wc_ev_verify_email', array( $this, 'prevent_non_paying_users_from_verify' ), 10, 3 );
		add_filter( 'alg_wc_ev_block_unverified_user_login_error_message', array( $this, 'replace_unverified_user_login_error_message' ), 10, 2 );
		add_action( 'alg_wc_ev_non_paying_user_blocked', array( $this, 'show_blocked_non_paying_user_error_message' ) );

		// Prevents sending activation email on user register for non paying users.
		add_filter( 'alg_wc_ev_reset_and_mail_activation_link_validation', array( $this, 'prevent_sending_activation_link_on_user_register_for_non_paying_users' ), 10, 3 );
		// Sends activation mail only on order is paid.
		add_action( 'woocommerce_order_status_changed', array( $this, 'mail_activation_link_on_paid_status' ), 11, 3 );
	}

	/**
	 * show_blocked_non_paying_user_error_message.
	 *
	 * @version 2.2.6
	 * @since   1.9.5
	 *
	 * @param $user_id
	 */
	function show_blocked_non_paying_user_error_message( $user_id ) {
		add_action( 'alg_wc_ev_verify_email_error', function ( $user_id, $args ) {
			if ( $args['directly'] ) {
				alg_wc_ev_add_notice( $this->get_non_paying_user_error_message( $user_id ), 'error' );
			}
		}, 10, 3 );
	}

	/**
	 * prevent_non_paying_users_from_verify.
	 *
	 * @version 2.2.5
	 * @since   1.9.5
	 *
	 * @param $is_valid
	 * @param $user_id
	 *
	 * @return bool
	 * @throws Exception
	 */
	function prevent_non_paying_users_from_verify( $is_valid, $user_id ) {
		if (
			'yes' === get_option( 'alg_wc_ev_block_nonpaying_users_activation', 'no' ) &&
			! alg_wc_ev_is_valid_paying_user( $user_id )
		) {
			do_action( 'alg_wc_ev_non_paying_user_blocked', $user_id );
			$is_valid = false;
		}
		return $is_valid;
	}

	/**
	 * replace_unverified_user_login_error_message.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 *
	 * @param $message
	 * @param $user
	 *
	 * @return string
	 * @throws Exception
	 */
	function replace_unverified_user_login_error_message( $message, $user ) {
		if (
			'no' === get_option( 'alg_wc_ev_block_nonpaying_users_activation', 'no' )
			|| empty( $customer = new \WC_Customer( $user->ID ) )
			|| alg_wc_ev_is_valid_paying_user( $user->ID )
		) {
			return $message;
		}
		return $this->get_non_paying_user_error_message( $user->ID );
	}

	/**
	 * prevent_sending_activation_link_on_user_register_for_non_paying_users.
	 *
	 * @version 2.4.0
	 * @since   1.9.5
	 *
	 * @param $can_send
	 * @param $user_id
	 * @param $current_hook
	 *
	 * @return bool
	 */
	function prevent_sending_activation_link_on_user_register_for_non_paying_users( $can_send, $user_id, $current_hook ) {
		if (
			'yes' === get_option( 'alg_wc_ev_block_nonpaying_users_activation', 'no' ) &&
			'yes' === get_option( 'alg_wc_ev_block_nonpaying_users_activation_email_on_payment', 'no' ) &&
			( 'user_register' === $current_hook || 'woocommerce_created_customer' == $current_hook ) &&
			! empty( $user = get_user_by( 'id', $user_id ) ) &&
			( empty( $role_checking = get_option( 'alg_wc_ev_block_nonpaying_users_activation_role', array( 'customer' ) ) ) || count( array_intersect( $role_checking, $user->roles ) ) > 0 )
		) {
			$code = alg_wc_ev_generate_user_code();
			alg_wc_ev()->core->emails->update_all_user_meta( $user_id, $code );
			$can_send = false;
		}
		return $can_send;
	}

	/**
	 * mail_activation_link_on_paid_status.
	 *
	 * @version 2.2.4
	 * @since   2.2.4
	 *
	 * @param $order_id
	 * @param $from
	 * @param $to
	 */
	function mail_activation_link_on_paid_status( $order_id, $from, $to ) {
		if (
			'yes' === get_option( 'alg_wc_ev_block_nonpaying_users_activation', 'no' ) &&
			'yes' === get_option( 'alg_wc_ev_block_nonpaying_users_activation_email_on_payment', 'no' ) &&
			! empty( $order = wc_get_order( $order_id ) ) &&
			! empty( $order->get_subtotal() ) &&
			! empty( $customer_id = $order->get_customer_id() ) &&
			! empty( $user = get_user_by( 'id', $customer_id ) ) &&
			( empty( $role_checking = get_option( 'alg_wc_ev_block_nonpaying_users_activation_role', array( 'customer' ) ) ) || count( array_intersect( $role_checking, $user->roles ) ) > 0 ) &&
			! empty( $statuses = wc_get_is_paid_statuses() ) &&
			in_array( $to, $statuses ) &&
			! alg_wc_ev_is_user_verified_by_user_id( $customer_id )
		) {
			alg_wc_ev()->core->emails->reset_and_mail_activation_link( $customer_id );
		}
	}

	/**
	 * get_non_paying_user_error_message.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 *
	 * @param null $user_id
	 *
	 * @return string
	 */
	function get_non_paying_user_error_message( $user_id = null ) {
		$notice = do_shortcode( get_option( 'alg_wc_ev_block_nonpaying_users_activation_error_notice',
			__( 'You need to become a paying customer in order to activate your account.', 'emails-verification-for-woocommerce' ) ) );

		if ( $user_id ) {
			return str_replace( '%resend_verification_url%', alg_wc_ev()->core->messages->get_resend_verification_url( $user_id ), $notice );
		} else {
			return $notice;
		}
	}
}

endif;

return new Alg_WC_Email_Verification_Non_Paying_Blocker();