<?php
/**
 * Email Verification for WooCommerce - Admin Class
 *
 * @version 2.0.1
 * @since   1.5.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Admin' ) ) :

class Alg_WC_Email_Verification_Admin {

	/**
	 * Constructor.
	 *
	 * @version 2.0.1
	 * @since   1.5.0
	 * @todo    (maybe) move more stuff here, e.g. settings, action links etc.
	 */
	function __construct() {
		// Admin column
		if ( 'yes' === get_option( 'alg_wc_ev_admin_column', 'yes' ) ) {
			add_filter( 'manage_users_columns',       array( $this, 'add_verified_email_column' ) );
			add_filter( 'manage_users_custom_column', array( $this, 'render_verified_email_column' ), PHP_INT_MAX, 3 );
			add_action( 'admin_enqueue_scripts',      array( $this, 'users_page_custom_style' ) );
			if ( $this->is_admin_manual_actions = ( 'yes' === get_option( 'alg_wc_ev_admin_manual', 'no' ) ) ) {
				$this->actions = array(
					'alg_wc_ev_admin_verify',
					'alg_wc_ev_admin_unverify',
					'alg_wc_ev_admin_resend',
					'alg_wc_ev_admin_verify_done',
					'alg_wc_ev_admin_unverify_done',
					'alg_wc_ev_admin_resend_done',
					'_alg_wc_ev_wpnonce',
				);
				add_action( 'admin_init', array( $this, 'admin_manual_actions' ) );
			}
		}
		// Admin delete unverified users
		add_action( 'alg_wc_email_verification_after_save_settings', array( $this, 'maybe_delete_unverified_users' ) );
		// Admin delete unverified users: Cron
		add_action( 'update_option_alg_wc_ev_delete_users_cron', array( $this, 'schedule_delete_unverified_users_cron_on_option_enabled' ), 10, 3 );
		if ( 'yes' === get_option( 'alg_wc_ev_delete_users_cron', 'no' ) ) {
			add_action( 'init',                               array( $this, 'schedule_delete_unverified_users_cron' ) );
			add_action( 'alg_wc_ev_delete_unverified_users',  array( $this, 'delete_unverified_users_cron' ) );
		} else {
			add_action( 'init',                               array( $this, 'unschedule_delete_unverified_users_cron' ) );
		}
		add_action( 'init',                                   array( $this, 'unschedule_delete_unverified_users_cron_on_deactivation' ) );
		// Users Bulk Actions
		add_filter( 'bulk_actions-users',        array( $this, 'add_bulk_user_actions' ) );
		add_filter( 'handle_bulk_actions-users', array( $this, 'handle_bulk_user_actions' ), 10, 3 );
		add_action( 'admin_notices',             array( $this, 'manage_bulk_notices' ) );
		// Bkg Process
		add_action( 'plugins_loaded',            array( $this, 'init_bkg_process' ) );
	}

	/**
	 * init_bkg_process.
	 *
	 * @version 2.0.1
	 * @since   2.0.1
	 */
	function init_bkg_process() {
		require_once( alg_wc_ev()->plugin_path() . '/includes/background-process/class-alg-wc-ev-delete-users-bkg-process.php' );
		$this->delete_users_bkg_process = new Alg_WC_Email_Verification_Import_Tool_Bkg_Process();
	}

	/**
	 * schedule_delete_unverified_users_cron_on_option_enabled.
	 *
	 * @version 2.0.1
	 * @since   2.0.1
	 *
	 * @param $old_value
	 * @param $value
	 * @param $option
	 */
	function schedule_delete_unverified_users_cron_on_option_enabled( $old_value, $value, $option ) {
		if ( 'yes' === $value ) {
			$this->schedule_delete_unverified_users_cron();
		}
	}

	/**
	 * manage_bulk_notices.
	 *
	 * @version 1.9.6
	 * @since   1.9.6
	 */
	function manage_bulk_notices() {
		if ( ! empty( $_REQUEST['bulk_alg_wc_ev_resend'] ) ) {
			$count = intval( $_REQUEST['bulk_alg_wc_ev_resend'] );
			printf( '<div id="message" class="updated notice is-dismissable"><p>' . _n( 'Verification email sent to %d user successfully.', 'Verification email sent to %d users successfully.', $count, 'emails-verification-for-woocommerce' ) . '</p></div>', $count );
		}
	}

	/**
	 * handle_bulk_actions_users.
	 *
	 * @version 1.9.6
	 * @since   1.9.6
	 *
	 * @param $redirect_to
	 * @param $doaction
	 * @param $user_ids
	 *
	 * @return string
	 */
	function handle_bulk_user_actions( $redirect_to, $doaction, $user_ids ) {
		switch ( $doaction ) {
			case 'alg_wc_ev_resend':
				$count = 0;
				foreach ( $user_ids as $user_id ) {
					$user = new WP_User( $user_id );
					if ( $user && ! is_wp_error( $user ) ) {
						if ( ! alg_wc_ev()->core->is_user_verified( $user ) ) {
							$count ++;
							alg_wc_ev()->core->emails->reset_and_mail_activation_link( $user_id );
						}
					}
				}
				$redirect_to = add_query_arg( 'bulk_alg_wc_ev_resend', $count, $redirect_to );
				return $redirect_to;
				break;
			default:
				return $redirect_to;
		}
		return $redirect_to;
	}

	/**
	 * add_users_bulk_actions.
	 *
	 * @version 1.9.6
	 * @since   1.9.6
	 *
	 * @param $bulk_actions
	 *
	 * @return mixed
	 */
	function add_bulk_user_actions( $bulk_actions ) {
		if ( 'yes' !== get_option( 'alg_wc_ev_admin_bulk_user_actions_resend' ) ) {
			return $bulk_actions;
		}
		$bulk_actions['alg_wc_ev_resend'] = __( 'Resend verification email', 'emails-verification-for-woocommerce' );
		return $bulk_actions;
	}

	/**
	 * users_page_custom_style.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 */
	function users_page_custom_style( $hook_suffix ) {
		if ( 'users.php' != $hook_suffix ) {
			return;
		}
		?>
		<style>
			.column-alg_wc_ev {
				width:14%;
			}

			.alg_wc_ev.column-alg_wc_ev .info {
				cursor: help;
				color: #666;
				display:inline-block;
				transform:scale(1.2);
				vertical-align: middle;
				position: relative;
				top: -1px;
			}

			.alg_wc_ev.column-alg_wc_ev .verified:hover span {
				color: #008A05;
			}

			.alg_wc_ev.column-alg_wc_ev .not-verified:hover span {
				color: #ce0000;
			}
		</style>
		<?php
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
	 * unschedule_delete_unverified_users_cron_on_deactivation.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function unschedule_delete_unverified_users_cron_on_deactivation() {
		register_deactivation_hook( alg_wc_ev()->plugin_file(), array( $this, 'unschedule_delete_unverified_users_cron' ) );
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

	/**
	 * delete_unverified_users.
	 *
	 * @version 2.0.1
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
				WC_Admin_Settings::add_message( sprintf( __( '%d unverified users are going to be deleted in background processing. When the task is complete an e-mail is going to be sent to %s', 'emails-verification-for-woocommerce' ), $total, get_option( 'alg_wc_ev_bkg_process_email_to', get_option( 'admin_email' ) ) ) );
			}
		} else {
			foreach ( $users_query as $user ) {
				if ( wp_delete_user( $user->ID, $current_user_id ) ) {
					$total ++;
				}
			}
			WC_Admin_Settings::add_message( sprintf( __( 'Total unverified users deleted: %d.', 'emails-verification-for-woocommerce' ), $total ) );
		}
	}

	/**
	 * get_user_id_from_action.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function get_user_id_from_action( $action, $do_check_nonce = true ) {
		if ( ! empty( $_GET[ $action ] ) && ( $user_id = intval( $_GET[ $action ] ) ) > 0 && current_user_can( 'manage_woocommerce' ) ) {
			if ( ! $do_check_nonce || ( isset( $_GET['_alg_wc_ev_wpnonce'] ) && wp_verify_nonce( $_GET['_alg_wc_ev_wpnonce'], 'alg_wc_ev_action' ) ) ) {
				return $user_id;
			} else {
				wp_die( __( 'Nonce not found or not verified.', 'emails-verification-for-woocommerce' ) );
			}
		}
		return false;
	}

	/**
	 * admin_manual_actions.
	 *
	 * @version 1.8.0
	 * @since   1.1.0
	 * @todo    [next] (maybe) new action: "expire" (i.e. make link expired) (i.e. remove `alg_wc_ev_activation_code_time` meta)
	 */
	function admin_manual_actions() {
		// Actions
		if ( $user_id = $this->get_user_id_from_action( 'alg_wc_ev_admin_verify' ) ) {
			update_user_meta( $user_id, 'alg_wc_ev_is_activated', '1' );
			wp_safe_redirect( add_query_arg( 'alg_wc_ev_admin_verify_done', $user_id, remove_query_arg( $this->actions ) ) );
			exit;
		}
		if ( $user_id = $this->get_user_id_from_action( 'alg_wc_ev_admin_unverify' ) ) {
			update_user_meta( $user_id, 'alg_wc_ev_is_activated', '0' );
			delete_user_meta( $user_id, 'alg_wc_ev_customer_new_account_email_sent' );
			delete_user_meta( $user_id, 'alg_wc_ev_admin_email_sent' );
			wp_safe_redirect( add_query_arg( 'alg_wc_ev_admin_unverify_done', $user_id, remove_query_arg( $this->actions ) ) );
			exit;
		}
		if ( $user_id = $this->get_user_id_from_action( 'alg_wc_ev_admin_resend' ) ) {
			alg_wc_ev()->core->emails->reset_and_mail_activation_link( $user_id );
			wp_safe_redirect( add_query_arg( 'alg_wc_ev_admin_resend_done', $user_id, remove_query_arg( $this->actions ) ) );
			exit;
		}
		// Notices
		if ( $user_id = $this->get_user_id_from_action( 'alg_wc_ev_admin_verify_done', false ) ) {
			$this->admin_notice = sprintf( __( 'User %s: verified.', 'emails-verification-for-woocommerce' ), '<code>ID: ' . $user_id . '</code>' );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
		if ( $user_id = $this->get_user_id_from_action( 'alg_wc_ev_admin_unverify_done', false ) ) {
			$this->admin_notice = sprintf( __( 'User %s: unverified.', 'emails-verification-for-woocommerce' ), '<code>ID: ' . $user_id . '</code>' );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
		if ( $user_id = $this->get_user_id_from_action( 'alg_wc_ev_admin_resend_done', false ) ) {
			$this->admin_notice = sprintf( __( 'User %s: activation link resent.', 'emails-verification-for-woocommerce' ), '<code>ID: ' . $user_id . '</code>' );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	/**
	 * admin_notices.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function admin_notices() {
		if ( isset( $this->admin_notice ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . $this->admin_notice . '</p></div>';
		}
	}

	/**
	 * add_verified_email_column.
	 *
	 * @version 1.9.5
	 * @since   1.0.0
	 * @todo    new column: "expire"
	 * @todo    (maybe) add option to rename the column(s)
	 */
	function add_verified_email_column( $columns ) {
		$position = get_option( 'alg_wc_ev_admin_column_position', 5 );
		$columns  = array_slice( $columns, 0, $position, true ) +
		            array( 'alg_wc_ev' => __( 'Verified', 'emails-verification-for-woocommerce' ) ) +
		            array_slice( $columns, $position, count( $columns ) - 1, true );
		return $columns;
	}

	/**
	 * get_admin_action_html.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 */
	function get_admin_action_html( $action, $title, $user_id ) {
		$link = wp_nonce_url( add_query_arg( 'alg_wc_ev_admin_' . $action, $user_id, remove_query_arg( $this->actions ) ), 'alg_wc_ev_action', '_alg_wc_ev_wpnonce' );
		return '<a href="' . $link . '"' . ' onclick="return confirm(\'' . __( 'Are you sure?', 'emails-verification-for-woocommerce' ) . '\')">' . $title . '</a>';
	}

	/**
	 * render_verified_email_column.
	 *
	 * @version 1.9.5
	 * @since   1.0.0
	 */
	function render_verified_email_column( $output, $column_name, $user_id ) {
		if ( 'alg_wc_ev' === $column_name && $user_id ) {
			$user = new WP_User( $user_id );
			if ( $user && ! is_wp_error( $user ) ) {
				$output = '';
				if ( ! alg_wc_ev()->core->is_user_verified( $user ) ) {
					$activation_code_time = get_user_meta( $user_id, 'alg_wc_ev_activation_code_time', true );
					if ( $activation_code_time ) {
						$activation_code_time = ' (' . date( 'Y-m-d H:i:s', $activation_code_time ) . ')';
					}
					$output .= '<span class="info not-verified" title="' . __( 'Email not verified', 'emails-verification-for-woocommerce' ) . $activation_code_time . '"><span class="dashicons-before dashicons-no-alt"></span></span>';
					if ( $this->is_admin_manual_actions ) {
						$output .= '<div class="row-actions"><span class="edit">';
						$output .= ' ' . $this->get_admin_action_html( 'verify', __( 'Verify', 'emails-verification-for-woocommerce' ), $user_id );
						$output .= ' | ';
						$output .= ' ' . $this->get_admin_action_html( 'resend', __( 'Resend', 'emails-verification-for-woocommerce' ), $user_id );
						$output .= '</span></div>';
					}
				} else {
					$output .= '<span class="info verified" title="' . __( 'Email verified', 'emails-verification-for-woocommerce' ) . '"><span class="dashicons-before dashicons-yes"></span></span>';
					if ( $this->is_admin_manual_actions && ! alg_wc_ev()->core->is_user_role_skipped( $user ) && ! apply_filters( 'alg_wc_ev_is_user_verified', false, $user_id ) ) {
						$output .= '<div class="row-actions"><span class="edit">';
						$output .= ' ' . $this->get_admin_action_html( 'unverify', __( 'Unverify', 'emails-verification-for-woocommerce' ), $user_id );
						$output .= '</span></div>';
					}
				}
			}
		}
		return $output;
	}

}

endif;

return new Alg_WC_Email_Verification_Admin();
