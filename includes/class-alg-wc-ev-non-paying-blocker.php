<?php
/**
 * Email Verification for WooCommerce - Non Paying Blocker.
 *
 * @version 3.2.5
 * @since   1.9.5
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

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
		 * @throws Exception
		 * @return bool
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
		 * @throws Exception
		 * @return string
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
		 * get_non_paying_user_error_message.
		 *
		 * @version 3.2.5
		 * @since   1.9.5
		 *
		 * @param   null  $user_id
		 *
		 * @return string
		 */
		function get_non_paying_user_error_message( $user_id = null ) {
			$default_notice = __( 'You need to become a paying customer in order to activate your account.', 'emails-verification-for-woocommerce' );
			$notice         = do_shortcode( apply_filters( 'alg_wc_ev_block_nonpaying_users_activation_error_notice', $default_notice ) );

			if ( $user_id ) {
				return str_replace( '%resend_verification_url%', alg_wc_ev()->core->messages->get_resend_verification_url( $user_id ), $notice );
			} else {
				return $notice;
			}
		}
	}

endif;

return new Alg_WC_Email_Verification_Non_Paying_Blocker();