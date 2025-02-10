<?php
$cloudflare_turnstile_settings = Ocean_Extra_New_Theme_Panel::get_cloudlfare_turnstile_settings();
?>

<p class="oceanwp-tp-block-description">
	<?php
	echo sprintf(
		esc_html__( '%1$sTurnstile%2$s is a free service by Cloudflare that protects your website from spam and abuse. It does this while letting your valid users pass through with ease.', 'ocean-extra' ),
		'<a href="https://developers.cloudflare.com/turnstile/get-started/" target="_blank">',
		'</a>'
	);
	?>
</p>


<div id="ocean-cloudlfare-turnstile-control" class="column-wrap clr">
	<form class="integration-settings" data-settings-for="cloudlfare_turnstile">

		<div id="owp_cloudlfare_turnstile_area">
			<table class="form-table">
				<tbody>
					<tr id="owp_cloudfare_turnstile_site_key_tr">
						<th scope="row">
							<label for="owp_turnstile_site_key"><?php esc_html_e( 'Site Key', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<input name="owp_integrations[turnstile_site_key]" type="text" id="owp_turnstile_site_key" value="<?php echo esc_attr( $cloudflare_turnstile_settings['turnstile_site_key'] ); ?>" class="regular-text">
						</td>
					</tr>
					<tr id="owp_cloudflare_turnstile_secret_key_tr">
						<th scope="row">
							<label for="owp_turnstile_secret_key"><?php esc_html_e( 'Secret Key', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<input name="owp_integrations[turnstile_secret_key]" type="text" id="owp_turnstile_secret_key" value="<?php echo esc_attr( $cloudflare_turnstile_settings['turnstile_secret_key'] ); ?>" class="regular-text">
						</td>
					</tr>

					<tr id="owp_cloudflare_turnstile_render_method_tr">
						<th scope="row">
							<label for="owp_turnstile_render_method"><?php esc_html_e( 'Render Method', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<select name="owp_integrations[turnstile_render_method]" id="owp_turnstile_render_method">
								<option <?php selected( $cloudflare_turnstile_settings['turnstile_render_method'], 'implicit', true ); ?> value="implicit">
									<?php esc_html_e( 'Implicit', 'ocean-extra' ); ?>
								</option>
								<option <?php selected( $cloudflare_turnstile_settings['turnstile_render_method'], 'explicit', true ); ?> value="explicit">
									<?php esc_html_e( 'Explicit', 'ocean-extra' ); ?>
								</option>
							</select>
						</td>
					</tr>

					<tr id="owp_cloudflare_turnstile_theme_tr">
						<th scope="row">
							<label for="owp_turnstile_theme"><?php esc_html_e( 'Theme', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<select name="owp_integrations[turnstile_theme]" id="owp_turnstile_theme">
								<option <?php selected( $cloudflare_turnstile_settings['turnstile_theme'], '', true ); ?> value="">
									<?php esc_html_e( 'Auto', 'ocean-extra' ); ?>
								</option>
								<option <?php selected( $cloudflare_turnstile_settings['turnstile_theme'], 'light', true ); ?> value="light">
									<?php esc_html_e( 'Light', 'ocean-extra' ); ?>
								</option>
								<option <?php selected( $cloudflare_turnstile_settings['turnstile_theme'], 'dark', true ); ?> value="dark">
									<?php esc_html_e( 'Dark', 'ocean-extra' ); ?>
								</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php submit_button(); ?>
	</form>
</div>
