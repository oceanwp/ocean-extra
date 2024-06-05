<?php
/**
 * Flickr Widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Flickr_Widget' ) ) {
	class Ocean_Extra_Flickr_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'ocean_flickr',
				esc_html__( '&raquo; Flickr', 'ocean-extra' ),
				array(
					'classname'                   => 'widget-oceanwp-flickr flickr-widget',
					'description'                 => esc_html__( 'Pulls in images from your flickr account.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
			);

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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

			$title  = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$number = isset( $instance['number'] ) ? intval( $instance['number'] ) : '';
			$id     = isset( $instance['id'] ) ? sanitize_text_field( $instance['id'] ) : '';


			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
			}

			$unique_id = 'oceanwp_flickr_photos_' . uniqid() . '_' . md5(rand());

			if ( $id ) : ?>
				<div class="oceanwp-flickr-wrap">
					<div id="<?php echo $unique_id; ?>" class="oceanwp-flickr-container" data-user-id="<?php echo esc_attr( $id ); ?>" data-max-photos="<?php echo intval( $number ); ?>"></div>
					<p class="flickr_stream_wrap"><a class="follow_btn" href="http://www.flickr.com/photos/<?php echo strip_tags( $id ); ?>"><?php esc_html_e( 'View stream on flickr', 'ocean-extra' ); ?></a></p>
				</div>
				<?php
			endif;

			echo $args['after_widget'];


		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 * @since 1.0.0
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance           = $old_instance;
			$instance['title']  = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['number'] = ! empty( $new_instance['number'] ) ? intval( $new_instance['number'] ) : '';
			$instance['id']     = ! empty( $new_instance['id'] ) ? sanitize_text_field( $new_instance['id'] ) : '';
			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 * @since 1.0.0
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$settings = wp_parse_args(
				(array) $instance,
				array(
					'title'  => esc_attr__( 'Flickr Photos', 'ocean-extra' ),
					'id'     => '73064996@N08',
					'number' => 6,
				)
			);

			$title  = esc_attr( $settings['title'] );
			$id     = esc_attr( $settings['id'] );
			$number = intval( $settings['number'] );

			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ocean-extra' ); ?>:</label>
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'Flickr ID', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" type="text" value="<?php echo esc_attr( $id ); ?>" />
				<small><?php esc_html_e( 'Enter the url of your Flickr page on this site: idgettr.com.', 'ocean-extra' ); ?></small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number:', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" value="<?php echo esc_attr( $number ); ?>" />
				<small><?php esc_html_e( 'The maximum is 10 images.', 'ocean-extra' ); ?></small>
			</p>

			<?php

		}

		/**
		 * Scripts
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'flickr-widget-script', OE_URL . 'includes/widgets/js/flickr.min.js', array( 'jquery' ), false, true );

			$widgets_settings = $this->get_settings();
			$localized_data = array();

			foreach ( $widgets_settings as $number => $instance ) {
				$localized_data[] = array(
					'userId' => ! empty( $instance['id'] ) ? esc_attr( $instance['id'] ) : '',
					'maxPhotos' => ! empty( $instance['number'] ) ? intval( $instance['number'] ) : 6,
					'containerId' => 'oceanwp-flickr-photos-' . esc_attr( $number ),
				);
			}

			wp_localize_script( 'flickr-widget-script', 'flickrWidgetParams', array(
				'widgets' => $localized_data,
			));
		}

	}
}
register_widget( 'Ocean_Extra_Flickr_Widget' );
