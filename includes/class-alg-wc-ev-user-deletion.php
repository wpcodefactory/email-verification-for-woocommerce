<?php
/**
 * Email Verification for WooCommerce - Users Deletion class.
 *
 * @version 2.8.0
 * @since   2.8.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Users_Deletion' ) ) {

	class Alg_WC_Email_Verification_Users_Deletion {

		/**
		 * delete_users_bkg_process.
		 *
		 * @since    2.6.2
		 *
		 * @var Alg_WC_Email_Verification_Delete_Tool_Bkg_Process
		 */
		public $delete_users_bkg_process;

		/**
		 * init.
		 *
		 * @version 2.8.0
		 * @since   2.8.0
		 *
		 * @return void
		 */
		function init() {
			// Bkg process.
			add_action( 'alg_wc_ev_core_loaded', array( $this, 'init_bkg_process' ) );

			// Admin delete unverified users.
			add_action( 'alg_wc_email_verification_after_save_settings', array( $this, 'maybe_delete_unverified_users' ) );

			// Admin delete unverified users: Cron.
			add_action( 'update_option_alg_wc_ev_delete_users_cron', array( $this, 'schedule_delete_unverified_users_cron_on_option_enabled' ), 10, 3 );
			if ( 'yes' === get_option( 'alg_wc_ev_delete_users_cron', 'no' ) ) {
				add_action( 'init', array( $this, 'schedule_delete_unverified_users_cron' ) );
				add_action( 'alg_wc_ev_delete_unverified_users', array( $this, 'delete_unverified_users_cron' ) );
			} else {
				add_action( 'init', array( $this, 'unschedule_delete_unverified_users_cron' ) );
			}
			add_action( 'alg_wc_ev_on_deactivation', array( $this, 'unschedule_delete_unverified_users_cron' ) );
		}

		/**
		 * Delete user.
		 *
		 * @version 2.8.0
		 * @since   2.8.0
		 *
		 * @param $args
		 *
		 * @return boolean
		 */
		function delete_user( $args = null ) {
			$args                = wp_parse_args( $args, array(
				'delete_from_network' => isset( $_POST['alg_wc_ev_delete_users_from_network'] ) ? true === filter_var( $_POST['alg_wc_ev_delete_users_from_network'], FILTER_VALIDATE_BOOLEAN ) : 'yes' === get_option( 'alg_wc_ev_delete_users_from_network', 'no' ),
				'log'                 => true,
				'user'                => '',
				'user_id'             => '',
				'reassign_user_id'    => null,
			) );
			$user                = is_a( $args['user'], 'WP_User' ) ? $args['user'] : '';
			$user                = empty( $user ) ? get_user_by( 'ID', intval( $args['user_id'] ) ) : $user;
			$reassign_user_id    = ! empty( $args['reassign_user_id'] ) ? intval( $args['reassign_user_id'] ) : null;
			$log                 = filter_var( $args['log'], FILTER_VALIDATE_BOOLEAN );
			$delete_from_network = filter_var( $args['delete_from_network'], FILTER_VALIDATE_BOOLEAN );
			if ( is_a( $user, 'WP_User' ) ) {
				if ( $delete_from_network ) {
					if ( ! function_exists( 'wpmu_delete_user' ) ) {
						require_once ABSPATH . '/wp-admin/includes/ms.php';
					}
					wpmu_delete_user( $user->ID );
				} else {
					wp_delete_user( $user->ID, $reassign_user_id );
				}
				if ( $log ) {
					$logger = wc_get_logger();
					$logger->info( sprintf( __( 'User deleted: (ID: %d, Email: %s).', 'emails-verification-for-woocommerce' ), $user->ID, $user->user_email ), array( 'source' => 'alg_wc_ev_delete_users_tool' ) );
				}

				return true;
			}

			return false;
		}

		/**
		 * init_bkg_process.
		 *
		 * @version 2.8.0
		 * @since   2.8.0
		 */
		function init_bkg_process() {
			require_once( alg_wc_ev()->plugin_path() . '/includes/background-process/class-alg-wc-ev-delete-users-bkg-process.php' );
			$this->delete_users_bkg_process = new Alg_WC_Email_Verification_Delete_Tool_Bkg_Process();
		}

		/**
		 * delete_unverified_users.
		 *
		 * @version 2.7.9
		 * @since   1.3.0
		 * @todo    add "preview"
		 */
		function delete_unverified_users( $is_cron = false ) {
			$current_user_id = ( function_exists( 'get_current_user_id' ) && 0 != get_current_user_id() ? get_current_user_id() : null );
			// Args
			$args = array(
				'role__not_in' => get_option( 'alg_wc_ev_skip_user_roles', array( 'administrator' ) ),
				'exclude'      => ( $current_user_id ? array( $current_user_id ) : array() ),
				'meta_query'   => array(
					array(
						'key'     => 'alg_wc_ev_is_activated',
						'value'   => '1',
						'compare' => '!=',
					),
				),
			);
			if ( 'yes' === get_option( 'alg_wc_ev_verify_already_registered', 'no' ) ) {
				$args['meta_query']['relation'] = 'OR';
				$args['meta_query'][]           = array(
					'key'     => 'alg_wc_ev_is_activated',
					'value'   => 'alg_wc_ev_is_activated', // this is ignored; needed for WP prior to v3.9, see https://core.trac.wordpress.org/ticket/23268
					'compare' => 'NOT EXISTS',
				);
			}
			$args = apply_filters( 'alg_wc_ev_delete_unverified_users_loop_args', $args, $current_user_id, $is_cron );
			// Loop
			$total                  = 0;
			$users_query            = get_users( $args );
			$bkg_process_min_amount = get_option( 'alg_wc_ev_bkg_process_min_amount', 20 );
			$perform_bkg_process    = $is_cron || count( $users_query ) >= $bkg_process_min_amount;
			if ( $perform_bkg_process ) {
				$this->delete_users_bkg_process->cancel_process();
				foreach ( $users_query as $user ) {
					$total ++;
					$this->delete_users_bkg_process->push_to_queue( array( 'user_id' => $user->ID, 'current_user_id' => $current_user_id ) );
				}
				$this->delete_users_bkg_process->save()->dispatch();
				if ( ! $is_cron ) {
					$message = sprintf( __( '%d unverified users are going to be deleted in background processing.', 'emails-verification-for-woocommerce' ), $total );
					if ( ! empty( $complete_bkg_task_msg_regarding_email = alg_wc_ev_get_complete_bkg_task_msg_regarding_email() ) ) {
						$message .= ' ' . $complete_bkg_task_msg_regarding_email;
					}
					WC_Admin_Settings::add_message( $message );
				}
			} else {
				foreach ( $users_query as $user ) {
					$this->delete_user( array(
						'user'             => $user,
						'reassign_user_id' => $current_user_id
					) );
					$total ++;
				}
				WC_Admin_Settings::add_message( sprintf( __( 'Total unverified users deleted: %d.', 'emails-verification-for-woocommerce' ), $total ) );
			}
		}

		/**
		 * delete_unverified_users_cron.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 */
		function delete_unverified_users_cron() {
			$this->delete_unverified_users( true );
		}

		/**
		 * schedule_delete_unverified_users_cron.
		 *
		 * @version 2.0.0
		 * @since   1.7.0
		 */
		function schedule_delete_unverified_users_cron() {
			if ( ! wp_next_scheduled( 'alg_wc_ev_delete_unverified_users' ) ) {
				wp_schedule_event( time(), get_option( 'alg_wc_ev_delete_users_cron_frequency', 'weekly' ), 'alg_wc_ev_delete_unverified_users' );
			}
		}

		/**
		 * unschedule_delete_unverified_users_cron.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 */
		function unschedule_delete_unverified_users_cron() {
			if ( $time = wp_next_scheduled( 'alg_wc_ev_delete_unverified_users' ) ) {
				wp_unschedule_event( $time, 'alg_wc_ev_delete_unverified_users' );
			}
		}

		/**
		 * schedule_delete_unverified_users_cron_on_option_enabled.
		 *
		 * @version 2.0.1
		 * @since   2.0.1
		 *
		 * @param $option
		 *
		 * @param $old_value
		 * @param $value
		 */
		function schedule_delete_unverified_users_cron_on_option_enabled( $old_value, $value, $option ) {
			if ( 'yes' === $value ) {
				$this->schedule_delete_unverified_users_cron();
			}
		}

		/**
		 * maybe_delete_unverified_users.
		 *
		 * @version 1.3.0
		 * @since   1.3.0
		 */
		function maybe_delete_unverified_users() {
			if ( 'yes' === get_option( 'alg_wc_ev_delete_users', 'no' ) ) {
				update_option( 'alg_wc_ev_delete_users', 'no' );
				add_action( 'admin_init', array( $this, 'delete_unverified_users' ) );
			}
		}
	}
}