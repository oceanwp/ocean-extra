<?php
/**
 * Class for the settings importer.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ocean_Settings_Importer' ) ) {

    /**
     * OWP_Settings_Importer class.
     *
     * This class handles the import of settings from a .dat file.
     */
    class Ocean_Settings_Importer {

        /**
         * Process the import file, parse the settings data, and return the result.
         *
         * @param string $file Path to the settings .dat file.
         */
        public function process_import_file( $file = '' ) {

            if ( ! file_exists( $file ) ) {
                return new WP_Error( 'file_not_found', __( 'The specified file does not exist', 'ocean-extra' ) );
            }

            // Read the file content. Check for errors.
            $raw = file_get_contents( $file );
            if ( false === $raw ) {
                return new WP_Error( 'file_read_error', __( 'Unable to read the file', 'ocean-extra' ) );
            }

            // Try to unserialize the data (if valid).
            $data = @unserialize( $raw, [ 'allowed_classes' => false ] );
            if ( false === $data ) {
                return new WP_Error( 'unserialize_error', __( 'Failed to unserialize data from file', 'ocean-extra' ) );
            }

            // Check and process wp_css if available.
            if ( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && ! empty( $data['wp_css'] ) ) {
                wp_update_custom_css_post( $data['wp_css'] );
            }

            // Import the settings data
            return $this->import_data( $data['mods'] );
        }

        /**
         * Import the settings data into WordPress theme mods.
         *
         * @param array $data The settings data.
         */
        private function import_data( $data ) {
            // Ensure there is valid data to import.
            if ( empty( $data ) ) {
                return new WP_Error( 'empty_data', __( 'No settings data found to import', 'ocean-extra' ) );
            }

            foreach ( $data as $mod => $value ) {
                set_theme_mod( $mod, $value );
            }

            return $data;
        }

    }
}
