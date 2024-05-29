<?php
/**
 * Email Verification for WooCommerce - Shortcodes Section Settings
 *
 * @version 2.8.2
 * @since   2.8.2
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_Settings_Shortcodes' ) ) :

	class Alg_WC_Email_Verification_Settings_Shortcodes extends Alg_WC_Email_Verification_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 */
		function __construct() {
			$this->id   = 'shortcodes';
			$this->desc = __( 'Shortcodes', 'emails-verification-for-woocommerce' );
			add_action( 'admin_head', array( $this, 'add_admin_inline_css' ) );
			parent::__construct();
		}

		/**
		 * add_admin_inline_css.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @return void
		 */
		function add_admin_inline_css() {
			$screen = get_current_screen();
			if (
				! $screen ||
				'woocommerce_page_wc-settings' !== $screen->id ||
				! isset( $_GET['tab'] ) ||
				'alg_wc_ev' !== $_GET['tab'] ||
				! isset( $_GET['section'] ) ||
				'shortcodes' !== $_GET['section']
			) {
				return;
			}
			?>
			<style>
                .wrap.woocommerce table.form-table .titledesc {
                    word-break: break-word;
                }
			</style>
			<?php
		}

		/**
		 * Get settings.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @return mixed
		 */
		function get_settings() {
			$shortcode_opts = array(
				array(
					'title' => __( 'Shortcodes', 'emails-verification-for-woocommerce' ),
					'desc'  => __( 'Email Verification shortcodes.', 'emails-verification-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_ev_shortcode_opts',
				),
				array(
					'title'    => __( '[alg_wc_ev_custom_msg]', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Displays useful information to users, such as the verification status, or a link to resend the verification email', 'emails-verification-for-woocommerce' ),
					'default'  => 'yes',
					'desc_tip' => $this->format_shortcode_params( array(
							'verified_status'   => array(
								'desc'    => __( 'The verified status HTML.', 'emails-verification-for-woocommerce' ),
								'default' => '<strong>' . __( 'Verified', 'emails-verification-for-woocommerce' ) . '</strong>',
							),
							'unverified_status' => array(
								'desc'    => __( 'The unverified status HTML.', 'emails-verification-for-woocommerce' ),
								'default' => '<strong>' . __( 'Unverified', 'emails-verification-for-woocommerce' ) . '</strong>',
							),
							'hide_clauses'      => array(
								'desc'            => __( 'Hides the shortcode on specific cases.', 'emails-verification-for-woocommerce' ),
								'default'         => 'unverified',
								'possible_values' => array( 'unverified', 'verified', 'guests' ),
							),
							'wrapper_template'  => array(
								'desc'    => __( 'The shortcode container along with its own content.', 'emails-verification-for-woocommerce' ),
								'default' => '<div class="alg-wc-ev-custom-msg">{content_template}</div>',
							),
							'content_template'  => array(
								'desc'    => __( 'The shortcode content.', 'emails-verification-for-woocommerce' ) . ' Available placeholders: ' . alg_wc_ev_array_to_string( array_merge( array( '%verification_status%','%resend_verification_url%' ), $this->get_default_email_placeholders() ), array(
										'item_template' => '<code>{value}</code>'
									) ) . '.',
								'default' => __( 'Verification status: ', 'emails-verification-for-woocommerce' ) . '%verification_status%',
							),
						) ) . $this->format_shortcode_params( array(
							'[alg_wc_ev_custom_msg content_template="Verification status: {verification_status}."]'                                                                                                                 => array(
								'desc' => __( 'Displays the verification status for both verified and unverified users.', 'emails-verification-for-woocommerce' )
							),
							'[alg_wc_ev_custom_msg hide_clauses="verified" content_template="Hi %user_display_name%, You can resend the email with verification link by clicking <a href=\'%resend_verification_url%\'>here</a>."]' => array(
								'desc' => __( 'Displays a link to send the verification email only for unverified users.', 'emails-verification-for-woocommerce' )
							)
						),
							array(
								'title'      => __( 'Examples:', 'emails-verification-for-woocommerce' ),
								'list_style' => 'circle inside'
							)
						),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_ev_sc_custom_msg',
				),
				array(
					'title'    => __( '[alg_wc_ev_email_content_placeholder]', 'emails-verification-for-woocommerce' ),
					'desc'     => __( 'Appends the verification email to some custom email template', 'emails-verification-for-woocommerce' ),
					'default'  => 'yes',
					'desc_tip' => __( 'In order to use it, it’s necessary to <strong>enable</strong> the option "Email > Activation email > Fine tune activation email placement", <strong>disable</strong> the option "Email > Activation email > Send as a separate email", and the option "Emails > Activation email > Email template" should be probably set as Plain.', 'emails-verification-for-woocommerce' ) . '<br /><br />' .
					              $this->format_shortcode_params( array(
						              'user_email' => array(
							              'desc'    => __( 'The User email.', 'emails-verification-for-woocommerce' ),
							              'default' => '<strong>' . __( 'Verified', 'emails-verification-for-woocommerce' ) . '</strong>',
						              ),
					              ) ) . $this->format_shortcode_params( array(
							'echo do_shortcode(\'[alg_wc_ev_email_content_placeholder user_email="\'.$email.\'"]\')' => array(
								'desc' => __( 'Appends the verification email content to a custom WooCommerce email template.', 'emails-verification-for-woocommerce' )
							),
						),
							array(
								'title'      => __( 'Examples:', 'emails-verification-for-woocommerce' ),
								'list_style' => 'circle inside'
							)
						),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_ev_sc_email_content_placeholder',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_ev_shortcode_opts',
				),
			);

			return array_merge( array(), $shortcode_opts );
		}

		/**
		 * Formats shortcode parameters.
		 *
		 * Use an array for each param having the key as the shortcode param.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @see     self::format_shortcode_param() for a list of argument for each param.
		 *
		 * @param $params
		 * @param $settings
		 *
		 * @return string
		 */
		public function format_shortcode_params( $params = array(), $settings = array() ) {
			$settings             = wp_parse_args( $settings, array(
				'title'      => __( 'Params:', 'emails-verification-for-woocommerce' ),
				'list_style' => 'inside'
			) );
			$title                = sanitize_text_field( $settings['title'] );
			$list_style           = sanitize_text_field( $settings['list_style'] );
			$output               = ! empty( $params ) ? $title . '<br />' . '<ul style="list-style: ' . esc_attr__( $list_style ) . '">%s</ul>' : '';
			$formatted_params_arr = array();
			foreach ( $params as $param_key => $param_data ) {
				$sc_param_data                    = array( 'param' => $param_key );
				$sc_param_data['desc']            = $param_data['desc'] ?? '';
				$sc_param_data['default']         = $param_data['default'] ?? '';
				$sc_param_data['possible_values'] = $param_data['possible_values'] ?? '';
				$formatted_params_arr         []  = $this->format_shortcode_param( $sc_param_data );
			}
			$output = sprintf( $output, alg_wc_ev_array_to_string(
				$formatted_params_arr, array(
					'glue'          => '',
					'item_template' => '<li>{value}</li>',
				)
			) );

			return $output;
		}

		/**
		 * format_shortcode_param.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @param $args
		 *
		 * @return string
		 */
		public function format_shortcode_param( $args = null ) {
			$args   = wp_parse_args( $args, array(
				'param'           => '',
				'desc'            => '',
				'default'         => '',
				'possible_values' => array(),
			) );
			$output = '';
			$output .= '<code>' . htmlspecialchars( $args['param'] ) . '</code>';
			if ( ! empty( $args['desc'] ) ) {
				$output .= ' - ' . $args['desc'];
			}
			if ( ! empty( $args['default'] ) ) {
				$output .= ' ' . __( 'Default value:', 'emails-verification-for-woocommerce' ) . ' ' . '<code>' . esc_html( $args['default'] ) . '</code>' . '.';
			}
			if ( ! empty( $args['possible_values'] ) && is_array( $args['possible_values'] ) ) {
				$output .= ' ' . __( 'Possible values:', 'emails-verification-for-woocommerce' ) . ' ' .
				           alg_wc_ev_array_to_string(
					           $args['possible_values'], array(
						           'item_template' => '<code>{value}</code>',
						           'glue'          => ', '
					           )
				           )
				           . '.';
			}

			return $output;
		}

	}

endif;

return new Alg_WC_Email_Verification_Settings_Shortcodes();