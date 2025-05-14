<?php
/**
 * Class for the widget importer.
 *
 * Code is mostly from the Widget Importer & Exporter plugin.
 *
 * @see https://wordpress.org/plugins/widget-importer-exporter/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Ocean_Widget_Importer {

    /**
     * Process import file - this parses the widget data and returns it.
     *
     * @param string $file Path to the JSON file.
     */
    public function process_import_file( $file ) {
        if ( ! file_exists( $file ) ) {
            return new WP_Error( 'file_not_found', __( 'The specified file does not exist', 'ocean-extra' ) );
        }

        // Read file contents.
        $data = file_get_contents( $file );
        if ( false === $data ) {
            return new WP_Error( 'file_read_error', __( 'Unable to read widget data file.', 'ocean-extra' ) );
        }

        // Decode JSON data.
        $widget_data = json_decode( $data, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'json_parse_error', __( 'Invalid widget data format.', 'ocean-extra' ) );
        }

        // Import the widget data.
        return $this->import_data( $widget_data );
    }

    /**
     * Import widget JSON data.
     *
     * @global array $wp_registered_sidebars
     * @param array $data JSON widget data.
     * @return array $results Import results.
     */
    private function import_data( $data ) {
        global $wp_registered_sidebars;

        if ( empty( $data ) || ! is_array( $data ) ) {
            return new WP_Error( 'invalid_data', __( 'Invalid or empty widget data.', 'ocean-extra' ) );
        }

        $available_widgets = $this->available_widgets();
        $results = [];

        $widget_instances = [];
        foreach ( $available_widgets as $widget ) {
            $widget_instances[ $widget['id_base'] ] = get_option( 'widget_' . $widget['id_base'], [] );
        }

        foreach ( $data as $sidebar_id => $widgets ) {
            if ( 'wp_inactive_widgets' === $sidebar_id ) {
                continue;
            }

            $sidebar_exists = isset( $wp_registered_sidebars[ $sidebar_id ] );
            $use_sidebar_id = $sidebar_exists ? $sidebar_id : 'wp_inactive_widgets';

            $results[ $sidebar_id ] = [
                'name'         => $sidebar_exists ? $wp_registered_sidebars[ $sidebar_id ]['name'] : $sidebar_id,
                'message_type' => $sidebar_exists ? 'success' : 'error',
                'message'      => $sidebar_exists ? '' : __( 'Sidebar does not exist in theme. Moving widget to Inactive.', 'ocean-extra' ),
                'widgets'      => [],
            ];

            foreach ( $widgets as $widget_instance_id => $widget ) {
                $widget_result = $this->import_widget_instance( $widget_instance_id, $widget, $available_widgets, $widget_instances, $use_sidebar_id );
                $results[ $sidebar_id ]['widgets'][ $widget_instance_id ] = $widget_result;
            }
        }

        return $results;
    }

    /**
     * Import a single widget instance.
     *
     * @param string $widget_instance_id Widget instance ID.
     * @param array  $widget Widget data.
     * @param array  $available_widgets Available widgets.
     * @param array  $widget_instances Existing widget instances.
     * @param string $sidebar_id Target sidebar.
     * @return array Import result for the widget.
     */
    private function import_widget_instance( $widget_instance_id, $widget, $available_widgets, &$widget_instances, $sidebar_id ) {
        $id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
        $instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

        if ( ! array_key_exists( $id_base, $available_widgets ) ) {
            return [
                'name'         => $id_base,
                'title'        => __( 'Unknown Widget', 'ocean-extra' ),
                'message_type' => 'error',
                'message'      => __( 'This widget type is not supported on this site.', 'ocean-extra' ),
            ];
        }

        $widget = json_decode( json_encode( $widget ), true );

        if ( isset( $widget_instances[ $id_base ] ) ) {
            $existing_instances = get_option( 'sidebars_widgets' )[ $sidebar_id ] ?? [];

            foreach ( $widget_instances[ $id_base ] as $existing_id => $existing_widget ) {
                if ( in_array( "$id_base-$existing_id", $existing_instances, true ) && $existing_widget === $widget ) {
                    return [
                        'name'         => $available_widgets[ $id_base ]['name'],
                        'title'        => $widget['title'] ?? __( 'No Title', 'ocean-extra' ),
                        'message_type' => 'warning',
                        'message'      => __( 'Widget already exists.', 'ocean-extra' ),
                    ];
                }
            }
        }

        $widget_instances[ $id_base ][] = $widget;
        end( $widget_instances[ $id_base ] );
        $new_instance_id_number = key( $widget_instances[ $id_base ] );

        update_option( 'widget_' . $id_base, $widget_instances[ $id_base ] );

        $sidebars_widgets = get_option( 'sidebars_widgets', [] );
        $sidebars_widgets[ $sidebar_id ][] = "$id_base-$new_instance_id_number";
        update_option( 'sidebars_widgets', $sidebars_widgets );

        return [
            'name'         => $available_widgets[ $id_base ]['name'],
            'title'        => $widget['title'] ?? __( 'No Title', 'ocean-extra' ),
            'message_type' => 'success',
            'message'      => __( 'Widget imported successfully.', 'ocean-extra' ),
        ];
    }

    /**
     * Get all available widgets.
     *
     * @return array Available widgets.
     */
    private function available_widgets() {
        global $wp_registered_widget_controls;

        $available_widgets = [];

        foreach ( $wp_registered_widget_controls as $widget ) {
            if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base']] ) ) {
                $available_widgets[ $widget['id_base'] ] = [
                    'id_base' => $widget['id_base'],
                    'name'    => $widget['name'],
                ];
            }
        }

        return $available_widgets;
    }
}
