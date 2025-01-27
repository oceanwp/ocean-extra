<?php
/**
 * OceanWP plugin update message
 *
 * @package OceanWP WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OE_Plugin_Update_Message' ) ) :

	class OE_Plugin_Update_Message {

		/**
		 * Setup class.
		 *
		 * @since   2.3.0
		 */
		public function __construct() {

			add_action( 'in_plugin_update_message-ocean-extra/ocean-extra.php', array( $this, 'plugin_update_message' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'plugin_update_asset' ) );
		}

		/**
		 * Message content
		 */
		public function plugin_update_content() {
			?>
			<hr class="owp-update-warning__separator">
			<div class="owp-update-warning">
				<div class="warning-info-icon">
					<span class="dashicons dashicons-info"></span>
				</div>
				<div>
					<div class="warning__title">
						<?php echo esc_html__( 'Backup recommended before plugin update.', 'ocean-extra' ); ?>
					</div>
					<div class="warning__message">
						<?php
						printf(
							/* translators: %1$s Link open tag, %2$s: Link close tag. */
							esc_html__( 'The latest update introduces significant improvements and changes to various plugin features.  For a smooth update process, it\'s crucial to %1$s backup your website beforehand  %2$s and test the update in a staging or test environment if available.', 'ocean-extra' ),
							'<a href="https://docs.oceanwp.org/article/875-how-to-safely-update-wordpress-website" target="_blank">',
							'</a>'
						);
						?>
					</div>
				</div>
			</div>

			<hr class="owp-update-warning__separator">
			<div class="owp-update-warning">
				<div class="warning-info-icon green">
					<span class="dashicons dashicons-yes-alt"></span>
				</div>
				<div>
					<div class="warning__title">
						<?php echo esc_html__( 'What\'s new?', 'ocean-extra' ); ?>
					</div>
					<div class="warning__message">
						<?php
						printf(
							/* translators: %1$s Link open tag, %2$s: Link close tag. */
							esc_html__( 'Revamped Customizer for enhanced experience! This update delivers a completely redesigned Customizer with a focus on improved user interface (UI), user experience (UX), and performance. Enjoy a faster and more intuitive way to personalize your website with a wider range of options at your fingertips. Learn %1$s how to properly update your websites and transition to OceanWP 4 %4$s, view %2$s OceanWP 4 New Customizer details %4$s or check out the %3$s OceanWP 4 Customizer documentation %4$s.', 'ocean-extra' ),
							'<a href="https://oceanwp.org/blog/oceanwp-4-release-announcement/" target="_blank">',
							'<a href="https://oceanwp.org/blog/customize-wordpress-new-core-update/" target="_blank">',
							'<a href="https://docs.oceanwp.org/category/894-oceanwp-customizer" target="_blank">',
							'</a>'
						);
						?>
					</div>
					<div class="owp-required-products">
						<table class="owp-required-version-table">
							<tbody>
								<tr>
									<th><?php echo esc_html__( 'Items', 'ocean-extra' ); ?></th>
									<th><?php echo esc_html__( 'Required Version', 'ocean-extra' ); ?></th>
								</tr>
								<tr>
									<td><?php echo esc_html__( 'OceanWP', 'ocean-extra' ); ?></td>
									<td><?php echo esc_html__( '4.0.0', 'ocean-extra' ); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Tested up to.
		 */
		public function plugin_tested_up_to_content() {

			$current_theme_version = oe_get_theme_version();
			$requires_at_least     = $this->oe_get_plugin_header_data( 'ocean-extra/ocean-extra.php', 'RequiresOWP' );

			?>

			<hr class="owp-update-warning__separator">
			<div class="owp-update-warning">
				<div class="warning-info-icon">
					<span class="dashicons dashicons-info"></span>
				</div>
				<div>
					<div class="warning__title">
						<?php echo esc_html__( 'Compatibility Alert.', 'ocean-extra' ); ?>
					</div>
					<div class="warning__message">
						<?php
						printf(
							esc_html__(
								'This plugin update requires compatibility with specific OceanWP theme versions. Please ensure your theme meets the following requirements before proceeding:',
								'ocean-extra'
							),

						);
						?>
					</div>
					<div class="owp-required-products">
						<table class="owp-required-version-table">
							<tbody>
								<tr>
									<th><?php echo esc_html__( 'Items', 'ocean-extra' ); ?></th>
									<th><?php echo esc_html__( 'Requires at Least', 'ocean-extra' ); ?></th>
								</tr>
								<tr>
									<td><?php echo esc_html__( 'OceanWP', 'ocean-extra' ); ?></td>
									<td><?php echo esc_attr( $requires_at_least ); ?></td>
								</tr>

							</tbody>
						</table>
					</div>
				</div>
			</div>

			<?php
		}

		/**
		 * Enqueue scripts
		 *
		 * @since   2.2.9
		 */
		public function plugin_update_message( $plugin_data, $new_data ) {

			$current_theme_version = oe_get_theme_version();

			if ( ! empty( $current_theme_version ) && version_compare( $current_theme_version, '3.6.1', '<=' ) ) {
				if ( isset( $plugin_data['update'] ) && $plugin_data['update']  ) {
					$this->plugin_update_content();
				}
			}

			$requires_at_least = $this->oe_get_plugin_header_data( 'ocean-extra/ocean-extra.php', 'RequiresOWP' );
			if ( ! empty( $current_theme_version ) && version_compare( $current_theme_version, $requires_at_least, '<=' ) ) {
				if ( isset( $plugin_data['update'] ) && $plugin_data['update']  ) {
					$this->plugin_tested_up_to_content();
				}
			}

		}

		public function oe_get_plugin_header_data( $plugin_slug, $key_name ) {

			$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_slug;

			$headers = array(
				'RequiresOWP' => 'OceanWP requires at least',
			);

			$plugin_data = get_file_data( $plugin_file, $headers );

			$get_data = isset( $plugin_data[$key_name] ) ? $plugin_data[$key_name] : false;

			return $get_data;
		}

		/**
		 * Script
		 */
		public function plugin_update_asset() {
			$screen = get_current_screen();

			if ( 'plugins' === $screen->id || 'plugins-network' === $screen->id ) {
				wp_enqueue_style(
					'oe-plugin-update',
					plugins_url( '/assets/css/pluginUpdateMessage.min.css', __DIR__ ),
					array(),
					false
				);
			}
		}

	}

endif;

new OE_Plugin_Update_Message();
