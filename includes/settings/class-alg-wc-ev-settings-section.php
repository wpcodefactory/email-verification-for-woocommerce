<?php
/**
 * Email Verification for WooCommerce - Section Settings.
 *
 * @version 2.2.7
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Section' ) ) :

class Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_ev',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_ev_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	function get_block_unverify_login_option_warning( $args = null ) {
		$args = wp_parse_args( $args, array(
			'enabled' => false
		) );
		$option_status = $args['enabled'] ? __( 'enabled', 'emails-verification-for-woocommerce' ) : __( 'disabled', 'emails-verification-for-woocommerce' );
		return sprintf( __( 'This will probably make more sense with the %s option %s, where the account verification becomes optional.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Block unverified login', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . $option_status . '</strong>' );
	}

	/**
	 * get_paid_statuses_msg.
	 *
	 * @version 2.2.4
	 * @since   2.2.4
	 *
	 * @return string
	 */
	function get_paid_statuses_msg() {
		return __( 'Paid statuses:', 'emails-verification-for-woocommerce' ) . ' ' . alg_wc_ev_array_to_string( wc_get_is_paid_statuses(), array( 'glue' => ', ', 'item_template' => '<code>{value}</code>' ) );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * available_placeholders_desc.
	 *
	 * @version 1.5.0
	 * @since   1.3.1
	 */
	function available_placeholders_desc( $placeholders ) {
		return sprintf( __( 'Available placeholder(s): %s', 'emails-verification-for-woocommerce' ),
			'<code>' . implode( '</code>, <code>', $placeholders ) . '</code>' );
	}

	/**
	 * pro_msg.
	 *
	 * @version 1.9.3
	 * @since   1.5.0
	 */
	function pro_msg( $before = '<br>', $message = 'You will need %s plugin to enable this option.', $after = '') {
		return apply_filters( 'alg_wc_ev_settings', $before . sprintf( $message,
			'<a target="_blank" href="https://wpfactory.com/item/email-verification-for-woocommerce/">' . 'Email Verification for WooCommerce Pro' . '</a>' ) . $after );
	}

	/**
	 * separate_email_option_msg.
	 *
	 * @version 2.2.7
	 * @since   1.8.0
	 *
	 * @param string $requirement 'enabled' | 'disabled'
	 *
	 * @return string
	 */
	function separate_email_option_msg( $requirement = 'enabled' ) {
		$translation = array(
			'enabled'  => __( 'enabled', 'emails-verification-for-woocommerce' ),
			'disabled' => __( 'disabled', 'emails-verification-for-woocommerce' ),
		);
		return sprintf( __( 'The option "%s > %s" needs to be %s.', 'emails-verification-for-woocommerce' ),
			'<strong>' . __( 'Email', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Send as a separate email', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . $translation[ $requirement ] . '</strong>' );
	}

	/**
	 * get_user_roles_options.
	 *
	 * @version 2.1.1
	 * @since   1.0.0
	 */
	function get_user_roles_options() {
		global $wp_roles;
		$roles = apply_filters( 'editable_roles', ( isset( $wp_roles ) && is_object( $wp_roles ) ? $wp_roles->roles : array() ) );
		return wp_list_pluck( $roles, 'name' );
	}

}

endif;
