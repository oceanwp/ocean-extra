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
		 * Enqueue scripts
		 *
		 * @since   2.2.9
		 */
		public function plugin_update_message( $plugin_data, $new_data ) {

			if ( isset( $plugin_data['update'] ) && $plugin_data['update']  ) {

				$this->plugin_update_content();

			}

		}

		/**
		 * Enqueue scripts
		 *
		 * @since   2.2.9
		 */
		public function ms_plugin_update_message( $file, $plugin ) {

			if ( is_multisite() && version_compare( $plugin['Version'], $plugin['new_version'], '<') ) {

				$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

				printf(
					'<tr class="plugin-update-tr">
						<td colspan="%s" class="plugin-update update-message notice inline notice-warning notice-alt">%s</td>
					</tr>',
					$wp_list_table->get_column_count(),
					$this->plugin_update_content()
				);
			}

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
