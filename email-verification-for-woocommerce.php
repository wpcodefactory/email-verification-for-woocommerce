<?php
/*
Plugin Name: Email Verification for WooCommerce
Plugin URI: https://wpfactory.com/item/email-verification-for-woocommerce/
Description: Verify user emails in WooCommerce. Beautifully.
Version: 2.6.3
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: emails-verification-for-woocommerce
Domain Path: /langs
Copyright: © 2023 WPFactory
WC tested up to: 8.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'alg_wc_ev_is_plugin_active' ) ) {
	/**
	 * alg_wc_cog_is_plugin_active.
	 *
	 * @version 2.1.6
	 * @since   2.1.6
	 */
	function alg_wc_ev_is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}
}

// Check for active plugins
if (
	! alg_wc_ev_is_plugin_active( 'woocommerce/woocommerce.php' ) ||
	( 'email-verification-for-woocommerce.php' === basename( __FILE__ ) && alg_wc_ev_is_plugin_active( 'email-verification-for-woocommerce-pro/email-verification-for-woocommerce-pro.php' ) )
) {
	return;
}

if ( ! class_exists( 'Alg_WC_Email_Verification' ) ) :
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
endif;

if ( ! class_exists( 'Alg_WC_Email_Verification' ) ) :

/**
 * Main Alg_WC_Email_Verification Class
 *
 * @class   Alg_WC_Email_Verification
 * @version 1.9.3
 * @since   1.0.0
 */
final class Alg_WC_Email_Verification {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '2.6.3';

	/**
	 * @var   Alg_WC_Email_Verification The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * @var Alg_WC_Email_Verification_Core
	 * @version 1.9.5
	 * @since   1.9.5
	 */
	public $core;

	/**
	 * Main Alg_WC_Email_Verification Instance
	 *
	 * Ensures only one instance of Alg_WC_Email_Verification is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_WC_Email_Verification - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initializes the plugin.
	 *
	 * @version 2.5.7
	 * @since   1.0.0
	 * @access  public
	 */
	function init() {

		// Localization.
		add_action( 'init', array( $this, 'localize' ) );

		// Pro.
		if ( 'email-verification-for-woocommerce-pro.php' === basename( __FILE__ ) ) {
			require_once( 'includes/pro/class-alg-wc-ev-pro.php' );
		}

		// Declare compatibility with custom order tables for WooCommerce.
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Include required files.
		$this->includes();

		// Admin.
		if ( is_admin() ) {
			$this->admin();
		}

		// Generate documentation
		add_filter( 'wpfpdh_documentation_params_' . plugin_basename( $this->get_filesystem_path() ), array( $this, 'handle_documentation_params' ), 10 );
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 2.5.7
	 * @since   2.5.7
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function wc_declare_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', alg_wc_ev()->get_filesystem_path(), true );
		}
	}

	/**
	 * Handle documentation params managed by the WP Factory
	 *
	 * @version 2.1.0
	 * @since   2.0.0
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	function handle_documentation_params( $params ) {
		$params['wc_tab_id']           = 'alg_wc_ev';
		$params['pro_settings_filter'] = 'alg_wc_ev_settings';
		/*$params['text_file_update_params']=array(
			'text_file_path' => WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'test' .DIRECTORY_SEPARATOR.'test.txt' //plugin_dir_path( $this->plugins_dir . DIRECTORY_SEPARATOR . $plugin_file ) ) ) . DIRECTORY_SEPARATOR . 'readme.txt'
		);*/
		return $params;
	}

	/**
	 * localize.
	 *
	 * @version 1.9.8
	 * @since   1.9.8
	 *
	 */
	function localize() {
		// Set up localisation
		load_plugin_textdomain( 'emails-verification-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.9.3
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once( 'includes/class-alg-wc-ev-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.2.0
	 * @since   1.1.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'alg_wc_ev_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.4.1
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'email-verification-for-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/email-verification-for-woocommerce/">' .
				__( 'Go Pro', 'emails-verification-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Email Verification settings tab to WooCommerce settings.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		if ( apply_filters( 'alg_wc_ev_add_woocommerce_settings_tab_validation', true ) ) {
			$settings[] = require_once( 'includes/settings/class-alg-wc-ev-settings.php' );
		}
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function version_updated() {
		update_option( 'alg_wc_ev_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * plugin_dir_name.
	 *
	 * @version 2.3.6
	 * @since   2.3.6
	 *
	 * @return string
	 */
	function plugin_dir_name(){
		return untrailingslashit( dirname( plugin_basename( __FILE__ ) ) );
	}

	/**
	 * Get the plugin file.
	 *
	 * @version 2.1.0
	 * @since   1.7.0
	 * @return  string
	 */
	function get_filesystem_path() {
		return __FILE__;
	}

}

endif;

if ( ! function_exists( 'alg_wc_ev' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Email_Verification to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_Email_Verification
	 */
	function alg_wc_ev() {
		return Alg_WC_Email_Verification::instance();
	}
}

// Initializes the plugin.
add_action( 'plugins_loaded', function () {
	$plugin = alg_wc_ev();
	$plugin->init();
} );

// Custom deactivation/activation hooks.
$activation_hook   = 'alg_wc_ev_on_activation';
$deactivation_hook = 'alg_wc_ev_on_deactivation';
register_activation_hook( __FILE__, function () use ( $activation_hook ) {
	add_option( $activation_hook, 'yes' );
} );
register_deactivation_hook( __FILE__, function () use ( $deactivation_hook ) {
	do_action( $deactivation_hook );
} );
add_action( 'admin_init', function () use ( $activation_hook ) {
	if ( is_admin() && get_option( $activation_hook ) === 'yes' ) {
		delete_option( $activation_hook );
		do_action( $activation_hook );
	}
} );

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', dirname(__FILE__), true );
	}
} );
