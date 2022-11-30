<?php
/**
 * Email Verification for WooCommerce - Email Section Settings.
 *
 * @version 2.4.8
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
	 * get_default_email_placeholders.
	 *
	 * @version 1.9.7
	 * @since   1.9.7
	 *
	 * @return array
	 */
	function get_default_email_placeholders(){
		return array(
			'%user_id%',
			'%user_login%',
			'%user_nicename%',
			'%user_email%',
			'%user_url%',
			'%user_registered%',
			'%user_display_name%',
			'%user_roles%',
			'%user_first_name%',
			'%user_last_name%',
			'%admin_user_profile_url%',
		);
	}

	/**
	 * get_settings.
	 *
	 * @version 2.4.8
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
		$activation_email_opts   = array(
			array(
				'title' => __( 'Activation email', 'emails-verification-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_ev_activation_email',
				'desc'  => __( 'An email sent to the user with an activation link.', 'emails-verification-for-woocommerce' ),
			),
			array(
				'title'    => __( 'Send as a separate email', 'emails-verification-for-woocommerce' ),
				'desc'     => __( 'Send verification as a separate email', 'emails-verification-for-woocommerce' ),
				'desc_tip' => __( 'Disable it if you want to append it to the standard WooCommerce "Customer new account" email.', 'emails-verification-for-woocommerce' ),
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
				'default'           => __( 'Please activate your account', 'emails-verification-for-woocommerce' ),
				'css'               => 'width:100%;',
				'alg_wc_ev_raw'     => true,
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'             => __( 'Email content', 'emails-verification-for-woocommerce' ),
				'desc'              => sprintf( __( 'Placeholders: %s', 'emails-verification-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
						'%verification_url%',
						'%user_id%',
						'%user_first_name%',
						'%user_last_name%',
						'%user_login%',
						'%user_nicename%',
						'%user_email%',
						'%user_display_name%',
					) ) . '</code>' ),
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_email_content',
				'default'           => __( '<p>Please <a href="%verification_url%" target="_blank">click here</a> to verify your email.</p>', 'emails-verification-for-woocommerce' ),
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
				'default'           => __( 'Your account has been activated successfully', 'emails-verification-for-woocommerce' ),
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				'css'               => 'width:100%;',
				'alg_wc_ev_raw'     => true,
			),
			array(
				'title'             => __( 'Email Heading', 'emails-verification-for-woocommerce' ),
				'desc'              => $this->available_placeholders_desc( $this->get_default_email_placeholders() ),
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_confirmation_email_heading',
				'default'           => __( 'Your account has been activated', 'emails-verification-for-woocommerce' ),
				'css'               => 'width:100%;',
				'custom_attributes' => apply_filters( 'alg_wc_ev_settings', array( 'readonly' => 'readonly' ) ),
				'alg_wc_ev_raw'     => true,
			),
			array(
				'title'             => __( 'Email content', 'emails-verification-for-woocommerce' ),
				'desc'              => sprintf( __( 'Placeholders: %s', 'emails-verification-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
						'%user_id%',
						'%user_first_name%',
						'%user_last_name%',
						'%user_login%',
						'%user_nicename%',
						'%user_email%',
						'%user_display_name%',
					) ) . '</code>' ),
				'type'              => 'textarea',
				'id'                => 'alg_wc_ev_confirmation_email_content',
				'default'           => __( '<p>Your account has been activated successfully.</p>', 'emails-verification-for-woocommerce' ),
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
				'desc'    => $this->available_placeholders_desc( $this->get_default_email_placeholders() ),
				'type'    => 'text',
				'id'      => 'alg_wc_ev_admin_email_subject',
				'default' => __( 'User email has been verified', 'emails-verification-for-woocommerce' ),
				'css'     => 'width:100%;',
			),
			array(
				'title'         => __( 'Heading', 'emails-verification-for-woocommerce' ),
				'desc'          => $this->available_placeholders_desc( $this->get_default_email_placeholders() ),
				'type'          => 'textarea',
				'id'            => 'alg_wc_ev_admin_email_heading',
				'default'       => __( 'User account has been activated', 'emails-verification-for-woocommerce' ),
				'css'           => 'width:100%;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'title'         => __( 'Content', 'emails-verification-for-woocommerce' ),
				'desc'          => $this->available_placeholders_desc( $this->get_default_email_placeholders() ),
				'type'          => 'textarea',
				'id'            => 'alg_wc_ev_admin_email_content',
				'default'       => sprintf( __( 'User %s has just verified his email (%s).', 'emails-verification-for-woocommerce' ),
					'<a href="%admin_user_profile_url%">%user_login%</a>', '%user_email%' ),
				'css'           => 'width:100%;height:100px;',
				'alg_wc_ev_raw' => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_ev_email_options',
			),
		);
		return array_merge( $general_opts, $activation_email_opts, $confirmation_email_opts, $admin_email_opts );
	}

}

endif;

return new Alg_WC_Email_Verification_Settings_Email();
