<?php
/**
 * NextGen NUX Patterns
 *
 * @since 1.0.0
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

/**
 * NUX_Patterns
 *
 * @package NextGen
 * @author  GoDaddy
 */
class NUX_Patterns {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'enqueue_block_editor_assets', [ $this, 'register_scripts' ] );
		add_action( 'admin_init', [ $this, 'deregister_go_layouts' ] );

	}

	/**
	 * Enqueue the scripts and styles.
	 */
	public function register_scripts() {

		$default_asset_file = [
			'dependencies' => [],
			'version'      => GD_NEXTGEN_VERSION,
		];

		// Editor Script.
		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/nux-patterns.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		wp_enqueue_script(
			'nextgen-nux-patterns',
			GD_NEXTGEN_PLUGIN_URL . 'build/nux-patterns.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true // Enqueue script in the footer.
		);

		wp_set_script_translations( 'nextgen-nux-patterns', 'nextgen', GD_NEXTGEN_PLUGIN_DIR . '/languages' );

		// Get imported template value from the 'wpnux_export_data' option.
		$wpnux_export_data = json_decode( get_option( 'wpnux_export_data' ) );

		wp_localize_script(
			'nextgen-nux-patterns',
			'nextgenNuxPatterns',
			[
				'nuxApiEndpoint' => apply_filters( 'nextgen_nux_api_endpoint', 'https://wpnux.godaddy.com/v2/api' ),
				'wpnuxTemplate'  => isset( $wpnux_export_data->_meta->template ) ? $wpnux_export_data->_meta->template : '',
			]
		);
	}

	/**
	 * Remove layouts registered by Go.
	 */
	public function deregister_go_layouts() {

		remove_filter( 'coblocks_layout_selector_layouts', 'go_coblocks_about_layouts' );
		remove_filter( 'coblocks_layout_selector_layouts', 'go_coblocks_contact_layouts' );
		remove_filter( 'coblocks_layout_selector_layouts', 'go_coblocks_home_layouts' );
		remove_filter( 'coblocks_layout_selector_layouts', 'go_coblocks_portfolio_layouts' );

	}
}
