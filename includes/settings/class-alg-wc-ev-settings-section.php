<?php
/**
 * Email Verification for WooCommerce - Section Settings
 *
 * @version 2.0.4
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
	 * @version 2.0.4
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
		return sprintf( __( 'This requires option "%s > %s" to be %s.', 'emails-verification-for-woocommerce' ),
			__( 'General', 'emails-verification-for-woocommerce' ), __( 'Send as a separate email', 'emails-verification-for-woocommerce' ), '<strong>' . $translation[ $requirement ] . '</strong>' );
	}

}

endif;
