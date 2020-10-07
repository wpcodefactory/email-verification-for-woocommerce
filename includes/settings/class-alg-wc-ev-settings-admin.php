<?php
/**
 * Email Verification for WooCommerce - Admin Section Settings
 *
 * @version 1.9.6
 * @since   1.3.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Admin' ) ) :

class Alg_WC_Email_Verification_Settings_Admin extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function __construct() {
		$this->id   = 'admin';
		$this->desc = __( 'Admin', 'emails-verification-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.9.6
	 * @since   1.3.0
	 * @todo    [next] Delete users (automatically): better description
	 * @todo    [next] Email: better description(s) and default value(s)
	 * @todo    [next] Email: heading: placeholders
	 * @todo    (maybe) set `alg_wc_ev_admin_manual` default to `yes`
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Verified column', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'A new column displayed on the <a href="%s">users list page</a> with useful info about users verification.', 'emails-verification-for-woocommerce' ), admin_url( 'users.php' ) ),
				'id'       => 'alg_wc_ev_admin_column_options',
			),
			array(
				'title'    => __( 'Verified column', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Adds "Verified" column to the admin "Users" list.', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_admin_column',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Position', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'The position of the column.', 'emails-verification-for-woocommerce' ),
				'type'     => 'number',
				'id'       => 'alg_wc_ev_admin_column_position',
				'default'  => 5,
			),
			array(
				'title'    => __( 'Actions', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Adds links for manual email verification, unverification and email resend by admin.', 'emails-verification-for-woocommerce' ) . ' ' .
				              __( '"Verified" column must be enabled.', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_admin_manual',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_admin_column_options',
			),

			// Bulk user actions
			array(
				'title'    => __( 'Bulk user actions', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'New options displayed on the <a href="%s">users</a> bulk actions.', 'emails-verification-for-woocommerce' ), admin_url( 'users.php' ) ),
				'id'       => 'alg_wc_ev_admin_bulk_user_actions',
			),
			array(
				'title'    => __( 'Resend verification email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Resends the verification email for multiple users that are still unverified.', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_admin_bulk_user_actions_resend',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_admin_bulk_user_actions',
			),
		);
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Admin();
