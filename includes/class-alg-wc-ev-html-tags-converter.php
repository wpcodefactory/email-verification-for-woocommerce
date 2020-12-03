<?php
/**
 * Email Verification for WooCommerce - HTML Tags Converter
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Email_Verification_HTML_Tags_Converter' ) ) :

	class Alg_WC_Email_Verification_HTML_Tags_Converter {

		private $args;

		/**
		 * init.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param null $args
		 */
		function init( $args = null ) {
			$args       = wp_parse_args( $args, array(
				'wc_tab_id'          => '', // The tab id from woocommerce settings. E.g "alg_wc_ev" for Email Verification plugin
				'replacement_params' => apply_filters( 'alg_wc_ev_html_replacement_params', array(
					'<' => '{',
					'>' => '}'
				) ),
				'database'           => array(
					'converter_id'    => '', // The option id (probably a checkbox) responsible for converting the html tags or not
					'replacement_ids' => array(), // The option ids going to be replaced
				)
			) );
			$this->args = $args;
			foreach ( $args['database']['replacement_ids'] as $option_name ) {
				add_filter( "woocommerce_admin_settings_sanitize_option_{$option_name}", array( $this, 'sanitize_option' ), 20, 3 );
				add_filter( "option_{$option_name}", array( $this, 'unsanitize_option' ), 20, 2 );
			}
		}

		/**
		 * unsanitize_option.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $value
		 * @param $option
		 *
		 * @return mixed
		 */
		function unsanitize_option( $value, $option ) {
			if (
				isset( $_GET['tab'] )
				&& ! empty( $wc_tab_id = $_GET['tab'] )
				&& $wc_tab_id == $this->args['wc_tab_id']
			) {
				return $value;
			} else {
				if ( 'yes' == get_option( $this->args['database']['converter_id'], 'no' ) ) {
					$value = $this->convert_html_tags( array(
						'value'  => $value,
						'action' => 'decode'
					) );
				}
			}
			return $value;
		}

		/**
		 * sanitize_option.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $value
		 * @param $option
		 * @param $raw_value
		 *
		 * @return mixed
		 */
		function sanitize_option( $value, $option, $raw_value ) {
			if ( 'yes' == get_option( $this->args['database']['converter_id'], 'no' ) ) {
				$new_value = $this->convert_html_tags( array(
					'value'  => $raw_value,
					'action' => 'encode'
				) );
			} else {
				$new_value = $this->convert_html_tags( array(
					'value'  => $raw_value,
					'action' => 'decode'
				) );
			}
			return $new_value;
		}

		/**
		 * convert_html_tags.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param null $args
		 *
		 * @return mixed
		 */
		function convert_html_tags( $args = null ) {
			$args    = wp_parse_args( $args, array(
				'replacement_params' => $this->args['replacement_params'],
				'value'              => '',
				'action'             => 'encode' // encode || decode
			) );
			$search  = array_keys( $args['replacement_params'] );
			$replace = array_values( $args['replacement_params'] );
			if ( 'decode' == $args['action'] ) {
				$search  = array_values( $args['replacement_params'] );
				$replace = array_keys( $args['replacement_params'] );
			}
			return str_replace( $search, $replace, $args['value'] );
		}
	}

endif;

return new Alg_WC_Email_Verification_HTML_Tags_Converter();