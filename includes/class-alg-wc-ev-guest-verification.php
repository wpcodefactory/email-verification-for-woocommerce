<?php
/**
 * Email Verification for WooCommerce - Guest Verification.
 *
 * @version 2.9.8
 * @since   2.8.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Guest_Verification' ) ) {

	class Alg_WC_Email_Verification_Guest_Verification {

		/**
		 * init.
		 *
		 * @version 2.9.7
		 * @since   2.9.7
		 *
		 * @return void
		 */
		function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_guest_feature_scripts' ) );
			add_action( 'wp_footer', array( $this, 'verify_guest_at_checkout_script_footer' ), PHP_INT_MAX );
			add_action( 'wp_ajax_alg_wc_ev_send_guest_verification_email_action', array( $this, 'alg_wc_ev_send_guest_verification_email_action' ) );
			add_action( 'wp_ajax_nopriv_alg_wc_ev_send_guest_verification_email_action', array( $this, 'alg_wc_ev_send_guest_verification_email_action' ) );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'checkout_validate_guest_email' ), PHP_INT_MAX );
			add_action( 'template_redirect', array( $this, 'checkout_validate_guest_email_message' ) );
			add_action( 'user_register', array( $this, 'sync_verification_if_guest' ), PHP_INT_MAX - 1 );

			// Guest verification table.
			add_filter( 'pre_update_option_alg_wc_ev_verify_guest_email', array( $this, 'create_guest_verification_table' ), 10, 2 );
		}

		/**
		 * enqueue_guest_feature_scripts.
		 *
		 * @version 2.9.7
		 * @since   2.9.0
		 *
		 * @return void
		 */
		function enqueue_guest_feature_scripts() {
			if (
				is_user_logged_in() ||
				'yes' !== get_option( 'alg_wc_ev_verify_guest_email', 'no' ) ||
				! is_checkout()
			) {
				return;
			}

			$wc_ev_options = array(
				'send'                => sprintf(
				/* translators: %s is the text for the "Send Verify Email" link text. */
					'<a href="javascript:;" id="alg_wc_ev_send_verify">%s</a>',
					esc_html( get_option( 'alg_wc_ev_verify_guest_send_link_text', __( 'Send Verify Email', 'emails-verification-for-woocommerce' ) ) )
				),
				'resend'              => esc_html__( 'Resending...', 'emails-verification-for-woocommerce' ),
				'sending'             => esc_html__( 'Sending...', 'emails-verification-for-woocommerce' ),
				'sent'                => sprintf(
				/* translators: %1$s is the success message, %2$s is the "Resend" link text. */
					'%1$s <a href="javascript:;" id="alg_wc_ev_resend_verify">%2$s</a>',
					esc_html( get_option( 'alg_wc_ev_verify_guest_verification_message', __( 'Verification mail sent successfully to billing email, please check inbox and verify!', 'emails-verification-for-woocommerce' ) ) ),
					esc_html( get_option( 'alg_wc_ev_verify_guest_resent_text', __( 'Resend', 'emails-verification-for-woocommerce' ) ) )
				),
				'already_verified'    => esc_html( get_option( 'alg_wc_ev_verify_guest_already_verification_message', __( 'Email ID verified!', 'emails-verification-for-woocommerce' ) ) ),
				'error_nonce_message' => sprintf(
				/* translators: %1$s is the error message, %2$s is the "Resend" link text. */
					'%1$s <a href="javascript:;" id="alg_wc_ev_resend_verify">%2$s</a>',
					esc_html( get_option( 'alg_wc_ev_verify_guest_verification_message', __( 'The request could not be completed due to an invalid or expired security token. Please refresh and try again!', 'emails-verification-for-woocommerce' ) ) ),
					esc_html( get_option( 'alg_wc_ev_verify_guest_resent_text', __( 'Resend', 'emails-verification-for-woocommerce' ) ) )
				),
				'email_exists'        => __( 'Sorry, that email address is already used!' ), // Without Email Verification domain because it's a default WordPress msg.
				'security_nonce'      => wp_create_nonce( 'alg_wc_ev_ajax_security_nonce' ),
			);

			wp_enqueue_script( 'alg-wc-ev-guest-verify', trailingslashit( alg_wc_ev()->plugin_url() ) . 'includes/js/alg-wc-ev-guest-verify.js', array( 'jquery' ), alg_wc_ev()->version, true );
			wp_localize_script( 'alg-wc-ev-guest-verify', 'email_verification_options', $wc_ev_options );
		}

		/**
		 * verify_guest_at_checkout_script_footer.
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 * @return string
		 */
		function verify_guest_at_checkout_script_footer() {
			if (
				is_user_logged_in() ||
				'yes' !== get_option( 'alg_wc_ev_verify_guest_email', 'no' ) ||
				! is_checkout() ||
				is_wc_endpoint_url()
			) {
				return;
			}

			$verified_email = '';

			$args = wp_parse_args( null, array(
				'verify_code' => isset( $_GET[ alg_wc_ev_get_verification_param() ] ) ? $_GET[ alg_wc_ev_get_verification_param() ] : '',
				'directly'    => true
			) );

			if (
				! empty( $args['verify_code'] ) &&
				! empty( $verify_code = wc_clean( $args['verify_code'] ) ) &&
				! empty( $data = alg_wc_ev_decode_verify_code( array( 'verify_code' => $verify_code ) ) )
			) {
				$verified_email = isset( $data['id'] ) ? filter_var( $data['id'], FILTER_SANITIZE_EMAIL ) : '';
				// guest user verified by email
				if ( ! empty( $verified_email ) && filter_var( $verified_email, FILTER_VALIDATE_EMAIL ) && isset( $data['code'] ) ) {
					$verified_email = $verified_email;
				}
			}
			$php_to_js = array(
				'verified_email' => $verified_email
			);
			?>
			<script>
				jQuery( function ( $ ) {
					let dataFromPHP = <?php echo wp_json_encode( $php_to_js );?>;
					var billing_email_input = $( 'input[name="billing_email"]' );
					if ( billing_email_input.length ) {
						billing_email_input.val( dataFromPHP.verified_email )
					}
				} );
			</script>
			<style>
				.alg-wc-ev-guest-verify-button {
					color: #008000;
				}

				.alg-wc-ev-guest-verify-error-color {
					color: #ff0000;
				}
			</style>
			<?php
		}

		/**
		 * alg_wc_ev_send_guest_verification_email_action.
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 * @return string
		 */
		function alg_wc_ev_send_guest_verification_email_action( $param ) {

			// Check if the nonce is set and valid
			if ( 'yes' === get_option( 'alg_wc_ev_nonce_verify_guest_email', 'yes' ) ) {
				if ( ! isset( $_POST['security_nonce'] ) || ! wp_verify_nonce( $_POST['security_nonce'], 'alg_wc_ev_ajax_security_nonce' ) ) {
					echo "invalid_nonce";
					die;
				}
			}

			if (
				is_user_logged_in() ||
				'yes' !== get_option( 'alg_wc_ev_verify_guest_email', 'no' )
			) {
				return;
			}
			if (
				isset( $_POST['alg_wc_ev_email'] ) &&
				'' !== $_POST['alg_wc_ev_email'] &&
				filter_var( $_POST['alg_wc_ev_email'], FILTER_VALIDATE_EMAIL )
			) {
				$email = filter_var( $_POST['alg_wc_ev_email'], FILTER_SANITIZE_EMAIL );
				$send  = sanitize_text_field( $_POST['send'] );

				if ( email_exists( $email ) ) {
					echo "email_exists";
					die;
				}

				if ( $this->is_guest_email_already_verified( $email ) ) {
					echo "already_verified";
					die;
				} else {
					$this->send_guest_verification( $email, $send );
					echo "sent";
					die;
				}
			}
			echo "notsent";
			die;
		}

		/**
		 * checkout_validate_guest_email.
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 * @return string
		 */
		function checkout_validate_guest_email( $_posted ) {
			if (
				is_user_logged_in() ||
				'yes' !== get_option( 'alg_wc_ev_verify_guest_email', 'no' )
			) {
				return false;
			}

			if ( isset( $_posted['billing_email'] ) && ! empty( $_posted['billing_email'] ) ) {
				if ( ! $this->is_guest_email_already_verified( $_posted['billing_email'] ) ) {
					alg_wc_ev_add_notice( alg_wc_ev()->core->messages->get_guest_unverified_message(), 'error' );

					return false;
				}
			}

			return true;
		}

		/**
		 * checkout_validate_guest_email_message.
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 * @return string
		 */
		function checkout_validate_guest_email_message() {
			if (
				is_user_logged_in() ||
				'yes' !== get_option( 'alg_wc_ev_verify_guest_email', 'no' )
			) {
				return;
			}

			if ( is_checkout() && ! is_wc_endpoint_url() ) {
				$args = wp_parse_args( null, array(
					'verify_code' => isset( $_GET[ alg_wc_ev_get_verification_param() ] ) ? $_GET[ alg_wc_ev_get_verification_param() ] : '',
					'directly'    => true
				) );

				if (
					! empty( $args['verify_code'] ) &&
					! empty( $verify_code = wc_clean( $args['verify_code'] ) ) &&
					! empty( $data = alg_wc_ev_decode_verify_code( array( 'verify_code' => $verify_code ) ) )
				) {

					// guest user verified by email
					if ( isset( $data['id'] ) && filter_var( $data['id'], FILTER_VALIDATE_EMAIL ) && isset( $data['code'] ) ) {
						$is_guest_verified = $this->is_guest_user_verified_by_email( $data['id'], $data['code'] );

						if ( $is_guest_verified ) {
							alg_wc_ev_add_notice( alg_wc_ev()->core->messages->get_guest_verified_message( $data['id'] ), 'notice', $args );
						}
					}
				}
			}
		}

		/**
		 * is_guest_user_verified_by_email.
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 * @return string
		 */
		function is_guest_user_verified_by_email( $email, $code ) {
			global $wpdb;

			$table_name = sanitize_key( $wpdb->prefix . 'alg_wc_ev_guest_verify' );
			$email      = filter_var( $email, FILTER_SANITIZE_EMAIL );

			if (
				filter_var( $email, FILTER_VALIDATE_EMAIL ) &&
				$wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name
			) {
				$result_arr = $wpdb->get_row(
					$wpdb->prepare( "SELECT * FROM $table_name WHERE email = %s", $email )
				);

				if ( ! empty( $result_arr ) ) {
					$result_code = $result_arr->code;
					if ( $code === $result_code ) {
						$wpdb->update( $table_name, array( 'status' => '1' ), array( 'email' => $email ) );

						return true;
					}
				}
			}

			return false;
		}

		/**
		 * is_guest_email_already_verified.
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 * @return string
		 */
		function is_guest_email_already_verified( $email ) {
			global $wpdb;

			$table_name = sanitize_key( $wpdb->prefix . 'alg_wc_ev_guest_verify' );
			$email      = filter_var( $email, FILTER_SANITIZE_EMAIL );

			if (
				$wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name &&
				filter_var( $email, FILTER_VALIDATE_EMAIL )
			) {

				$result_arr = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM {$table_name} WHERE email = %s",
						$email // email as a string
					)
				);

				if ( ! empty( $result_arr ) ) {
					$result_status = $result_arr->status;

					if ( '1' == $result_status ) {
						return true;
					}
				}

			}

			return false;
		}

		/**
		 * send_guest_verification.
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 * @return string
		 */
		function send_guest_verification( $email, $send ) {
			global $wpdb;
			$code              = alg_wc_ev_generate_user_code();
			$table_name        = sanitize_key( $wpdb->prefix . 'alg_wc_ev_guest_verify' );
			$current_date_time = date( "Y-m-d H:i:s" );
			$email             = filter_var( $email, FILTER_SANITIZE_EMAIL );

			if (
				filter_var( $email, FILTER_VALIDATE_EMAIL ) &&
				$wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name
			){
				$result_arr = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM $table_name WHERE email = %s AND status = %d",
						$email,
						0
					)
				);

				$inser_new_row = true;

				if ( $send == 'resend' ) {
					$inser_new_row = true;
				}

				if ( ! empty( $result_arr ) ) {
					$result_time = $result_arr->date;

					$min_diff = (int) alg_wc_ev()->core->get_time_diff( $current_date_time, $result_time );
					if ( $min_diff <= 15 ) {
						$inser_new_row = false;
						$code          = $result_arr->code;
					}
				}

				if ( $inser_new_row ) {
					$wpdb->delete( $table_name, array( 'email' => $email ) );
					$wpdb->insert( $table_name, array(
						"code"   => $code,
						"email"  => $email,
						"status" => '0',
						"date"   => $current_date_time
					) );
					if ( $send != 'resend' ) {
						$this->send_guest_verify_email( $email, $code );
					}
				}

				if ( $send == 'resend' && ! $inser_new_row ) {
					$this->send_guest_verify_email( $email, $code );
				}
			}
		}

		/**
		 * sync verification if guest function.
		 *
		 * @see sync_verification_if_guest()
		 *
		 * @version 2.9.8
		 * @since   2.6.9
		 */
		function sync_verification_if_guest( $user_id ) {
			if (
				is_user_logged_in() ||
				'yes' !== get_option( 'alg_wc_ev_verify_guest_email', 'no' )
			) {
				return;
			}
			global $wpdb;
			$user_obj = get_user_by( 'id', $user_id );
			if ( $user_obj ) {
				$email      = $user_obj->user_email;
				$code       = '';
				$table_name = sanitize_key( $wpdb->prefix . 'alg_wc_ev_guest_verify' );

				if (
					$wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name &&
					filter_var( $email, FILTER_VALIDATE_EMAIL )
				) {
					$prepared_sql = $wpdb->prepare(
						"SELECT * FROM {$table_name} WHERE email = %s AND status = %d",
						$email,
						1       //
					);
					$result_arr   = $wpdb->get_row( $prepared_sql );
					if ( ! empty( $result_arr ) ) {
						$code = $result_arr->code;
					}
					if ( $this->is_guest_email_already_verified( $email ) && ! empty( $code ) ) {
						alg_wc_ev()->core->activate_user( array(
							'user_id'  => $user_id,
							'directly' => true,
							'code'     => $code
						) );
					}
				}
			}
		}

		/**
		 * send verify email function.
		 *
		 * @see send_guest_verify_email()
		 *
		 * @version 2.9.7
		 * @since   2.5.8
		 */
		function send_guest_verify_email( $email, $code ) {
			$email_content = alg_wc_ev()->core->emails->get_email_content( array(
				'user_id'                 => $email,
				'code'                    => $code,
				'context'                 => 'activation_email_separate',
				'verification_check_page' => 'checkout'
			) );

			$email_subject = alg_wc_ev()->core->emails->get_email_subject( array(
				'user_id' => $email,
				'context' => 'activation_email_separate',
				'subject' => '[%site_title%]: ' . __( 'Please activate your account', 'emails-verification-for-woocommerce' )
			) );

			// Send email.
			alg_wc_ev()->core->emails->send_mail( $email, $email_subject, $email_content );
		}

		/**
		 * create_guest_verification_table.
		 *
		 * @param $new_value, $old_value
		 *
		 * @return string
		 * @version 2.9.7
		 * @since   2.5.8
		 *
		 */
		function create_guest_verification_table( $new_value, $old_value ) {
			global $wpdb;
			$table_name      = sanitize_key( $wpdb->prefix . 'alg_wc_ev_guest_verify' );
			$charset_collate = $wpdb->get_charset_collate();

			if ( 'yes' === $new_value && $old_value !== $new_value ) {
				if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name ) {
					$sql = "CREATE TABLE $table_name (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					`email` tinytext NOT NULL,
					`code` text NOT NULL,
					`status` varchar(10) DEFAULT '0' NOT NULL,
					PRIMARY KEY  (`id`)
				) $charset_collate;";

					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
					dbDelta( $sql );
				}
			}

			return $new_value;
		}
	}
}