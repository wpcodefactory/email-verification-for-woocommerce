<?php
/**
 * Email Verification for WooCommerce - Email Section Settings.
 *
 * @version 2.8.6
 * @since   1.3.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Email' ) ) :

class Alg_WC_Email_Verification_Settings_Email extends Alg_WC_Email_Verification_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.1.1
	 * @since   1.3.0
	 */
	function __construct() {
		$this->id   = 'emails';
		$this->desc = __( 'Email', 'emails-verification-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.8.6
	 * @since   1.3.0
	 */
	function get_settings() {
		$general_opts = array(
			array(
				'title' => __( 'Email options', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_email_options',
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
				'title'    => __( 'Customer new account email', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Delay WooCommerce "Customer new account" email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( '"Customer new account" email is only sent on successful verification.', 'emails-verification-for-woocommerce' ) . ' ' .
				              $this->separate_email_option_msg(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delay_wc_email',
				'default'  => 'no',
			),
			array(
				'title'             => __( 'Email template', 'emails-verification-for-woocommerce' ),
				'desc'              => sprintf( __( 'Possible values: %s, %s, %s.', 'emails-verification-for-woocommerce' ), __( 'Plain', 'emails-verification-for-woocommerce' ), __( 'WooCommerce', 'emails-verification-for-woocommerce' ), __( 'Smart', 'emails-verification-for-woocommerce' ) ),
				'desc_tip'          => __( '"Smart" will automatically send a "Plain" email when appended to other email, like the "Customer new account" email, and will send a "WooCommerce" email when sending it as a separate email. Most probably "Smart" will be the best choice.', 'emails-verification-for-woocommerce' ),
				'id'                => 'alg_wc_ev_email_template',
				'type'              => 'select',
				'class'             => 'chosen_select',
				'default'           => 'plain',
				'options'           => array(
					'plain' => __( 'Plain', 'emails-verification-for-woocommerce' ),
					'wc'    => __( 'WooCommerce', 'emails-verification-for-woocommerce' ),
					'smart' => __( 'Smart', 'emails-verification-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'WC email template', 'emails-verification-for-woocommerce' ),
				'desc'     => sprintf( __( '%s will use the template options from this page.', 'emails-verification-for-woocommerce' ), '<code>' . __( 'Email Verification plugin', 'emails-verification-for-woocommerce' ) . '</code>' ) . '<br />' .
				              sprintf( __( '%s option will create new emails (%s) on %s.', 'emails-verification-for-woocommerce' ), '<code>' . __( 'WooCommerce > Emails', 'emails-verification-for-woocommerce' ) . '</code>', alg_wc_ev_array_to_string( array( __( 'Activation', 'emails-verification-for-woocommerce' ), __( 'Confirmation', 'emails-verification-for-woocommerce' ) ) ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=email' ) . '">' . __( 'WooCommerce > Emails', 'emails-verification-for-woocommerce' ) . '</a>' ),
				'desc_tip' => __( 'This option will only be useful if the Email Template option is set as <code>WooCommerce</code> or <code>Smart</code>.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_wc_email_template',
				'default'  => 'simulation',
				'options'  => array(
					'simulation'    => __( 'Email Verification plugin', 'emails-verification-for-woocommerce' ),
					'real_wc_email' => __( 'WooCommerce > Emails', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Email wrap method', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'If "%s" option is selected as "%s" or "%s", set the email wrap method here.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Email template', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'WooCommerce', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Smart', 'emails-verification-for-woocommerce' ) . '</strong>' ),
				'id'       => 'alg_wc_ev_email_template_wc_wrap_method',
				'type'     => 'radio',
				'default'  => 'manual', // Maybe put default as native?
				'options'  => array(
					'manual' => __( 'Manual - Adding WooCommerce header and footer manually', 'emails-verification-for-woocommerce' ),
					'native' => sprintf( __( 'Native - Using %s function', 'emails-verification-for-woocommerce' ), 'WC_Emails::wrap_message()' )
				)
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_email_options',
			)
		);
		$email_placeholder_opts = array(
			array(
				'title' => __( 'Common placeholders', 'emails-verification-for-woocommerce' ),
				'desc'  => sprintf( __( 'These placeholders can be used on the Email Subject or Content options: %s.', 'emails-verification-for-woocommerce' ), '<code>' . implode( '</code>, <code>', $this->get_default_email_placeholders() ) . '</code>' ) . ' ' .
				           sprintf( __( 'If you\'re having deliverability problems or emails being sent to Spam, try using the placeholders %s and %s on the Email Content or subject.', 'emails-verification-for-woocommerce' ), '<code>%site_title%</code>','<code>%site_url%</code>'),

				'type'  => 'title',
				'id'    => 'alg_wc_ev_common_email_placeholder_options',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_common_email_placeholder_options',
			)
		);
		$activation_email_opts   = array(
			array(
				'title' => __( 'Activation email', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_activation_email',
				'desc'  => __( 'An email sent to the user with an activation link.', 'emails-verification-for-woocommerce' ) . ' ' .
				           sprintf( __( 'Additional placeholders: %s.', 'emails-verification-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array( '%verification_url%' ) ) . '</code>' ),

			),
			array(
				'title'    => __( 'Send as a separate email', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Send verification as a separate email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Disable it if you want to append it to the standard WooCommerce "Customer new account" email.', 'emails-verification-for-woocommerce' ) . ' ' .
				              sprintf( __( 'If the %s email is not being sent, try to change the option %s.', 'emails-verification-for-woocommerce' ), __( 'New account', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Email sending trigger', 'emails-verification-for-woocommerce' ) . '</strong>' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_send_as_separate_email',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Email sending trigger', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Leave the default value if unsure.', 'emails-verification-for-woocommerce' ) . ' ' .
				              $this->separate_email_option_msg(),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_ev_new_user_action',
				'default'  => 'user_register',
				'options'  => array(
					'user_register'                => __( 'On "user register"', 'emails-verification-for-woocommerce' ),
					'woocommerce_created_customer' => __( 'On "WooCommerce created customer"', 'emails-verification-for-woocommerce' ),
					//'on_order_payment'             => __( 'On order payment', 'emails-verification-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Activation email delay', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Delay the activation email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Try to enable it if the activation emails are getting sent to authenticated users.', 'emails-verification-for-woocommerce' ) . '<br />' .
				              $this->separate_email_option_msg(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_delay_activation_email',
				'default'  => 'no',
			),
			array(
				'title'             => __( 'Email subject', 'emails-verification-for-woocommerce' ),
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_email_subject',
				'default'           => '[%site_title%]: ' . __( 'Please activate your account', 'emails-verification-for-woocommerce' ),
				'css'               => 'width:100%;',
				'alg_wc_ev_raw'     => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'             => __( 'Email content', 'emails-verification-for-woocommerce' ),
				'desc'              => '',
				'desc_tip'          => '',
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_email_content',
				'default'           => alg_wc_ev()->core->emails->get_default_email_content('activation'),
				'css'               => 'width:100%;height:150px;',
				'alg_wc_ev_raw'     => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'desc_tip'          => sprintf( __( 'If "%s" option is selected as "%s" or "%s", set the email wrap method here.', 'emails-verification-for-woocommerce' ), '<strong>' . __( 'Email template', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'WooCommerce', 'emails-verification-for-woocommerce' ) . '</strong>', '<strong>' . __( 'Smart', 'emails-verification-for-woocommerce' ) . '</strong>' ),
				'title'             => __( 'Email heading', 'emails-verification-for-woocommerce' ),
				'id'                => 'alg_wc_ev_email_template_wc_heading',
				'type'              => 'textarea',
				'default'           => __( 'Activate your account', 'emails-verification-for-woocommerce' ),
				'css'               => 'width:100%;',
				'alg_wc_ev_raw'     => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'    => __( 'Fine tune placement', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Choose precisely where the activation email will be appended to the "Customer new account" email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'It\'s necessary to add %s to the %s email template.', 'emails-verification-for-woocommerce' ), '<code>do_action( "alg_wc_ev_activation_email_content_placeholder", $email->object )</code>', '"Customer new account"' ) . '<br />' .
				              $this->separate_email_option_msg( 'disabled' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_ev_fine_tune_activation_email_placement',
				'default'  => 'no',
			),
			array(
				'title'             => __( 'Automatic resending', 'emails-verification-for-woocommerce' ),
				'desc'              => __( 'Resend the activation email automatically if the user has not yet been verified', 'emails-verification-for-woocommerce' ),
				'desc_tip'          => __( 'Works for future users, or if a current unverified user receives the activation email manually.', 'emails-verification-for-woocommerce' ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_ev_activation_email_automatic_sending',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
				'default'           => 'no',
			),
			array(
				'desc'              => __( 'Frequency.', 'emails-verification-for-woocommerce' ) . ' ' .
				                       $this->get_frequency_description(),
				'type'              => 'number',
				//'custom_attributes' => array( 'min' => 1 ),
				'id'                => 'alg_wc_ev_activation_email_automatic_sending_frequency',
				'default'           => 1,
				'custom_attributes' => array_merge( empty( $disabled_arr = apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ) ) ? array() : $disabled_arr, array( 'min' => 1 ) ),
			),
			array(
				'desc'     => __( 'Unit of time.', 'emails-verification-for-woocommerce' ),
				'type'     => 'select',
				'id'       => 'alg_wc_ev_activation_email_automatic_sending_frequency_unit',
				'options'  => array(
					'hour' => __( 'Hours', 'emails-verification-for-woocommerce' ),
					'day'  => __( 'Days', 'emails-verification-for-woocommerce' ),
				),
				'class'    => 'chosen_select',
				'default'  => 'day',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'              => __( 'Max attempts.', 'emails-verification-for-woocommerce' ),
				'type'              => 'number',
				'id'                => 'alg_wc_ev_activation_email_automatic_sending_count_max',
				'default'           => 3,
				'custom_attributes' => array_merge( empty( $disabled_arr = apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ) ) ? array() : $disabled_arr, array( 'min' => 1 ) ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_activation_email',
			)
		);
		$confirmation_email_opts = array(
			array(
				'title' => __( 'Confirmation email', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_confirmation_email',
				'desc'  => __( 'An email sent to the user when the account is verified.', 'emails-verification-for-woocommerce' ),
			),
			array(
				'title'         => __( 'Confirmation email', 'emails-verification-for-woocommerce' ),
				'desc'          => __( 'Send confirmation email to users who have just verified their accounts', 'emails-verification-for-woocommerce' ),
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'id'            => 'alg_wc_ev_enable_confirmation_email',
				'default'       => 'yes',
			),
			array(
				'desc'          => __( 'Send confirmation email to the user manually verified by admin', 'emails-verification-for-woocommerce' ),
				'desc_tip'      => sprintf( __( 'Only useful if the options %s, %s are enabled.', 'emails-verification-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev&section=admin' ) . '">' . __( 'Verified column', 'emails-verification-for-woocommerce' ) . '</a>', '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev&section=admin' ) . '">' . __( 'Actions', 'emails-verification-for-woocommerce' ) . '</a>' ),
				'checkboxgroup' => 'end',
				'type'          => 'checkbox',
				'id'            => 'alg_wc_ev_send_confirmation_email_to_manually_verified_users',
				'default'       => 'no',
			),
			array(
				'title'             => __( 'Email subject', 'emails-verification-for-woocommerce' ),
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_confirmation_email_subject',
				'default'           => '[%site_title%]: ' . __( 'Your account has been activated successfully', 'emails-verification-for-woocommerce' ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				'css'               => 'width:100%;',
				'alg_wc_ev_raw'     => true,
			),
			array(
				'title'             => __( 'Email Heading', 'emails-verification-for-woocommerce' ),
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_confirmation_email_heading',
				'default'           => __( 'Your account has been activated', 'emails-verification-for-woocommerce' ),
				'css'               => 'width:100%;',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				'alg_wc_ev_raw'     => true,
			),
			array(
				'title'             => __( 'Email content', 'emails-verification-for-woocommerce' ),
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_confirmation_email_content',
				'default'           => alg_wc_ev()->core->emails->get_default_email_content('confirmation'),
				'css'               => 'width:100%;height:150px;',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				'alg_wc_ev_raw'     => true,
			),
			array(
				'title'   => __( 'Delay', 'emails-verification-for-woocommerce' ),
				'desc'    => __( 'Wait for some time before sending the email', 'emails-verification-for-woocommerce' ),
				'type'    => 'checkbox',
				'id'      => 'alg_wc_ev_confirmation_email_delay',
				'default' => 'no',
			),
			array(
				'desc'    => __( 'Unit of time.', 'emails-verification-for-woocommerce' ),
				'type'    => 'select',
				'class'   => 'chosen_select',
				'id'      => 'alg_wc_ev_confirmation_email_delay_time_unit',
				'default' => 'hours',
				'options' => array(
					'hours' => __( 'Hours', 'emails-verification-for-woocommerce' ),
					'days'  => __( 'Days', 'emails-verification-for-woocommerce' ),
				)
			),
			array(
				'desc'              => __( 'Delay value.', 'emails-verification-for-woocommerce' ),
				'type'              => 'number',
				'default'           => 1,
				'custom_attributes' => array( 'step' => 0.1, 'min' => 0.1 ),
				'id'                => 'alg_wc_ev_confirmation_email_delay_value',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_activation_email',
			),
		);
		$admin_email_opts = array(
			array(
				'title' => __( 'Admin email', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'An email sent to the admin when a user verifies his email.', 'emails-verification-for-woocommerce' ),
				'id'    => 'alg_wc_ev_email_options',
			),
			array(
				'title'             => __( 'Admin email', 'emails-verification-for-woocommerce' ),
				$this->pro_msg(),
				'desc'              => __( 'Send email to the admin when a user verifies his email', 'emails-verification-for-woocommerce' ),
				'type'              => 'checkbox',
				'checkboxgroup'     => 'start',
				'id'                => 'alg_wc_ev_admin_email',
				'default'           => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'          => __( 'Send admin email when a user has been manually verified by admin', 'emails-verification-for-woocommerce' ),
				'desc_tip'      => sprintf( __( 'Only useful if the options %s, %s are enabled.', 'emails-verification-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev&section=admin' ) . '">' . __( 'Verified column', 'emails-verification-for-woocommerce' ) . '</a>', '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_ev&section=admin' ) . '">' . __( 'Actions', 'emails-verification-for-woocommerce' ) . '</a>' ),
				'checkboxgroup' => 'end',
				'type'          => 'checkbox',
				'id'            => 'alg_wc_ev_send_admin_email_to_manually_verified_users',
				'default'       => 'no',
			),
			array(
				'title'    => __( 'Recipient', 'emails-verification-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Leave empty to send to %s.', 'emails-verification-for-woocommerce' ), get_bloginfo( 'admin_email' ) ),
				'type'     => 'text',
				'id'       => 'alg_wc_ev_admin_email_recipient',
				'default'  => '',
				'css'      => 'width:100%;',
			),
			array(
				'title'   => __( 'Subject', 'emails-verification-for-woocommerce' ),
				'type'    => 'text',
				'id'      => 'alg_wc_ev_admin_email_subject',
				'default' => '[%site_title%]: ' . __( 'User email has been verified', 'emails-verification-for-woocommerce' ),
				'css'     => 'width:100%;',
			),
			array(
				'title'         => __( 'Heading', 'emails-verification-for-woocommerce' ),
				'type'          => 'textarea',
				'id'            => 'alg_wc_ev_admin_email_heading',
				'default'       => __( 'User account has been activated', 'emails-verification-for-woocommerce' ),
				'css'           => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'         => __( 'Content', 'emails-verification-for-woocommerce' ),
				'type'          => 'textarea',
				'id'            => 'alg_wc_ev_admin_email_content',
				'default'       => alg_wc_ev()->core->emails->get_default_email_content('admin'),
				'css'           => 'width:100%;height:100px;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_email_options',
			),
		);
		return array_merge( $general_opts, $email_placeholder_opts, $activation_email_opts, $confirmation_email_opts, $admin_email_opts );
	}

	/**
	 * get_frequency_description.
	 *
	 * @version 2.8.6
	 * @since   2.8.6
	 *
	 * @return string
	 */
	function get_frequency_description() {
		$frequency       = get_option( 'alg_wc_ev_activation_email_automatic_sending_frequency', '1' );
		$unit            = get_option( 'alg_wc_ev_activation_email_automatic_sending_frequency_unit', 'day' );
		$formatted_words = array(
			'hour' => _n( 'hour', 'hours', $frequency, 'emails-verification-for-woocommerce' ),
			'day'  => _n( 'day', 'days', $frequency, 'emails-verification-for-woocommerce' ),
		);

		return sprintf( __( 'Send email every %s %s.', 'emails-verification-for-woocommerce' ), (int)$frequency === 1 ? '' : $frequency, $formatted_words[ $unit ] );
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Email();
