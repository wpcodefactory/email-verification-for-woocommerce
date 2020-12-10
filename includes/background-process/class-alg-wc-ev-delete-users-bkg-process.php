<?php
/**
 * Email Verification for WooCommerce - Background Process - Delete users
 *
 * @version 2.0.1
 * @since   2.0.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Import_Tool_Bkg_Process' ) ) :

	class Alg_WC_Email_Verification_Import_Tool_Bkg_Process extends Alg_WC_Email_Verification_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'alg_wc_ev_delete_users_tool';

		/**
		 * @version 2.0.1
		 * @since   2.0.1
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Email Verification for WooCommerce - Delete users tool', 'emails-verification-for-woocommerce' );
		}

		/**
		 * @version 2.0.1
		 * @since   2.0.1
		 *
		 * @param mixed $item
		 *
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			//parent::task( $item );
			if ( ! function_exists( 'wp_delete_user' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/user.php' );
			}
			if ( wp_delete_user( $item['user_id'], $item['current_user_id'] ) ) {
				$logger = wc_get_logger();
				$logger->info( sprintf( __( 'User deleted: %d.', 'emails-verification-for-woocommerce' ), $item['user_id'] ), array( 'source' => $this->get_logger_context() ) );
			}
			return false;
		}

	}
endif;