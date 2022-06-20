<?php
/**
 * Email Verification for WooCommerce - Emails Class.
 *
 * @version 2.3.7
 * @since   1.6.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Emails' ) ) :

class Alg_WC_Email_Verification_Emails {
	/**
	 * Constructor.
	 *
	 * @version 2.3.6
	 * @since   1.6.0
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_wc_ev_send_as_separate_email', 'yes' ) ) {
			// Activation email
			$new_user_action = apply_filters( 'alg_wc_ev_new_user_action', ( get_option( 'alg_wc_ev_new_user_action', 'user_register' ) ) );
			add_action( $new_user_action, array( $this, 'handle_activation_email_sending' ), PHP_INT_MAX - 1 );
			// Delay WC customer new account email
			if ( 'yes' === get_option( 'alg_wc_ev_delay_wc_email', 'no' ) ) {
				add_action( 'init', array( $this, 'remove_customer_new_account_email' ), 90 );
			}
			add_action( 'init', array( $this, 'maybe_send_delayed_activation_email' ), 100 );
		} else {
			// Append to WC customer new account email
			add_filter( 'woocommerce_email_additional_content_' . 'customer_new_account', array( $this, 'customer_new_account_reset_and_append_verification_link' ), PHP_INT_MAX, 3 );
			// Append to anywhere else
			add_action( 'alg_wc_ev_activation_email_content_placeholder', array( $this, 'customer_new_account_reset_and_append_verification_link_fine_tune' ) );
			add_shortcode( 'alg_wc_ev_email_content_placeholder', array( $this, 'alg_wc_ev_email_content_placeholder' ) );
		}
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'maybe_send_wc_customer_new_account_email' ) );
		// Confirmation email.
		add_action( 'alg_wc_ev_user_account_activated', array( $this, 'maybe_send_confirmation_email' ), 10, 2 );
	}

	/**
	 * Send Confirmation email to the user.
	 *
	 * @version 2.3.6
	 * @since   2.2.9
	 *
	 * @param   $user_id
	 * @param   $args
	 */
	function maybe_send_confirmation_email( $user_id, $args ) {
		if ( 'yes' === get_option( 'alg_wc_ev_enable_confirmation_email', 'yes' ) ) {
			$user      = new WP_User( $user_id );
			$recipient = $user->user_email;
			if ( '' === $recipient ) {
				return;
			}
			$content            = $this->get_email_content( array(
				'user_id' => $user_id,
				'context' => 'confirmation_email',
				'content' => __( 'Your account has been activated successfully', 'emails-verification-for-woocommerce' ),
				'heading' => __( 'Your account has been activated', 'emails-verification-for-woocommerce' )
			) );
			$subject            = $this->get_email_subject( array(
				'user_id' => $user_id,
				'context' => 'confirmation_email',
				'subject' => __( 'Your account has been activated successfully', 'emails-verification-for-woocommerce' )
			) );
			$wc_email_template  = get_option( 'alg_wc_ev_wc_email_template', 'simulation' );
			$email_template     = get_option( 'alg_wc_ev_email_template', 'plain' );

			if ( in_array( $email_template, array( 'wc', 'smart' ) ) && 'real_wc_email' === $wc_email_template ) {
				do_action('alg_wc_ev_trigger_confirmation_wc_email', $user_id );
			} else {
				$this->send_mail( $recipient, $subject, $content );
			}

			$data = array( 'confirmation_email_sent' => time() );
			alg_wc_ev()->core->update_activation_code_data( $user_id, $args['code'], $data );
		}
	}

	/**
	 * alg_wc_ev_email_content_placeholder.
	 *
	 * @version 2.1.3
	 * @since   2.1.3
	 *
	 * @param $atts
	 *
	 * @return false|string
	 */
	function alg_wc_ev_email_content_placeholder( $atts ) {
		$atts = shortcode_atts( array(
			'user_email' => '',
		), $atts, 'alg_wc_ev_email_content_placeholder' );
		if ( is_a( $user = get_user_by( 'email', $atts['user_email'] ), 'WP_User' ) ) {
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
	 * @version 2.0.4
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
				$this->reset_and_mail_activation_link( $user_id );
			}
		}
	}

	/**
	 * handle_activation_email_sending.
	 *
	 * @version 2.3.5
	 * @since   2.0.2
	 *
	 * @param $user_id
	 */
	function handle_activation_email_sending( $user_id ) {
		if ( 'yes' !== get_option( 'alg_wc_ev_delay_activation_email', 'no' ) ) {
			$this->reset_and_mail_activation_link( $user_id );
		} else {
			$code = md5( time() );
			$this->update_all_user_meta( $user_id, $code );
			$delayed_email_users   = get_option( 'alg_wc_ev_send_delayed_email_users', array() );
			$delayed_email_users[] = $user_id;
			update_option( 'alg_wc_ev_send_delayed_email_users', array_unique( $delayed_email_users ) );
		}
	}

	/**
	 * get_verification_url.
	 *
	 * @version 2.1.1
	 * @since   1.8.0
	 */
	function get_verification_url( $user_id, $code = false ) {
		if ( false === $code ) {
			$code = md5( time() );
		}
		return add_query_arg( 'alg_wc_ev_verify_email', alg_wc_ev()->core->base64_url_encode( json_encode( array( 'id' => $user_id, 'code' => $code ) ) ), wc_get_page_permalink( 'myaccount' ) );
	}

	/**
	 * get_email_subject.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 */
	function get_email_subject( $args ) {
		$args         = wp_parse_args( $args, array(
			'user_id'      => '',
			'subject'      => '',
			'context'      => 'activation_email_separate',
			'placeholders' => array()
		) );
		$user_id      = $args['user_id'];
		$placeholders = array_merge( $args['placeholders'], alg_wc_ev_get_user_placeholders( array( 'user_id' => $user_id ) ) );
		$subject      = apply_filters( 'alg_wc_ev_email_subject', $args['subject'], $args );
		return apply_filters( 'alg_wc_ev_email_subject_final', str_replace( array_keys( $placeholders ), $placeholders, $subject ), $args );
	}

	/**
	 * get_email_content.
	 *
	 * @version 2.3.1
	 * @since   1.8.0
	 * @todo    (maybe) `$user->user_url`, `$user->user_registered`
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	function get_email_content( $args = null ) {
		$args = wp_parse_args( $args, array(
			'user_id' => '',
			'code'    => false,
			'content' => __( '<p>Please <a href="%verification_url%" target="_blank">click here</a> to verify your email.</p>', 'emails-verification-for-woocommerce' ),
			'heading' => __( 'Activate your account', 'emails-verification-for-woocommerce' ),
			'context' => 'activation_email_separate',
			'placeholders' => array()
		) );
		$user_id = $args['user_id'];
		$code = $args['code'];
		$placeholders = array_merge( $args['placeholders'], alg_wc_ev_get_user_placeholders( array( 'user_id' => $user_id ) ) );
		if ( $args['code'] ) {
			$placeholders['%verification_url%'] = $this->get_verification_url( $user_id, $code );
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
	 * @version 2.3.1
	 * @since   1.8.0
	 * @todo    (recheck) `<p>` and plain?
	 * @todo    (maybe) try getting new code before generating new one (i.e. `$code = get_user_meta( $user->ID, 'alg_wc_ev_activation_code', true );`)
	 */
	function customer_new_account_reset_and_append_verification_link( $content, $user, $email ) {
		$code = md5( time() );
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
	 * append_verification_link.
	 *
	 * @version 2.1.8
	 * @since   2.0.4
	 *
	 * @param $user
	 */
	function customer_new_account_reset_and_append_verification_link_fine_tune( $user ) {
		if ( 'no' === get_option( 'alg_wc_ev_fine_tune_activation_email_placement', 'no' ) ) {
			return;
		}
		$code = md5( time() );
		$this->update_all_user_meta( $user->ID, $code );
		if ( ! alg_wc_ev()->core->is_user_verified( $user ) ) {
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
	 * @version 2.3.7
	 * @since   1.0.0
	 * @todo    (maybe) add `%site_name%` etc. replaced value in `alg_wc_ev_email_subject`
	 */
	function reset_and_mail_activation_link( $user_id ) {
		if ( $user_id && apply_filters( 'alg_wc_ev_reset_and_mail_activation_link_validation', true, $user_id, current_filter() ) ) {
			// Get data
			$user          = get_userdata( $user_id );
			$code          = md5( time() );
			$email_content = $this->get_email_content( array(
				'user_id' => $user_id,
				'code'    => $code,
				'context' => 'activation_email_separate',
			) );
			$email_subject = $this->get_email_subject( array(
				'user_id' => $user_id,
				'context' => 'activation_email_separate',
				'subject' => __( 'Please activate your account', 'emails-verification-for-woocommerce' )
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
	 * remove_customer_new_account_email.
	 *
	 * @version 1.6.0
	 * @since   1.2.0
	 */
	function remove_customer_new_account_email() {
		if ( class_exists( 'WC_Emails' ) && method_exists( 'WC_Emails', 'instance' ) ) {
			$wc_emails = WC_Emails::instance();
			remove_action( 'woocommerce_created_customer_notification', array( $wc_emails, 'customer_new_account' ), 10, 3 );
		}
	}

	/**
	 * maybe_send_wc_customer_new_account_email.
	 *
	 * @see wc_create_new_customer()
	 *
	 * @version 2.0.8
	 * @since   1.6.0
	 */
	function maybe_send_wc_customer_new_account_email( $user_id ) {
		if (
			'yes' === get_option( 'alg_wc_ev_send_as_separate_email', 'yes' ) &&
			'yes' === get_option( 'alg_wc_ev_delay_wc_email', 'no' ) &&
			'' == get_user_meta( $user_id, 'alg_wc_ev_customer_new_account_email_sent', true ) &&
			class_exists( 'WC_Emails' ) && method_exists( 'WC_Emails', 'instance' )
		) {
			$wc_emails     = WC_Emails::instance();
			$customer_data = ( $password_generated = 'yes' === get_option( 'woocommerce_registration_generate_password', 'yes' ) ) ? array( 'user_pass' => $user_pass = wp_generate_password() ) : array();
			if ( $password_generated ) {
				add_filter( 'send_password_change_email', '__return_false' );
				wp_update_user( array( 'ID' => $user_id, 'user_pass' => $user_pass ) );
			}
			$wc_emails->customer_new_account( $user_id, $customer_data, $password_generated );
			update_user_meta( $user_id, 'alg_wc_ev_customer_new_account_email_sent', time() );
		}
	}

	/**
	 * send_mail.
	 *
	 * @version 1.9.2
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
		$res     = $func( $to, $subject, $message, "Content-Type: text/html\r\n" );
		if ( ! $res ) {
			$error_message  = __( 'Error sending mail.', 'emails-verification-for-woocommerce' );
			$error_message .= ' ' . sprintf( __( 'Mail function: %s.', 'emails-verification-for-woocommerce' ), $func );
			$last_error     = error_get_last();
			if ( ! empty( $last_error['message'] ) ) {
				$error_message .= ' ' . sprintf( __( 'Last error: %s.', 'emails-verification-for-woocommerce' ), $last_error['message'] );
			}
			alg_wc_ev()->core->add_to_log( $error_message );
		}
	}

}

endif;

return new Alg_WC_Email_Verification_Emails();
