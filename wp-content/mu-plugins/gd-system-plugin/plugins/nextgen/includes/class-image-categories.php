<?php
/**
 * NextGen image categories
 *
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

/**
 * NextGen Image Categories.
 */
class Image_Categories {


	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'localize_categories_script' ] );
		add_action( 'init', [ $this, 'register_image_category_setting' ], 11 );

	}

	/**
	 * Enqueue the script.
	 */
	public function localize_categories_script() {

		$default_asset_file = [
			'dependencies' => [],
			'version'      => GD_NEXTGEN_VERSION,
		];

		// Editor Script.
		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/layout-selector.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		wp_set_script_translations( 'image-categories-script', 'nextgen', GD_NEXTGEN_PLUGIN_DIR . '/languages' );

		wp_enqueue_script(
			'image-categories-script',
			GD_NEXTGEN_PLUGIN_URL . 'build/layout-selector.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true // Enqueue script in the footer.
		);

		wp_enqueue_style(
			'image-categories-script',
			GD_NEXTGEN_PLUGIN_URL . 'build/layout-selector-editor.css',
			[],
			$asset_file['version']
		);

	}

	/**
	 * Register a core site setting for an image category
	 */
	public function register_image_category_setting() {

		// Get imported template value from the 'wpnux_export_data' option.
		$wpnux_export_data = json_decode( get_option( 'wpnux_export_data' ) );

		register_setting(
			'image_category',
			'image_category',
			[
				'show_in_rest'      => true,
				'default'           => isset( $wpnux_export_data->_meta->template ) ? $wpnux_export_data->_meta->template : '',
				'type'              => 'string',
				'description'       => __( 'Image category.', 'nextgen' ),
				'sanitize_callback' => null,
			]
		);
	}

}
