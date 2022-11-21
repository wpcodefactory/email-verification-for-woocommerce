<?php
/**
 * Email Verification for WooCommerce - Admin Section Settings.
 *
 * @version 2.3.8
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
	 * get_allowed_user_roles_option.
	 *
	 * @version 2.3.3
	 * @since   2.2.0
	 *
	 * @return array
	 */
	function get_available_user_roles_option() {
		return wp_roles()->get_names();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.8
	 * @since   1.3.0
	 * @todo    [next] Delete users (automatically): better description
	 * @todo    [next] Email: better description(s) and default value(s)
	 * @todo    [next] Email: heading: placeholders
	 * @todo    (maybe) set `alg_wc_ev_admin_manual` default to `yes`
	 */
	function get_settings() {
		return array(
			// Admin options
			array(
				'title'    => __( 'Admin options', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_admin_options',
			),
			array(
				'title'    => __( 'Allowed user roles', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'User roles allowed to see and interact with the admin interface of the Email Verification plugin.', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'The administrator can\'t be removed.', 'emails-verification-for-woocommerce' ).'<br />'.
				              __( 'Leave it empty to allow all user roles to access the plugin\'s settings.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_admin_allowed_user_roles',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $this->get_available_user_roles_option(),
			),
			array(
				'title'    => __( 'Users filter', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Add a dropdown on the users page allowing to filter verified/unverified users.', 'emails-verification-for-woocommerce' ),
				'id'       => 'alg_wc_ev_admin_users_filter',
				'type'     => 'checkbox',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_admin_options',
			),
			// Column options
			array(
				'title'    => __( 'Verified column', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'A new column displayed on the <a href="%s">users list page</a> with useful info about users verification.', 'emails-verification-for-woocommerce' ), admin_url( 'users.php' ) ),
				'id'       => 'alg_wc_ev_admin_column_options',
			),
			array(
				'title'    => __( 'Verified column', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Add "Verified" column to the admin "Users" list', 'emails-verification-for-woocommerce' ),
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
				'desc_tip' => sprintf( __( '%s option must be enabled.', 'emails-verification-for-woocommerce' ), '"' . __( 'Verified column', 'emails-verification-for-woocommerce' ) . '"' ),
				'desc'     => __( 'Add links for manual email verification, unverification and email resend by admin', 'emails-verification-for-woocommerce' ),
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
				'desc'     => sprintf( __( 'Add %s bulk option', 'emails-verification-for-woocommerce' ), '"' . __( 'Resend verification email', 'emails-verification-for-woocommerce' ) . '"' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_admin_bulk_user_actions_resend',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Verify users', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Add %s bulk option', 'emails-verification-for-woocommerce' ), '"' . __( 'Verify users', 'emails-verification-for-woocommerce' ) . '"' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_admin_bulk_verify_users',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Unverify users', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( 'Add %s bulk option', 'emails-verification-for-woocommerce' ), '"' . __( 'Unverify users', 'emails-verification-for-woocommerce' ) . '"' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_admin_bulk_unverify_users',
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
