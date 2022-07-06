<?php
/**
 * Email Verification for WooCommerce - Functions.
 *
 * @version 2.3.8
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

if ( ! function_exists( 'alg_wc_ev_add_notice' ) ) {
	/**
	 * alg_wc_ev_add_notice.
	 *
	 * @version 2.3.5
	 * @since   2.0.9
	 *
	 * @param $message
	 * @param string $notice_type
	 * @param array $data
	 * @param null $args
	 */
	function alg_wc_ev_add_notice( $message, $notice_type = 'success', $data = array(), $args = null ) {
		$args = wp_parse_args( $args, array(
			'clear_previous_messages' => 'yes' === get_option( 'alg_wc_ev_clear_previous_messages', 'no' ),
			'check_previous_messages' => true
		) );
		$clear_previous_messages = $args['clear_previous_messages'];
		if ( $clear_previous_messages ) {
			wc_clear_notices();
		}
		if (
			! $args['check_previous_messages'] ||
			( function_exists( 'wc_has_notice' ) && ! wc_has_notice( $message, $notice_type ) )
		) {
			wc_add_notice( $message, $notice_type, $data );
		}
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
	 * @version 2.2.4
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
			alg_wc_ev()->core->is_user_verified_by_user_id( $user_id ) ||
			(
				! empty( $user = get_user_by( 'id', $user_id ) ) &&
				! empty( $customer = new \WC_Customer( $user_id ) ) &&
				( empty( $role_checking = get_option( 'alg_wc_ev_block_nonpaying_users_activation_role', array( 'customer' ) ) ) || count( array_intersect( $role_checking, $user->roles ) ) > 0 ) &&
				$customer->get_is_paying_customer()
			)
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

if ( ! function_exists( 'alg_wc_ev_get_user_placeholders' ) ) {
	/**
	 * converts array to string.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function alg_wc_ev_get_user_placeholders( $args ) {
		$args         = wp_parse_args( $args, array(
			'user_id' => '',
			'user'    => '',
		) );
		$args         = apply_filters( 'alg_wc_ev_user_placeholders_args', $args );
		$user_id      = intval( $args['user_id'] );
		$user         = ! empty( $args['user'] ) && is_a( $args['user'], 'WP_User' ) ? $args['user'] : new WP_User( $user_id );
		$placeholders = array(
			'%user_id%'                => $user_id,
			'%user_login%'             => $user->user_login,
			'%user_nicename%'          => $user->user_nicename,
			'%user_email%'             => $user->user_email,
			'%user_url%'               => $user->user_url,
			'%user_registered%'        => $user->user_registered,
			'%user_display_name%'      => $user->display_name,
			'%user_roles%'             => implode( ', ', $user->roles ),
			'%user_first_name%'        => $user->first_name,
			'%user_last_name%'         => $user->last_name,
			'%admin_user_profile_url%' => admin_url( 'user-edit.php?user_id=' . $user_id ),
		);
		return apply_filters( 'alg_wc_ev_user_placeholders_args', $placeholders, $args );
	}
}

if ( ! function_exists( 'alg_wc_ev_get_default_session_start_params' ) ) {
	/**
	 * alg_wc_ev_get_session_start_default_params.
	 *
	 * @version 2.3.4
	 * @since   2.3.4
	 *
	 * @return array
	 */
	function alg_wc_ev_get_default_session_start_params() {
		return array(
			'cache_limiter'  => 'private',
			'read_and_close' => true,
		);
	}
}

if ( ! function_exists( 'alg_wc_ev_get_session_start_params_option' ) ) {
	/**
	 * get_session_start_params_option.
	 *
	 * @version 2.3.4
	 * @since   2.3.4
	 *
	 * @return array
	 */
	function alg_wc_ev_get_session_start_params_option() {
		return apply_filters( 'alg_wc_ev_session_start_params', json_decode( get_option( 'alg_wc_ev_session_start_params', wp_json_encode( alg_wc_ev_get_default_session_start_params() ) ), true ) );
	}
}

if ( ! function_exists( 'alg_wc_ev_get_complete_bkg_task_msg_regarding_email' ) ) {
	/**
	 * alg_wc_ev_get_complete_bkg_task_msg_regarding_email.
	 *
	 * @version 2.3.8
	 * @since   2.3.8
	 *
	 * @return string
	 */
	function alg_wc_ev_get_complete_bkg_task_msg_regarding_email() {
		$msg = '';
		if ( 'yes' === get_option( 'alg_wc_ev_bkg_process_send_email', 'no' ) ) {
			$msg = sprintf( __( 'When the task is complete an email is going to be sent to %s.', 'emails-verification-for-woocommerce' ), get_option( 'alg_wc_ev_bkg_process_email_to', get_option( 'admin_email' ) ) );
		}
		return $msg;
	}
}
