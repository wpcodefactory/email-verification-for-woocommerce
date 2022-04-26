<?php
/**
 * Email Verification for WooCommerce - Background Process - Verify Users
 *
 * @version 2.3.3
 * @since   2.3.3
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Verify_Users' ) ) :

	class Alg_WC_Email_Verification_Verify_Users extends Alg_WC_Email_Verification_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'alg_wc_ev_verify_users';

		/**
		 * @version 2.3.3
		 * @since   2.3.3
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Email Verification for WooCommerce - Verify Users', 'emails-verification-for-woocommerce' );
		}

		/**
		 * @version 2.3.3
		 * @since   2.3.3
		 *
		 * @param mixed $item
		 *
		 * @return bool|mixed
		 */
		protected function task( $item ) {

			alg_wc_ev()->core->activate_user( array(
				'user_id'   => $item['user_id'],
				'directly'  => false
			) );

			$logger = wc_get_logger();
			$logger->info( sprintf( __( 'User verified manually: %d.', 'emails-verification-for-woocommerce' ), $item['user_id'] ), array( 'source' => $this->get_logger_context() ) );

			return false;
		}

	}
endif;