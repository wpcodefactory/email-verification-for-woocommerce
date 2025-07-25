<?php
/**
 * Email Verification for WooCommerce - Emails Class.
 *
 * @version 3.0.7
 * @since   1.6.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Emails' ) ) :

class Alg_WC_Email_Verification_Emails {

	/**
	 * Flag that tells whether the customer new account email is enabled or disabled.
	 *
	 * @since 2.4.3
	 *
	 * @var bool
	 */
	protected $activate_customer_new_account_email = false;

	/**
	 * Recent registered user email.
	 *
	 * @since 2.9.6
	 *
	 * @var
	 */
	protected $recent_registered_user_email;

	/**
	 * Constructor.
	 *
	 * @version 2.9.7
	 * @since   1.6.0
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_wc_ev_send_as_separate_email', 'yes' ) ) {
			// Activation email.
			$new_user_action = apply_filters( 'alg_wc_ev_new_user_action', ( get_option( 'alg_wc_ev_new_user_action', 'user_register' ) ) );
			add_action( $new_user_action, array( $this, 'handle_activation_email_sending' ), PHP_INT_MAX - 1 );
			// Delay WC customer new account email.
			if ( 'yes' === get_option( 'alg_wc_ev_delay_wc_email', 'no' ) ) {
				add_filter( 'woocommerce_email_enabled_' . 'customer_new_account', array( $this, 'maybe_disable_customer_new_account_email' ) );
				add_action( 'woocommerce_created_customer_notification', array( $this, 'disable_customer_new_account_email' ), 9 );
				add_action( 'woocommerce_created_customer_notification', array( $this, 'enable_customer_new_account_email' ), 11 );
			}
			add_action( 'init', array( $this, 'maybe_send_delayed_activation_email' ), 100 );
		} else {
			// Append to WC customer new account email
			add_filter( 'woocommerce_email_additional_content_' . 'customer_new_account', array( $this, 'customer_new_account_reset_and_append_verification_link' ), PHP_INT_MAX, 3 );
			// Append to anywhere else
			add_action( 'alg_wc_ev_activation_email_content_placeholder', array( $this, 'customer_new_account_reset_and_append_verification_link_fine_tune' ) );
			add_shortcode( 'alg_wc_ev_email_content_placeholder', array( $this, 'alg_wc_ev_email_content_placeholder' ) );
			$new_user_action = apply_filters( 'alg_wc_ev_new_user_action', ( get_option( 'alg_wc_ev_new_user_action', 'user_register' ) ) );
			add_action( $new_user_action, array( $this, 'get_recent_registered_user_email' ), PHP_INT_MAX - 1 );
		}
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'maybe_send_wc_customer_new_account_email' ) );
		// Confirmation email.
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'maybe_send_confirmation_email' ), 10, 2 );
		add_action( 'alg_wc_ev_confirmation_email_delay_event', array( $this, 'send_confirmation_email' ), 10, 2 );
	}

	/**
	 * send_confirmation_email.
	 *
	 * @version 2.6.7
	 * @since   2.4.1
	 *
	 * @param $user_id
	 * @param null $args
	 */
	function send_confirmation_email( $user_id, $args = null ) {
		$user      = new WP_User( $user_id );
		$recipient = $user->user_email;
		if ( '' === $recipient ) {
			return;
		}
		$content           = $this->get_email_content( array(
			'user_id' => $user_id,
			'context' => 'confirmation_email',
			'content' => alg_wc_ev()->core->emails->get_default_email_content('confirmation'),
			'heading' => __( 'Your account has been activated', 'emails-verification-for-woocommerce' )
		) );
		$subject           = $this->get_email_subject( array(
			'user_id' => $user_id,
			'context' => 'confirmation_email',
			'subject' => '[%site_title%]: ' . __( 'Your account has been activated successfully', 'emails-verification-for-woocommerce' )
		) );
		$wc_email_template = get_option( 'alg_wc_ev_wc_email_template', 'simulation' );
		$email_template    = get_option( 'alg_wc_ev_email_template', 'plain' );

		if ( in_array( $email_template, array( 'wc', 'smart' ) ) && 'real_wc_email' === $wc_email_template ) {
			do_action( 'alg_wc_ev_trigger_confirmation_wc_email', $user_id );
		} else {
			$this->send_mail( $recipient, $subject, $content );
		}

		$data = array( 'confirmation_email_sent' => time() );
		alg_wc_ev()->core->update_activation_code_data( $user_id, $args['code'], $data );
	}

	/**
	 * Send Confirmation email to the user.
	 *
	 * @version 2.4.8
	 * @since   2.2.9
	 *
	 * @param   $user_id
	 * @param   $args
	 */
	function maybe_send_confirmation_email( $user_id, $args = null ) {
		if ( 'yes' !== get_option( 'alg_wc_ev_enable_confirmation_email', 'yes' ) ) {
			return;
		}
		if (
			isset( $args['context'] ) &&
			! empty( $args['context'] ) &&
			'admin_verification' === $args['context'] &&
			'no' === get_option( 'alg_wc_ev_send_confirmation_email_to_manually_verified_users', 'no' )
		) {
			return;
		}
		if ( 'no' === get_option( 'alg_wc_ev_confirmation_email_delay', 'no' ) ) {
			$this->send_confirmation_email( $user_id, $args );
		} else {
			wp_schedule_single_event( $this->get_confirmation_email_delay_timestamp(), 'alg_wc_ev_confirmation_email_delay_event', array( $user_id, $args ) );
		}
	}

	/**
	 * get_confirmation_email_delay_timestamp.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @return float|int
	 */
	function get_confirmation_email_delay_timestamp() {
		$delay_value     = get_option( 'alg_wc_ev_confirmation_email_delay_value', 1 );
		$delay_unit      = get_option( 'alg_wc_ev_confirmation_email_delay_time_unit', 'hours' );
		$unit_in_seconds = $delay_unit === 'hours' ? HOUR_IN_SECONDS : ( $delay_unit === 'days' ? DAY_IN_SECONDS : 1 );
		$timestamp       = time() + ( $delay_value * $unit_in_seconds );
		return $timestamp;
	}

	/**
	 * alg_wc_ev_email_content_placeholder.
	 *
	 * @version 2.9.6
	 * @since   2.1.3
	 *
	 * @param $atts
	 *
	 * @return false|string
	 */
	function alg_wc_ev_email_content_placeholder( $atts ) {
		if ( 'yes' !== get_option( 'alg_wc_ev_sc_email_content_placeholder', 'yes' ) ) {
			return '[alg_wc_ev_email_content_placeholder]';
		}
		if (
			! empty( $this->recent_registered_user_email ) &&
			filter_var( $this->recent_registered_user_email, FILTER_VALIDATE_EMAIL ) &&
			is_a( $user = get_user_by( 'email', $this->recent_registered_user_email ), 'WP_User' )
		) {
			ob_start();
			$this->customer_new_account_reset_and_append_verification_link_fine_tune( $user );
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		return '';
	}

	/**
	 * maybe_send_delayed_activation_email.
	 *
	 * @version 2.8.6
	 * @since   2.0.2
	 */
	function maybe_send_delayed_activation_email() {
		if (
			'yes' === get_option( 'alg_wc_ev_delay_activation_email', 'no' )
			&& ! empty( $delayed_email_users = get_option( 'alg_wc_ev_send_delayed_email_users', array() ) )
		) {
			$delayed_email_users_update = array_diff( get_option( 'alg_wc_ev_send_delayed_email_users', array() ), $delayed_email_users );
			empty( $delayed_email_users_update ) ? delete_option( 'alg_wc_ev_send_delayed_email_users' ) : update_option( 'alg_wc_ev_send_delayed_email_users', $delayed_email_users_update );
			foreach ( $delayed_email_users as $user_id ) {
				$this->reset_and_mail_activation_link( array( 'user_id' => $user_id ) );
			}
		}
	}

	/**
	 * handle_activation_email_sending.
	 *
	 * @version 2.8.6
	 * @since   2.0.2
	 *
	 * @param $user_id
	 */
	function handle_activation_email_sending( $user_id ) {
		if ( 'yes' !== get_option( 'alg_wc_ev_delay_activation_email', 'no' ) ) {
			$this->reset_and_mail_activation_link( array( 'user_id' => $user_id ) );
		} else {
			$code = alg_wc_ev_generate_user_code();
			$this->update_all_user_meta( $user_id, $code );
			$delayed_email_users   = get_option( 'alg_wc_ev_send_delayed_email_users', array() );
			$delayed_email_users[] = $user_id;
			update_option( 'alg_wc_ev_send_delayed_email_users', array_unique( $delayed_email_users ) );
		}
	}

	/**
	 * get_recent_registered_user_email.
	 *
	 * @version 2.9.6
	 * @since   2.9.6
	 *
	 * @param $user_id
	 *
	 * @return void
	 */
	function get_recent_registered_user_email( $user_id ) {
		$user                               = get_userdata( $user_id );
		$user_email                         = $user->user_email;
		$this->recent_registered_user_email = $user_email;
	}

	/**
	 * get_verification_url.
	 *
	 * @version 2.7.7
	 * @since   1.8.0
	 */
	function get_verification_url( $args = null ) {
		$args = wp_parse_args( $args, array(
			'user_id'                 => '',
			'code'                    => false,
			'verification_check_page' => 'myaccount', // myaccount | checkout
			'encoding_method'         => get_option( 'alg_wc_ev_encoding_method', 'base64_encode' ),
		) );

		if ( filter_var( $args['user_id'], FILTER_VALIDATE_EMAIL ) ) {
			$user_id = $args['user_id'];
		} else {
			$user_id = intval( $args['user_id'] );
		}
		$code                    = $args['code'];
		$code                    = false === $code && ! empty( $activation_code = get_user_meta( $user_id, 'alg_wc_ev_activation_code', true ) ) ? $activation_code : ( ! empty( $code ) ? $code : false );
		$encoding_method         = $args['encoding_method'];
		$verify_email_hash       = '';
		$verification_check_page = $args['verification_check_page'];
		switch ( $encoding_method ) {
			case 'base64_encode':
				if ( false === $code ) {
					$code = alg_wc_ev_generate_user_code();
				}
				$verify_email_hash = alg_wc_ev()->core->base64_url_encode( json_encode( array(
					'id'   => $user_id,
					'code' => $code
				) ) );
				break;
			case 'hashids':
				$hashids = alg_wc_ev_get_hashids();
				if ( false === $code ) {
					$code = alg_wc_ev_generate_user_code();
				}
				$verify_email_hash = $hashids->encode( $user_id, $code );
				break;
		}
		switch ( $verification_check_page ) {
			case 'checkout':
				$verification_check_page = wc_get_checkout_url();
				break;
			default:
				$verification_check_page = wc_get_page_permalink( 'myaccount' );
		}

		return add_query_arg( alg_wc_ev_get_verification_param(), $verify_email_hash, $verification_check_page );
	}

	/**
	 * get_email_subject.
	 *
	 * @version 2.6.5
	 * @since   2.3.1
	 */
	function get_email_subject( $args ) {
		$args         = wp_parse_args( $args, array(
			'user_id'      => '',
			'subject'      => '',
			'context'      => 'activation_email_separate',
			'placeholders' => alg_wc_ev_get_common_placeholders()
		) );
		$user_id      = $args['user_id'];
		$placeholders = array_merge( $args['placeholders'], alg_wc_ev_get_user_placeholders( array( 'user_id' => $user_id ) ) );
		$subject      = apply_filters( 'alg_wc_ev_email_subject', $args['subject'], $args );
		return apply_filters( 'alg_wc_ev_email_subject_final', str_replace( array_keys( $placeholders ), $placeholders, $subject ), $args );
	}

	/**
	 * get_email_content.
	 *
	 * @version 2.6.9
	 * @since   1.8.0
	 * @todo    (maybe) `$user->user_url`, `$user->user_registered`
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	function get_email_content( $args = null ) {
		$args                    = wp_parse_args( $args, array(
			'user_id'                 => '',
			'code'                    => false,
			'content'                 => alg_wc_ev()->core->emails->get_default_email_content( 'activation' ),
			'heading'                 => __( 'Activate your account', 'emails-verification-for-woocommerce' ),
			'context'                 => 'activation_email_separate',
			'placeholders'            => alg_wc_ev_get_common_placeholders(),
			'verification_check_page' => 'myaccount', // myaccount | checkout
		) );
		$verification_check_page = $args['verification_check_page'];
		$user_id                 = $args['user_id'];
		$code                    = $args['code'];
		$placeholders            = array_merge( $args['placeholders'], alg_wc_ev_get_user_placeholders( array( 'user_id' => $user_id ) ) );
		if ( $args['code'] ) {
			$placeholders['%verification_url%'] = $this->get_verification_url( array(
				'user_id'                 => $user_id,
				'code'                    => $code,
				'verification_check_page' => $verification_check_page
			) );
		}
		$content = apply_filters( 'alg_wc_ev_email_content', $args['content'], $args );

		return apply_filters( 'alg_wc_ev_email_content_final', str_replace( array_keys( $placeholders ), $placeholders, $content ), $args );
	}

	/**
	 * update_all_user_meta.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 * @todo    [next] (maybe) run this always and only on `$new_user_action` (i.e. instead of on `$new_user_action` or `woocommerce_email_additional_content_`)
	 */
	function update_all_user_meta( $user_id, $code ) {
		update_user_meta( $user_id, 'alg_wc_ev_is_activated',         '0' );
		update_user_meta( $user_id, 'alg_wc_ev_activation_code',      $code );
		update_user_meta( $user_id, 'alg_wc_ev_activation_code_time', time() );
	}

	/**
	 * customer_new_account_reset_and_append_verification_link.
	 *
	 * @version 2.4.0
	 * @since   1.8.0
	 * @todo    (recheck) `<p>` and plain?
	 * @todo    (maybe) try getting new code before generating new one (i.e. `$code = get_user_meta( $user->ID, 'alg_wc_ev_activation_code', true );`)
	 */
	function customer_new_account_reset_and_append_verification_link( $content, $user, $email ) {
		$code = alg_wc_ev_generate_user_code();
		$this->update_all_user_meta( $user->ID, $code );
		if (
			'no' === get_option( 'alg_wc_ev_fine_tune_activation_email_placement', 'no' )
			&& ! alg_wc_ev()->core->is_user_verified( $user )
		) {
			update_user_meta( $user->ID, 'alg_wc_ev_activation_email_sent', time() );
			return str_replace(
				       array( '<br>' ),
				       array( "\n" ),
				       $this->get_email_content( array(
					       'user_id' => $user->ID,
					       'code'    => $code,
					       'context' => 'activation_email_customer_new_account_email'
				       ) ) ) . "\n\n" . $content;
		}
		return $content;
	}

	/**
	 * get_default_email_content.
	 *
	 * @version 2.6.7
	 * @since   2.6.7
	 *
	 * @param $email_type 'activation' | confirmation | admin
	 *
	 * @return string
	 */
	function get_default_email_content( $email_type ) {
		$default_content = '';
		switch ( $email_type ) {
			case 'activation':
				$default_content = sprintf(
					__( '<p>Please <a href="%s" target="_blank">click here</a> to verify your email on %s.</p>', 'emails-verification-for-woocommerce' ),
					'%verification_url%',
					'<a href="%site_url%" target="_blank">%site_title%</a>'
				);
				break;
			case 'confirmation':
				$default_content = sprintf(
					__( '<p>Your account has been activated successfully on %s.</p>', 'emails-verification-for-woocommerce' ),
					'<a href="%site_url%" target="_blank">%site_title%</a>'
				);
				break;
			case 'admin':
				$default_content =
					sprintf(
						__( 'User %s has just verified his email (%s) on %s.', 'emails-verification-for-woocommerce' ),
						'<a href="%admin_user_profile_url%">%user_login%</a>',
						'%user_email%',
						'<a href="%site_url%" target="_blank">%site_title%</a>'
					);
				break;
		}

		return $default_content;
	}

	/**
	 * append_verification_link.
	 *
	 * @version 2.8.3
	 * @since   2.0.4
	 *
	 * @param $user
	 */
	function customer_new_account_reset_and_append_verification_link_fine_tune( $user ) {
		if ( 'no' === get_option( 'alg_wc_ev_fine_tune_activation_email_placement', 'no' ) ) {
			return;
		}
		if ( ! alg_wc_ev()->core->is_user_verified( $user ) ) {
			$code = ! empty( $activation_code = get_user_meta( $user->ID, 'alg_wc_ev_activation_code', true ) ) ? $activation_code : false;
			if ( empty( $code ) ) {
				$code = alg_wc_ev_generate_user_code();
				$this->update_all_user_meta( $user->ID, $code );
			}
			update_user_meta( $user->ID, 'alg_wc_ev_activation_email_sent', time() );
			echo wp_kses_post( wpautop( wptexturize( $this->get_email_content( array(
				'user_id' => $user->ID,
				'code'    => $code,
				'context' => 'activation_email_content_placeholder'
			) ) ) ) );
		}
	}

	/**
	 * reset_and_mail_activation_link.maybe_send_wc_customer_new_account_email.
	 *
	 * @version 2.8.6
	 * @since   1.0.0
	 * @todo    (maybe) add `%site_name%` etc. replaced value in `alg_wc_ev_email_subject`
	 */
	function reset_and_mail_activation_link( $args = null ) {
		$args    = wp_parse_args( $args, array(
			'user_id' => '',
			'context' => '',
		) );
		$user_id = intval( $args['user_id'] );
		$context = $args['context'];
		if ( $user_id && apply_filters( 'alg_wc_ev_reset_and_mail_activation_link_validation', true, $user_id, current_filter() ) ) {
			do_action('alg_wc_ev_reset_and_mail_activation_link', $args );
			// Get data.
			$user          = get_userdata( $user_id );
			$code          = alg_wc_ev_generate_user_code();
			$email_content = $this->get_email_content( array(
				'user_id' => $user_id,
				'code'    => $code,
				'context' => 'activation_email_separate',
			) );
			$email_subject = $this->get_email_subject( array(
				'user_id' => $user_id,
				'context' => 'activation_email_separate',
				'subject' => '[%site_title%]: ' . __( 'Please activate your account', 'emails-verification-for-woocommerce' )
			) );
			// Set user meta
			$this->update_all_user_meta( $user_id, $code );
			// Send email
			if ( ! alg_wc_ev()->core->is_user_verified_by_user_id( $user_id ) ) {
				$wc_email_template  = get_option( 'alg_wc_ev_wc_email_template', 'simulation' );
				$email_template     = get_option( 'alg_wc_ev_email_template', 'plain' );
				if ( in_array( $email_template, array( 'wc', 'smart' ) ) && 'real_wc_email' === $wc_email_template ) {
					do_action('alg_wc_ev_trigger_activation_wc_email', $user_id );
				} else {
					$this->send_mail( $user->user_email, $email_subject, $email_content );
				}

				// Store referer url in meta from cookies
				if(
					isset( $_COOKIE['alg_wc_ev_my_account_referer_url'] ) &&
					'my_account_referer' === get_option( 'alg_wc_ev_redirect_to_my_account_on_success', 'yes' )
				) {
					update_user_meta( $user_id, 'alg_wc_ev_my_account_referer_url', sanitize_url( $_COOKIE['alg_wc_ev_my_account_referer_url'] ) );
				}

				update_user_meta( $user_id, 'alg_wc_ev_activation_email_sent', time() );
			} else {
				$this->maybe_send_wc_customer_new_account_email( $user_id );
			}
		}
	}

	/**
	 * maybe_disable_customer_new_account_email.
	 *
	 * @version 2.5.0
	 * @since   2.4.3
	 *
	 * @param $enable
	 *
	 * @return bool
	 */
	function maybe_disable_customer_new_account_email( $enable ) {
		if (
			isset( $_GET['page'] ) &&
			'wc-settings' === $_GET['page'] &&
			isset( $_GET['tab'] ) &&
			'email' === $_GET['tab']
		) {
			return $enable;
		}
		return $enable && $this->activate_customer_new_account_email;
	}

	/**
	 * disable_customer_new_account_email.
	 *
	 * @version 2.4.3
	 * @since   2.4.3
	 *
	 */
	function disable_customer_new_account_email() {
		$this->activate_customer_new_account_email = false;
	}

	/**
	 * enable_customer_new_account_email.
	 *
	 * @version 2.4.3
	 * @since   2.4.3
	 *
	 */
	function enable_customer_new_account_email(){
		$this->activate_customer_new_account_email = true;
	}

	/**
	 * maybe_send_wc_customer_new_account_email.
	 *
	 * @see wc_create_new_customer()
	 *
	 * @version 3.0.7
	 * @since   1.6.0
	 */
	function maybe_send_wc_customer_new_account_email( $user_id ) {
		if (
			'yes' === get_option( 'alg_wc_ev_send_as_separate_email', 'yes' ) &&
			'yes' === get_option( 'alg_wc_ev_delay_wc_email', 'no' ) &&
			'' == get_user_meta( $user_id, 'alg_wc_ev_customer_new_account_email_sent', true ) &&
			class_exists( 'WC_Emails' ) && method_exists( 'WC_Emails', 'instance' )
		) {
			$customer_data = ( $password_generated = 'yes' === get_option( 'woocommerce_registration_generate_password', 'yes' ) ) ? array( 'user_pass' => $user_pass = wp_generate_password() ) : array();
			if ( $password_generated ) {
				add_filter( 'send_password_change_email', '__return_false' );
				wp_set_password( $user_pass, $user_id );
			}
			$wc_emails = WC_Emails::instance();
			$this->activate_customer_new_account_email = true;
			$wc_emails->customer_new_account( $user_id, $customer_data, $password_generated );
			update_user_meta( $user_id, 'alg_wc_ev_customer_new_account_email_sent', time() );
		}
	}

	/**
	 * send_mail.
	 *
	 * @version 3.0.2
	 * @since   1.9.2
	 * @see     https://www.php.net/manual/en/function.mail.php
	 * @see     https://github.com/woocommerce/woocommerce/blob/master/includes/wc-core-functions.php
	 * @see     https://developer.wordpress.org/reference/functions/wp_mail/
	 * @todo    [test] if `$last_error` message for `wc_mail` is logged
	 */
	function send_mail( $to, $subject, $message ) {
		/**
		 * `mail ( string $to , string $subject , string $message [, mixed $additional_headers [, string $additional_parameters ]] ) : bool`
		 * `wc_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' )`
		 * `wp_mail( string|array $to, string $subject, string $message, string|array $headers = '', string|array $attachments = array() )`
		 */
		$func    = get_option( 'alg_wc_ev_mail_function', 'wc_mail' );
		$message = apply_filters( 'alg_wc_ev_send_mail_message', $message, $func );
		add_filter( 'wp_mail_from', array( $this, 'change_email_from' ), PHP_INT_MAX );
		$res = $func( $to, $subject, $message, "Content-Type: text/html\r\n" );
		remove_filter( 'wp_mail_from', array( $this, 'change_email_from' ), PHP_INT_MAX );
		if ( ! $res ) {
			$error_message = __( 'Error sending mail.', 'emails-verification-for-woocommerce' );
			$error_message .= ' ' . sprintf( __( 'Mail function: %s.', 'emails-verification-for-woocommerce' ), $func );
			$last_error    = error_get_last();
			if ( ! empty( $last_error['message'] ) ) {
				$error_message .= ' ' . sprintf( __( 'Last error: %s.', 'emails-verification-for-woocommerce' ), $last_error['message'] );
			}
			alg_wc_ev()->core->add_to_log( $error_message );
		}
	}

	/**
	 * change_email_from.
	 *
	 * @version 3.0.2
	 * @since   3.0.2
	 *
	 * @param $from
	 *
	 * @return mixed
	 */
	function change_email_from( $from ) {
		remove_filter( 'wp_mail_from', array( $this, 'change_email_from' ), PHP_INT_MAX );
		$from = get_option( 'alg_wc_ev_wc_email_from', alg_wc_ev_get_default_email_from() );

		return $from;
	}

}

endif;

return new Alg_WC_Email_Verification_Emails();
