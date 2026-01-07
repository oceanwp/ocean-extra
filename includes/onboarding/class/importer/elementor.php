<?php
/**
 * Class for importing Elementor data.
 *
 * @package Ocean Extra
 */

namespace Elementor\TemplateLibrary;

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

use Elementor\Plugin;

/**
 * Class for importing Elementor data.
 */
class Ocean_Elementor_Import extends Source_Local {

	/**
	 * Term ID mappings
	 * @var array
	 */
	private $term_mappings = array();

	/**
	 * Set term mappings
	 *
	 * @param array $mappings Term ID mappings
	 */
	public function set_term_mappings( $mappings ) {
		$this->term_mappings = $mappings;
	}

	/**
	 * Update the Elementor meta data.
	 *
	 * @param integer $post_id the post id to update.
	 * @param array   $data the meta data to update.
	 */
	public function import( $post_id = 0, $data = array() ) {
		if ( empty( $post_id ) || empty( $data ) ) {
			return array();
		}

		if ( ! is_array( $data ) ) {
			$data = json_decode( $data, true );
		}

		if ( ! empty( $this->term_mappings ) ) {
			$data = $this->fix_menu_ids_recursive( $data );
		}

		$data = $this->replace_elements_ids( $data );
		$data = $this->process_export_import_content( $data, 'on_import' );

		$document = Plugin::$instance->documents->get( $post_id );
		if ( $document ) {
			$data = $document->get_elements_raw_data( $data, true );
		}

		update_metadata( 'post', $post_id, '_elementor_data', $data );

		Plugin::$instance->files_manager->clear_cache();

		return $data;
	}

	/**
	 * Recursively fix menu IDs in Elementor data
	 *
	 * @param mixed $data The data to process
	 * @return mixed
	 */
	private function fix_menu_ids_recursive( $data ) {

		if ( ! is_array( $data ) ) {
			return $data;
		}

		if ( empty( $this->term_mappings ) ) {
			return $data;
		}

		foreach ( $data as $key => &$value ) {

			if ( $key === 'settings' && is_array( $value ) ) {

				if ( isset( $value['nav_menu'] ) && is_scalar( $value['nav_menu'] ) ) {
					$old_id = absint( $value['nav_menu'] );

					if ( $old_id && isset( $this->term_mappings[ $old_id ] ) ) {
						$value['nav_menu'] = (string) $this->term_mappings[ $old_id ];
					}
				}

				if (
					isset( $value['wp'] ) &&
					is_array( $value['wp'] ) &&
					isset( $value['wp']['nav_menu'] ) &&
					is_scalar( $value['wp']['nav_menu'] )
				) {
					$old_id = absint( $value['wp']['nav_menu'] );

					if ( $old_id && isset( $this->term_mappings[ $old_id ] ) ) {
						$value['wp']['nav_menu'] = (string) $this->term_mappings[ $old_id ];
					}
				}
			}

			if ( is_array( $value ) ) {
				$value = $this->fix_menu_ids_recursive( $value );
			}
		}

		return $data;
	}
}