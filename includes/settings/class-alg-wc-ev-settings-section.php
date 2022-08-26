<?php
/**
 * Email Verification for WooCommerce - Section Settings.
 *
 * @version 2.4.0
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

	/**
	 * set_red_border_if_empty.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 *
	 * @param $option_id
	 *
	 * @param string $default_option
	 *
	 * @return string
	 */
	function set_red_border_if_empty( $option_id, $default_option = '' ) {
		$css = '';
		if ( empty( get_option( $option_id, $default_option ) ) ) {
			$css .= 'border:1px solid red;';
		}
		return $css;
	}

	/**
	 * get_empty_warning_msg.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 *
	 * @param null $args
	 *
	 * @return string
	 */
	function get_empty_warning_msg( $args = null ) {
		$args           = wp_parse_args( $args, array(
			'option_id'   => '',
			'default_opt' => '',
			'msg'         => '',
			'style'       => 'color:red',
		) );
		$option_id      = $args['option_id'];
		$default_option = $args['default_opt'];
		$msg            = $args['msg'];
		$style          = $args['style'];
		$final_msg      = '';
		if ( empty( get_option( $option_id, $default_option ) ) ) {
			$msg_content = empty( $msg ) ? __( 'Please, do not leave the option empty.', 'emails-verification-for-woocommerce' ) : $msg;
			$final_msg   = '<span style="' . $style . '">' . ' ' . $msg_content . '</span>';
		}
		return $final_msg;
	}

}

endif;
