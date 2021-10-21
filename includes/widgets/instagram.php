<?php
/**
 * Instagram Widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Instagram_Widget' ) ) {
	class Ocean_Extra_Instagram_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct(
	            'ocean_instagram',
	            $name = __( '&raquo; Instagram', 'ocean-extra' ),
	            array(
	                'classname'		=> 'widget-oceanwp-instagram instagram-widget',
					'description'	=> esc_html__( 'Displays Instagram photos.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
	            )
	        );

	        add_action( 'admin_enqueue_scripts', array( $this, 'ocean_extra_instagram_js' ) );

		}

	    /**
	     * Upload the Javascripts for the media uploader
	     */
	    public function ocean_extra_instagram_js() {
	        // wp_enqueue_script( 'oe-insta-admin-script', OE_URL .'includes/widgets/js/insta-admin.min.js', array( 'jquery' ), false, true );
	    }

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			$title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';

			// Before widget WP hook
			echo $args['before_widget'];

				// Show widget title
				if ( $title ) {
					echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
				}

				// Display the widget
				echo $this->display_widget( $instance );

			// After widget WP hook
			echo $args['after_widget'];
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance 						= $old_instance;
			$instance['title'] 				= strip_tags($new_instance['title']);
			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			return $instance;
		}

		/**
		 * Display the widget.
		 */
		private function display_widget( $args ) {
			if( current_user_can('editor') || current_user_can('administrator') ) {
				return '<p>' . __('Instagram Widget is deprecated, please remove this widget from your site.') . '</p>';
			}

			return '';
		}

	}
}
register_widget( 'Ocean_Extra_Instagram_Widget' );
