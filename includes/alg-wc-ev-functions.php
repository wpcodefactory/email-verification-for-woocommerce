<?php
/**
 * Email Verification for WooCommerce - Functions
 *
 * @version 1.9.5
 * @since   1.9.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'alg_wc_ev_is_user_verified_by_user_id' ) ) {
	/**
	 * alg_wc_ev_is_user_verified_by_user_id.
	 *
	 * @version 1.9.0
	 * @since   1.9.0
	 */
	function alg_wc_ev_is_user_verified_by_user_id( $user_id = false, $is_guest_verified = false ) {
		return ( function_exists( 'alg_wc_ev' ) ? alg_wc_ev()->core->is_user_verified_by_user_id( $user_id, $is_guest_verified ) : null );
	}
}

if ( ! function_exists( 'alg_wc_ev_is_user_verified' ) ) {
	/**
	 * alg_wc_ev_is_user_verified.
	 *
	 * @version 1.9.0
	 * @since   1.9.0
	 * @todo    allow `$user = false` as default param (i.e. try `get_current_user()` then)
	 */
	function alg_wc_ev_is_user_verified( $user, $is_guest_verified = false ) {
		return ( function_exists( 'alg_wc_ev' ) ? alg_wc_ev()->core->is_user_verified( $user, $is_guest_verified ) : null );
	}
}

if ( ! function_exists( 'alg_wc_ev_is_valid_paying_user' ) ) {
	/**
	 * is_user_valid_non_paying.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 *
	 * @param $user_id
	 *
	 * @return bool
	 * @throws Exception
	 *
	 * @todo Maybe create an option or a filter to change if the function should check if user is already verified or not
	 */
	function alg_wc_ev_is_valid_paying_user( $user_id ) {
		if (
			alg_wc_ev()->core->is_user_verified_by_user_id( $user_id )
			|| 'no' === get_option( 'alg_wc_ev_block_nonpaying_users_activation', 'no' )
			|| empty( $user = get_user_by( 'id', $user_id ) )
			|| empty( $customer = new \WC_Customer( $user_id ) )
			|| (
				! empty( $role_checking = get_option( 'alg_wc_ev_block_nonpaying_users_activation_role', array('customer') ) ) && count( array_intersect( $role_checking, $user->roles ) ) == 0
			)
			|| $customer->get_is_paying_customer()
		) {
			return true;
		}
		return false;
	}
}