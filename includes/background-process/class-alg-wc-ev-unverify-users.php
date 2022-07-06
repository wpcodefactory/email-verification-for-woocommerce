<?php
/**
 * Email Verification for WooCommerce - Background Process - Unverify Users
 *
 * @version 2.3.8
 * @since   2.3.8
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Email_Verification_Unverify_Users' ) ) :
	class Alg_WC_Email_Verification_Unverify_Users extends Alg_WC_Email_Verification_Bkg_Process {
		/**
		 * @var string
		 */
		protected $action = 'alg_wc_ev_unverify_users';

		/**
		 * @version 2.3.8
		 * @since   2.3.8
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Email Verification for WooCommerce - Unverify Users', 'emails-verification-for-woocommerce' );
		}

		/**
		 * @version 2.3.8
		 * @since   2.3.8
		 *
		 * @param mixed $item
		 *
		 * @return bool
		 */
		protected function task( $item ) {

			alg_wc_ev()->core->deactivate_user( array(
				'user_id'   => $item['user_id'],
			) );

			$logger = wc_get_logger();
			$logger->info( sprintf( __( 'User unverified manually: %d.', 'emails-verification-for-woocommerce' ), $item['user_id'] ), array( 'source' => $this->get_logger_context() ) );

			return false;
		}

	}
endif;