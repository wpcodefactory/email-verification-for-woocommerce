<?php
/**
 * Email Verification for WooCommerce - Functions.
 *
 * @version 2.8.2
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
	 * @version 2.8.2
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
		$user         = ! empty( $args['user'] ) && is_a( $args['user'], 'WP_User' ) ? $args['user'] : new WP_User( intval( $args['user_id'] ) );
		$user_id      = is_a( $user, 'WP_User' ) ? $user->ID : intval( $args['user_id'] );
		$placeholders = array(
			'%user_id%'                 => $user_id,
			'%user_login%'              => $user->user_login,
			'%user_nicename%'           => $user->user_nicename,
			'%user_email%'              => $user->user_email,
			'%user_url%'                => $user->user_url,
			'%user_registered%'         => $user->user_registered,
			'%user_display_name%'       => $user->display_name,
			'%user_roles%'              => implode( ', ', $user->roles ),
			'%user_first_name%'         => $user->first_name,
			'%user_last_name%'          => $user->last_name,
			'%resend_verification_url%' => alg_wc_ev()->core->messages->get_resend_verification_url( $user_id ),
			'%admin_user_profile_url%'  => admin_url( 'user-edit.php?user_id=' . $user_id ),
		);

		return apply_filters( 'alg_wc_ev_user_placeholders_args', $placeholders, $args );
	}
}

if ( ! function_exists( 'alg_wc_ev_get_common_placeholders' ) ) {
	/**
	 * alg_wc_ev_get_common_placeholders.
	 *
	 * @version 2.6.5
	 * @since   2.6.5
	 *
	 * @return array
	 */
	function alg_wc_ev_get_common_placeholders() {
		$placeholders = array(
			'%site_title%' => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
			'%site_url%'   => wp_parse_url( home_url(), PHP_URL_HOST )
		);

		return apply_filters( 'alg_wc_ev_common_placeholders', $placeholders );
	}
}

