<?php
/**
 * Email Verification for WooCommerce - Advanced Section Settings
 *
 * @version 2.0.0
 * @since   1.6.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Advanced' ) ) :

class Alg_WC_Email_Verification_Settings_Advanced extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.9.6
	 * @since   1.6.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'emails-verification-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 * @since   1.6.0
	 * @todo    (maybe) remove `alg_wc_ev_prevent_login_after_checkout_notice` (i.e. make it always enabled)
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Advanced Options', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_advanced_options',
			),
			array(
				'title'    => __( 'Mail function', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Function used for sending the plugin\'s emails.', 'emails-verification-for-woocommerce' ) . ' ' .
					__( 'Leave the default value if unsure.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_mail_function',
				'default'  => 'wc_mail',
				'options'  => array(
					'mail'    => sprintf( __( '%s function', 'emails-verification-for-woocommerce' ), 'PHP "mail()"' ),
					'wc_mail' => sprintf( __( '%s function', 'emails-verification-for-woocommerce' ), 'WooCommerce "wc_mail()"' ),
					'wp_mail' => sprintf( __( '%s function', 'emails-verification-for-woocommerce' ), 'WordPress "wp_mail()"' ),
				),
			),
			array(
				'title'    => __( 'Custom "logout" function', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Replaces standard %s function in plugin with custom one. Enable this if you are having issues with "Activate" notice not being displayed after user registration.', 'emails-verification-for-woocommerce' ) . '<br />' . __( 'If your cart is getting cleared after a new account is created, try to enable this option.', 'emails-verification-for-woocommerce' ),
					'<code>wp_logout()</code>' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_custom_logout_function',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Action for "Prevent automatic user login after checkout"', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Change this if you are having issues with "Prevent automatic user login after checkout" option, e.g. product is removed from the cart on checkout.', 'emails-verification-for-woocommerce' ) . ' ' .
					__( 'Leave the default value if unsure.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_action',
				'default'  => 'woocommerce_get_return_url',
				'options'  => array(
					'woocommerce_get_return_url'  => __( 'On "get return URL"', 'emails-verification-for-woocommerce' ),
					'woocommerce_before_thankyou' => __( 'On "before \'thank you\' page"', 'emails-verification-for-woocommerce' ),
					'woocommerce_thankyou'        => __( 'On "\'thank you\' page"', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Notice for "Prevent automatic user login after checkout"', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Add notice', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Adds "Activate" notice to the WooCommerce "Thank you" (i.e. "Order received") page.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_notice',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Replace HTML tags', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Replace angle brackets from HTML tags.', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Replaces angle brackets from HTML tags by other characters.', 'emails-verification-for-woocommerce' ).'<br />'.
				              __( 'Enable if you have problem trying to save settings.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_replace_html_tags',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_advanced_options',
			),
			array(
				'title'    => __( 'Delete options', 'emails-verification-for-woocommerce' ),
				'desc' => __( 'Some notes regarding how this tool works:', 'emails-verification-for-woocommerce' ) . '<br />'
				          . '- ' . sprintf( __( 'It will only delete unverified users which roles are not set in the %s option.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Skip email verification for user roles', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				          . '- ' . sprintf( __( 'If the %s option is set it will only delete unverified users whose activation have expired, so it\'s more safe to use it.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Expire time', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				          . '- ' . sprintf( __( 'If the %s option is enabled it may delete the current users who have not verified their account yet, so it\'s more safe to leave it <strong>disabled</strong>.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Enable email verification for already registered users', 'emails-verification-for-woocommerce' ) . '</strong>' ) . '<br />'
				          . '- ' . '<span style="font-weight: bold; color: red;">' . __( 'Deleted users can only be restored if you have a backup.', 'emails-verification-for-woocommerce' ) . '</span>',
				'type'     => 'title',
				'id'       => 'alg_wc_ev_admin_delete_options',
			),
			array(
				'title'    => __( 'Delete users', 'emails-verification-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Delete', 'emails-verification-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Deletes unverified users from the database.', 'emails-verification-for-woocommerce' ) . ' ' .
				              __( 'Check the box and save changes to run the tool.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delete_users',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Delete users automatically', 'emails-verification-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'emails-verification-for-woocommerce' ) . '</strong>',
				'desc_tip' => sprintf( __( 'Deletes unverified users from the database automatically based on the frequency option below.', 'emails-verification-for-woocommerce' ) )
				              . $this->get_delete_users_cron_info(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delete_users_cron',
				'default'  => 'no',
			),
			array(
				'desc'     => __( 'Frequency', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'If the frequency is changed after the Delete users automatically option is enabled, it will be necessary to disable and enable it again to see the frequency updated.', 'emails-verification-for-woocommerce' ),
				'options'  => array(
					'hourly'     => __( 'Hourly', 'emails-verification-for-woocommerce' ),
					'daily'      => __( 'Daily', 'emails-verification-for-woocommerce' ),
					'twicedaily' => __( 'Twice daily', 'emails-verification-for-woocommerce' ),
					'weekly'     => __( 'Weekly', 'emails-verification-for-woocommerce' ),
				),
				'id'       => 'alg_wc_ev_delete_users_cron_frequency',
				'default'  => 'weekly',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_admin_delete_options',
			),
			array(
				'title'    => __( 'Prevent automatic user login', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Prevents users from login automatically before their accounts are verified.', 'emails-verification-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_ev_prevent_login_options',
			),
			array(
				'title'    => __( 'Prevent login after register', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Prevents automatic user login after registration on "My Account" page.', 'emails-verification-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_register',
				'default'  => 'yes',
			),
			array(
				'desc'     => __( 'Redirect', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_prevent_login_after_register_redirect',
				'default'  => 'no',
				'options'  => array(
					'no'     => __( 'No redirect', 'emails-verification-for-woocommerce' ),
					'yes'    => __( 'Force redirect to the "My Account" page', 'emails-verification-for-woocommerce' ),
					'custom' => __( 'Custom redirect', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'desc'     => __( 'Custom redirect URL', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( '"%s" must be selected for the "%s" option above.', 'emails-verification-for-woocommerce' ),
						__( 'Custom redirect', 'emails-verification-for-woocommerce' ), __( 'Redirect', 'emails-verification-for-woocommerce' ) ) . ' ' .
				              __( 'Must be a local URL.', 'emails-verification-for-woocommerce' ),
				'type'     => 'text',
				'id'       => 'alg_wc_ev_prevent_login_after_register_redirect_url',
				'default'  => '',
				'css'      => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'    => __( 'Prevent login after checkout', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Enable', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Prevents automatic user login after registration during checkout.', 'emails-verification-for-woocommerce' ) . '<br>' .
				              sprintf( __( 'If this option is not working correctly on your site, please try changing the value for the %s option in %s.', 'emails-verification-for-woocommerce' ),
					              "<strong>" . __( 'Action for "Prevent automatic user login after checkout"', 'emails-verification-for-woocommerce' ) . "</strong>",
					              '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev&section=advanced' ) . '">' .
					              __( 'WooCommerce > Settings > Email Verification > Advanced', 'emails-verification-for-woocommerce' ) . '</a>'
				              ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout',
				'default'  => 'yes',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Block "Thank you" page', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Blocks "Thank you" (i.e. "Order received") page access for non-verified users. Users will be redirected to the "My account" page.', 'emails-verification-for-woocommerce' ) .
				              $this->pro_msg(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_prevent_login_after_checkout_block_thankyou',
				'default'  => 'no',
				'checkboxgroup' => '',
				//'readme'   => array( 'dynamic_params' => array( 'pro' => array( 'active' => true ) ) ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Block customer order emails', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Blocks standard WooCommerce customer order emails ("%s", "%s", "%s") for all non-verified users (including guests).', 'emails-verification-for-woocommerce' ),
						__( 'Order on-hold', 'woocommerce' ), __( 'Processing order', 'woocommerce' ), __( 'Completed order', 'woocommerce' ) ) .
				              $this->pro_msg(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_block_customer_order_emails',
				'default'  => 'no',
				'checkboxgroup' => 'end',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_ev_prevent_login_options',
			),
		);
	}

	/**
	 * get_delete_users_cron_info.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see https://wordpress.stackexchange.com/a/107258/25264
	 *
	 * @return string
	 */
	function get_delete_users_cron_info() {
		if ( ( isset( $_POST['save'] ) && isset( $_POST['alg_wc_ev_delete_users_cron'] ) ) ) {
			do_action( 'alg_wc_ev_enable_delete_users_cron' );
		}
		if (
			! ( $event_timestamp = wp_next_scheduled( 'alg_wc_ev_delete_unverified_users' ) )
			|| ( isset( $_POST['save'] ) && ! isset( $_POST['alg_wc_ev_delete_users_cron'] ) )
		) {
			return '';
		}
		$output          = '<br />';
		$now             = new DateTime();
		$event_date_time = new DateTime();
		$event_date_time->setTimestamp( $event_timestamp );
		$diff                = $event_date_time->diff( $now );
		$pretty_time_missing = '';
		if ( $diff->d > 0 ) {
			$pretty_time_missing = sprintf( __( '(%s days missing)', 'emails-verification-for-woocommerce' ), $diff->d );
		} elseif ( $diff->h > 0 ) {
			$pretty_time_missing = sprintf( __( '(%s hours missing)', 'emails-verification-for-woocommerce' ), $diff->h + ( $diff->days * 24 ) );
		} elseif ( $diff->d == 0 && $diff->h == 0 && $diff->i > 0 ) {
			$pretty_time_missing = sprintf( __( '(%s minutes missing)', 'emails-verification-for-woocommerce' ), $diff->i );
		}
		if ( ! empty( $pretty_time_missing ) ) {
			$output .= sprintf( __( 'Next event scheduled to %s', 'emails-verification-for-woocommerce' ), '<strong>' . get_date_from_gmt( date( 'Y-m-d H:i:s', $event_timestamp ), get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) ) . '</strong>' );
			$output .= ' ' . $pretty_time_missing;
		} else {
			$output .= '<span style="font-weight: bold; color: green;">' . __( 'Please, reload the page to see the next scheduled event info.', 'emails-verification-for-woocommerce' ) . '</span>';
		}
		return $output;
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Advanced();
