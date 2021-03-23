<?php
/**
 * Email Verification for WooCommerce - Functions
 *
 * @version 2.0.7
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
	 * alg_wc_ev_is_valid_paying_user.
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

if ( ! function_exists( 'alg_wc_ev_get_expiration_time' ) ) {
	/**
	 * alg_wc_ev_get_expiration_time.
	 *
	 * @version 1.9.8
	 * @since   1.9.8
	 *
	 * @return float|int
	 */
	function alg_wc_ev_get_expiration_time() {
		$unit_constants   = array(
			'seconds' => 1,
			'days'    => DAY_IN_SECONDS,
		);
		$expire_time_opt  = get_option( 'alg_wc_ev_expiration_time', 0 );
		$expire_time_unit = get_option( 'alg_wc_ev_expiration_time_unit', 'seconds' );
		if ( empty( $expire_time_opt ) ) {
			return 0;
		} else {
			return $expire_time_opt * $unit_constants[ $expire_time_unit ];
		}
	}
}

if ( ! function_exists( 'alg_wc_ev_array_to_string' ) ) {
	/**
	 * converts array to string.
	 *
	 * @version 2.0.7
	 * @since   2.0.7
	 *
	 * @param $arr
	 * @param array $args
	 *
	 * @return string
	 */
	function alg_wc_ev_array_to_string( $arr, $args = array() ) {
		$args            = wp_parse_args( $args, array(
			'glue'          => ', ',
			'item_template' => '{value}' //  {key} and {value} allowed
		) );
		$transformed_arr = array_map( function ( $key, $value ) use ( $args ) {
			$item = str_replace( array( '{key}', '{value}' ), array( $key, $value ), $args['item_template'] );
			return $item;
		}, array_keys( $arr ), $arr );
		return implode( $args['glue'], $transformed_arr );
	}
}