if ( ! function_exists( 'alg_wc_ev_associative_array_replace' ) ) {
	/**
	 * alg_wc_ev_associative_array_replace.
	 *
	 * @version 2.8.2
	 * @since   2.8.2
	 *
	 * @param $args
	 *
	 * @return array|mixed|string|string[]
	 */
	function alg_wc_ev_associative_array_replace( $args = null ) {
		$args       = wp_parse_args( $args, array(
			'from_to'    => array(),
			'delimiters' => array( array( '{', '}' ), array( '%', '%' ) ),
			'subject'    => ''
		) );
		$from_to    = $args['from_to'];
		$delimiters = $args['delimiters'];
		$subject    = $args['subject'];
		// Sanitizes from to.
		foreach ( $from_to as $from => $to ) {
			foreach ( $delimiters as $v ) {
				$delimiter_start = $v[0];
				$delimiter_end   = $v[1];
				$new_from        = $from;
				if ( $delimiter_start === substr( $from, 0, 1 ) ) {
					unset( $from_to[ $from ] );
					$new_from = substr( $from, 1 );
				}
				if ( $delimiter_end === substr( $from, - 1 ) ) {
					unset( $from_to[ $from ] );
					$new_from = substr( $new_from, 0, - 1 );
				}
				$from_to[ $new_from ] = $to;
			}
		}
		// Adds delimiters.
		foreach ( $from_to as $from => $to ) {
			foreach ( $delimiters as $v ) {
				$delimiter_start      = $v[0];
				$delimiter_end        = $v[1];
				$new_from             = $delimiter_start . $from . $delimiter_end;
				$from_to[ $new_from ] = $to;
			}
		}
		// Removes entries with no delimiters.
		foreach ( $from_to as $from => $to ) {
			$delimeters_check_count = 0;
			foreach ( $delimiters as $v ) {
				$delimiter_start = $v[0];
				$delimiter_end   = $v[1];
				if ( $delimiter_start !== substr( $from, 0, 1 ) || $delimiter_end !== substr( $from, - 1 ) ) {
					$delimeters_check_count ++;
				}
			}
			if ( $delimeters_check_count == count( $delimiters ) ) {
				unset( $from_to[ $from ] );
			}
		}

		return str_replace( array_keys( $from_to ), $from_to, $subject );
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

if ( ! function_exists( 'alg_wc_ev_generate_placeholders_for_villatheme_email_customizer' ) ) {
	/**
	 * get_placeholders_for_villatheme_email_customizer.
	 *
	 * @version 2.3.9
	 * @since   2.3.9
	 *
	 * @param $user
	 *
	 * @return array|mixed
	 */
	function alg_wc_ev_generate_placeholders_for_villatheme_email_customizer( $user ) {
		$placeholders     = alg_wc_ev_get_user_placeholders( array(
			'user' => $user
		) );
		$new_placeholders = array_map( function ( $k, $v ) {
			$new_key = preg_replace( '/\%$/', '}', preg_replace( '/^\%/', '{alg_wc_ev_', $k ) );
			return array( $new_key => $v );
		}, array_keys( $placeholders ), $placeholders );
		$new_placeholders = call_user_func_array( 'array_merge', $new_placeholders );
		return $new_placeholders;
	}
}

if ( ! function_exists( 'alg_wc_ev_get_hashids' ) ) {
	/**
	 * alg_wc_ev_get_hashids.
	 *
	 * @version 2.6.0
	 * @since   2.4.0
	 *
	 * @return \Hashids\Hashids
	 */
	function alg_wc_ev_get_hashids() {
		return alg_wc_ev()->core->get_hashids();
	}
}

if ( ! function_exists( 'alg_wc_ev_generate_user_code' ) ) {
	/**
	 * generate_user_code.
	 *
	 * @version 2.7.5
	 * @since   2.4.0
	 *
	 * @param null $args
	 *
	 * @return int|string
	 */
	function alg_wc_ev_generate_user_code( $args = null ) {
		$args = wp_parse_args( $args, array(
			'encoding_method' => get_option( 'alg_wc_ev_encoding_method', 'base64_encode' ),
		) );
		$code = '';
		if ( 'base64_encode' === $args['encoding_method'] ) {
			$code = md5( wp_generate_password() );
		} elseif ( 'hashids' === $args['encoding_method'] ) {
			$code = wp_rand( 100 );
		}

		return $code;
	}
}

if ( ! function_exists( 'alg_wc_ev_decode_verify_code' ) ) {

	/**
	 * alg_wc_ev_decode_verify_code.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 *
	 * @param null $args
	 *
	 * @return array
	 */
	function alg_wc_ev_decode_verify_code( $args = null ) {
		$args        = wp_parse_args( $args, array(
			'verify_code'     => '',
			'encoding_method' => get_option( 'alg_wc_ev_encoding_method', 'base64_encode' ),
		) );
		$verify_code = $args['verify_code'];
		$data        = array();
		if ( 'base64_encode' === $args['encoding_method'] ) {
			$data = json_decode( alg_wc_ev()->core->base64_url_decode( $verify_code ), true );
		} elseif ( 'hashids' === $args['encoding_method'] ) {
			$hashids         = alg_wc_ev_get_hashids();
			$hashids_decoded = $hashids->decode( $verify_code );
			$data['id']      = is_array( $hashids_decoded ) && isset( $hashids_decoded[0] ) ? $hashids_decoded[0] : '';
			$data['code']    = is_array( $hashids_decoded ) && isset( $hashids_decoded[1] ) ? $hashids_decoded[1] : '';
		}
		return $data;
	}
}

if ( ! function_exists( 'alg_wc_ev_get_current_url' ) ) {
	/**
	 * alg_wc_ev_get_current_url.
	 *
	 * @version 2.4.7
	 * @since   2.4.7
	 *
	 * @return string
	 */
	function alg_wc_ev_get_current_url() {
		global $wp;
		$wp->parse_request();
		$query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '';
		$current_url  = trailingslashit( home_url( $wp->request ) ) . $query_string;
		return $current_url;
	}
}

if ( ! function_exists( 'alg_wc_ev_get_verification_param' ) ) {
	/**
	 * alg_wc_ev_get_verification_param.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 *
	 * @return string
	 */
	function alg_wc_ev_get_verification_param() {
		return apply_filters( 'alg_wc_ev_verification_param', 'alg_wc_ev_verify_email' );
	}
}