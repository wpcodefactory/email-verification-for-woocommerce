<?php
/**
 * Email Verification for WooCommerce - Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings' ) ) :

class Alg_WC_Email_Verification_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_ev';
		$this->label = __( 'Email Verification', 'emails-verification-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_raw_parameter' ), 10, 3 );
		// Sections
		require_once( 'class-alg-wc-ev-settings-section.php' );
		require_once( 'class-alg-wc-ev-settings-general.php' );
		require_once( 'class-alg-wc-ev-settings-messages.php' );
		require_once( 'class-alg-wc-ev-settings-emails.php' );
		require_once( 'class-alg-wc-ev-settings-admin.php' );
		require_once( 'class-alg-wc-ev-settings-advanced.php' );
	}

	/**
	 * maybe_unsanitize_option.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 * @todo    find better solution
	 *
	 * @param $value
	 * @param $option
	 * @param $raw_value
	 *
	 * @return mixed|string
	 */
	function sanitize_raw_parameter( $value, $option, $raw_value ) {
		if ( ! isset( $option['alg_wc_ev_raw'] ) || empty( $option['alg_wc_ev_raw'] ) ) {
			return $value;
		}
		$new_value = wp_kses_post( trim( $raw_value ) );
		return $new_value;
	}

	/**
	 * get_settings.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'emails-verification-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'emails-verification-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'emails-verification-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'emails-verification-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( 'Your settings have been reset.', 'emails-verification-for-woocommerce' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
			}
		}
	}

	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 1.1.1
	 * @since   1.1.0
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'emails-verification-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		do_action( 'alg_wc_email_verification_after_save_settings' );
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_Email_Verification_Settings();
