<?php
/**
 * Email Verification for WooCommerce - Account verification widget.
 *
 * @version 2.1.1
 * @since   2.1.1
 * @author  WPFactory
 */

/**
 * Adds Alg_WC_Email_Verification_Info_Widget widget.
 */
class Alg_WC_Email_Verification_Info_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 */
	public function __construct() {
		parent::__construct(
			'alg_wc_ev_verification_info_widget', // Base ID
			__( 'Verification info', 'emails-verification-for-woocommerce' ),
			array( 'description' => __( 'Account verification info', 'emails-verification-for-woocommerce' ) )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		echo do_shortcode( get_option( 'alg_wc_ev_verification_info_customization', alg_wc_ev()->core->get_verification_info_default() ) );
		echo $after_widget;
	}

	/**
	 * Back-end widget form.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'New title', 'emails-verification-for-woocommerce' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @version 2.1.1
	 * @since   2.1.1
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}

?>