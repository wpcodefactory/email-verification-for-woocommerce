<?php
/**
 * Email Verification for WooCommerce - Settings
 *
 * @version 2.0.8
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings' ) ) :

class Alg_WC_Email_Verification_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.0.8
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
		// Create notice about pro
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'create_notice_regarding_pro' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'highlight_premium_notice_on_disabled_setting_click' ) );
	}

	/**
	 * highlight_premium_notice_on_disabled_setting_click.
	 *
	 * @version 2.0.8
	 * @since   2.0.8
	 */
	function highlight_premium_notice_on_disabled_setting_click(){
		if ( '' === apply_filters( 'alg_wc_ev_settings', true ) ) {
			return;
		}
		?>
		<script>
			jQuery(document).ready(function ($) {
				jQuery(document).ready(function ($) {
					let highlighter = {
						targetClass: '.alg-wc-ev-premium-notice',
						highlight: function () {
							window.scrollTo({
								top: 0,
								behavior: 'smooth'
							});
							setTimeout(function () {
								$(highlighter.targetClass).addClass('alg-wc-ev-blink');
							}, 300);
							setTimeout(function () {
								$(highlighter.targetClass).removeClass('alg-wc-ev-blink');
							}, 3000);
						}
					};
					function createDisabledElem(){
						$(".form-table *:disabled,.form-table *[readonly],.form-table .select2-container--disabled").each(function () {
							$(this).parent().css({
								"position": "relative"
							});
							let position = $(this).position();
							position.top = $(this)[0].offsetTop;
							let disabledDiv = $("<div class='alg-wc-ev-disabled alg-wc-ev-highlight-premium-notice'></div>").insertAfter($(this));
							disabledDiv.css({
								"position": "absolute",
								"left": position.left,
								"top": position.top,
								"width": $(this).outerWidth(),
								"height": $(this).outerHeight(),
								"cursor": 'pointer'
							});
						});
					}
					createDisabledElem();
					$("label:has(input:disabled),label:has(input[readonly])").addClass('alg-wc-ev-highlight-premium-notice');
					$(".alg-wc-ev-highlight-premium-notice, .select2-container--disabled").on('click', highlighter.highlight);
				});
			});
		</script>
		<style>
			.alg-wc-ev-blink{
				animation: alg-dtwp-blink 1s;
				animation-iteration-count: 3;
			}
			@keyframes alg-dtwp-blink { 50% { background-color:#ececec ; }  }
		</style>
		<?php
	}

	/**
	 * create_notice_regarding_pro.
	 *
	 * @version 2.0.8
	 * @since   2.0.8
	 */
	function create_notice_regarding_pro() {
		if ( true === apply_filters( 'alg_wc_ev_settings', true ) ) {
			$pro_version_title      = __( 'Email Verification for WooCommerce Pro', 'emails-verification-for-woocommerce' );
			$pro_version_url        = 'https://wpfactory.com/item/email-verification-for-woocommerce/';
			$plugin_icon_url        = 'https://ps.w.org/emails-verification-for-woocommerce/assets/icon-128x128.png?rev=1884298';
			$upgrade_btn_icon_class = 'dashicons-before dashicons-unlock';
			// Message
			$message = sprintf( '<img style="%s" src="%s"/><span style="%s">%s</span>',
				'margin-right:10px;width:38px;vertical-align:middle',
				$plugin_icon_url,
				'vertical-align: middle;margin:0 14px 0 0;',
				sprintf( __( 'Disabled options can be unlocked using <a href="%s" target="_blank">%s</a>', 'emails-verification-for-woocommerce' ), $pro_version_url, '<strong>' . $pro_version_title . '</strong>' )
			);
			// Button
			$button = sprintf( '<a style="%s" target="_blank" class="button-primary" href="%s"><i style="%s" class="%s"></i>%s</a>',
				'vertical-align:middle;display:inline-block;margin:0',
				$pro_version_url,
				'position:relative;top:3px;margin:0 2px 0 -2px;',
				$upgrade_btn_icon_class,
				__( 'Upgrade to Pro version', 'emails-verification-for-woocommerce' )
			);
			echo '<div id="message" class="alg-wc-ev-premium-notice notice notice-info inline"><p style="margin:5px 0">' . $message . $button . '</p></div>';
		}
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
