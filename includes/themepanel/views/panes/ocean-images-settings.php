<?php
$ocean_images_settings = Ocean_Extra_New_Theme_Panel::get_ocean_images_settings();
?>

<div id="ocean-images-control" class="column-wrap clr">
	<form class="integration-settings" data-settings-for="ocean-images">
		<table class="form-table">
				<tbody>
					<tr id="owp_api_images_integration_tr">
						<th scope="row">
							<label for="owp_api_images_integration"><?php esc_html_e( 'Enable Ocean Images Module', 'oceanwp' ); ?></label>
						</th>
						<td>
							<select name="owp_integrations[api_images_integration]" id="owp_api_images_integration">
								<option <?php selected( $ocean_images_settings['api_images_integration'], '0', true ); ?> value="0">
									<?php esc_html_e( 'Disable', 'oceanwp' ); ?>
								</option>
								<option <?php selected( $ocean_images_settings['api_images_integration'], '1', true ); ?> value="1">
									<?php esc_html_e( 'Enable', 'oceanwp' ); ?>
								</option>							
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="form-table api-ingegrations">
			<tbody>
				<tr id="owp_flaticon_integration_tr">
					<th scope="row">
						<label for="owp_flaticon_integration"><?php esc_html_e( 'Enable Flaticon', 'oceanwp' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[flaticon_integration]" id="owp_flaticon_integration">
							<option <?php selected( $ocean_images_settings['flaticon_integration'], '0', true ); ?> value="0">
								<?php esc_html_e( 'Disable', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['flaticon_integration'], '1', true ); ?> value="1">
								<?php esc_html_e( 'Enable', 'oceanwp' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table api-ingegrations">
			<tbody>
				<tr id="owp_freepik_integration_tr">
					<th scope="row">
						<label for="owp_freepik_integration"><?php esc_html_e( 'Enable Freepik', 'oceanwp' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[freepik_integration]" id="owp_freepik_integration">
							<option <?php selected( $ocean_images_settings['freepik_integration'], '0', true ); ?> value="0">
								<?php esc_html_e( 'Disable', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_integration'], '1', true ); ?> value="1">
								<?php esc_html_e( 'Enable', 'oceanwp' ); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr id="owp_freepik_image_width_tr">
					<th scope="row">
						<label for="owp_freepik_image_width"><?php esc_html_e( 'Freepik Image Width', 'oceanwp' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[freepik_image_width]" id="owp_freepik_image_width">
							<option <?php selected( $ocean_images_settings['freepik_image_width'], 'origin', true ); ?> value="origin">
								<?php esc_html_e( 'Original', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], '500', true ); ?> value="500">
								<?php esc_html_e( '500px', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], '800', true ); ?> value="800">
								<?php esc_html_e( '800px', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], '1000', true ); ?> value="1000">
								<?php esc_html_e( '1000px', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], '1380', true ); ?> value="1380">
								<?php esc_html_e( '1380px', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], '1600', true ); ?> value="1600">
								<?php esc_html_e( '1600px', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], '2560', true ); ?> value="1600">
								<?php esc_html_e( '2560px', 'oceanwp' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], 'custom', true ); ?> value="custom">
								<?php esc_html_e( 'Custom Size', 'oceanwp' ); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr id="owp_freepik_image_width_custom_tr">
					<th scope="row">
						<label for="owp_freepik_image_width_custom"><?php esc_html_e( 'Width Size (px)', 'oceanwp' ); ?></label>
					</th>
					<td>
						<input class="regular-text" name="owp_integrations[freepik_image_width_custom]" min="1" step="1" value="<?php echo $ocean_images_settings['freepik_image_width_custom']; ?>" type="number" placeholder="<?php esc_attr_e( 'Enter image width in pixels', 'oceanwp' ); ?>"/>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
