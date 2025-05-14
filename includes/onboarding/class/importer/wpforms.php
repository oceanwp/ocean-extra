<?php
/**
 * Class for the WPForms importer.
 *
 * Thank you very much to SiteGround for the code.
 */

if ( ! class_exists( 'WPForms' ) ) {
	return;
}

class Ocean_WPForms_Importer {

	/**
	 * Process import file - this parses the widget data and returns it.
	 *
	 * @param string $file path to json file.
	 * @global string $widget_import_results
	 */
	public function process_import_file( $file ) {

		if ( ! file_exists( $file ) ) {
            return new WP_Error( 'file_not_found', __( 'The specified file does not exist', 'ocean-extra' ) );
        }

        // Read file contents.
        $data = file_get_contents( $file );
        if ( false === $data ) {
            return new WP_Error( 'file_read_error', __( 'Unable to read form data file.', 'ocean-extra' ) );
        }

        // Decode JSON data.
        $form_data = json_decode( $data, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'json_parse_error', __( 'Invalid form data format.', 'ocean-extra' ) );
        }

        // Import the widget data.
        return $this->import_json( $form_data );

	}

	public function import_json( $forms ) {

		if ( ! function_exists( 'wpforms' ) ) {
			return;
		}

		foreach ( $forms as $form ) {

			// Create empty form so we have an ID to work with.
			$form_id = wp_insert_post(
				array(
					'post_status' => 'publish',
					'post_type'   => 'wpforms',
				)
			);

			// Bail if post creation has failed.
			if ( empty( $form_id )
				|| is_wp_error( $form_id ) ) {
				continue;
			}

			$form['id'] = $form_id;

			// Update the form with all our compiled data.
			wpforms()->form->update( $form['id'], $form );
		}
	}
}
