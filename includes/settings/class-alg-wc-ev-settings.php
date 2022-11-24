<?php
/**
 * Email Verification for WooCommerce - Settings.
 *
 * @version 2.4.7
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings' ) ) :

class Alg_WC_Email_Verification_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.4.7
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_ev';
		$this->label = __( 'Email Verification', 'emails-verification-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_raw_parameter' ), 10, 3 );
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_textarea_url_parameter' ), 10, 3 );
		// Sections
		require_once( 'class-alg-wc-ev-settings-section.php' );
		require_once( 'class-alg-wc-ev-settings-general.php' );
		require_once( 'class-alg-wc-ev-settings-email.php' );
		require_once( 'class-alg-wc-ev-settings-blocking.php' );
		require_once( 'class-alg-wc-ev-settings-admin.php' );
		require_once( 'class-alg-wc-ev-settings-messages.php' );
		require_once( 'class-alg-wc-ev-settings-advanced.php' );
		require_once( 'class-alg-wc-ev-settings-compatibility.php' );
		// Create notice about pro
		add_action( 'admin_init', array( $this, 'add_promoting_notice' ) );
		// Sanitize "admin user roles" option.
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . 'alg_wc_ev_admin_allowed_user_roles', array( $this, 'append_administrator_to_admin_user_roles_option' ), 10, 3 );
	}

	/**
	 * Append 'administrator' to "admin user roles" option.
	 *
	 * @version 2.3.3
	 * @since   2.2.0
	 *
	 * @param $value
	 * @param $option
	 * @param $raw_value
	 *
	 * @return array
	 */
	function append_administrator_to_admin_user_roles_option( $value, $option, $raw_value ) {
		if (
			! empty( $value ) &&
			! in_array( 'administrator', $value )
		) {
			$value[] = 'administrator';
		}
		return $value;
	}

	/**
	 * add_promoting_notice.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function add_promoting_notice() {
		$promoting_notice = wpfactory_promoting_notice();
		$promoting_notice->set_args( array(
			'url_requirements'              => array(
				'page_filename' => 'admin.php',
				'params'        => array( 'page' => 'wc-settings', 'tab' => $this->id ),
			),
			'enable'                        => true === apply_filters( 'alg_wc_ev_settings', true ),
			'optimize_plugin_icon_contrast' => true,
			'template_variables'            => array(
				'%pro_version_url%'    => 'https://wpfactory.com/item/email-verification-for-woocommerce/',
				'%plugin_icon_url%'    => 'https://ps.w.org/emails-verification-for-woocommerce/assets/icon-128x128.png',
				'%pro_version_title%'  => __( 'Email Verification for WooCommerce Pro', 'emails-verification-for-woocommerce' ),
				'%main_text%'          => __( 'Disabled options can be unlocked using <a href="%pro_version_url%" target="_blank"><strong>%pro_version_title%</strong></a>', 'emails-verification-for-woocommerce' ),
				'%btn_call_to_action%' => __( 'Upgrade to Pro version', 'emails-verification-for-woocommerce' ),
			),
		) );
		$promoting_notice->init();
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
	 * sanitize_url_parameter.
	 *
	 * @version 2.4.7
	 * @since   2.4.7
	 *
	 * @param $value
	 * @param $option
	 * @param $raw_value
	 *
	 * @return string
	 */
	function sanitize_textarea_url_parameter( $value, $option, $raw_value ) {
		if ( ! isset( $option['alg_wc_ev_textarea_url'] ) || empty( $option['alg_wc_ev_textarea_url'] ) || empty( $raw_value ) ) {
			return $value;
		}
		$sanitized_urls = array();
		foreach ( explode( PHP_EOL, $raw_value ) as $url ) {
			if ( ! empty( $url ) ) {
				$url_sanitized    = sanitize_url( wp_strip_all_tags( trim( $url ) ) );
				$sanitized_urls[] = preg_replace( '/(?<!\/)\?/', '/?', $url_sanitized );
			}
		}
		return implode( PHP_EOL, array_unique( $sanitized_urls ) );
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
	 * @version 2.4.0
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
			do_action( 'alg_wc_email_verification_after_reset_settings' );
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
