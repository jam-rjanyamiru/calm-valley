<?php
/**
 * NextGen Feedback Modal
 *
 * @since 1.0.0
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

/**
 * Feedback_Modal
 *
 * @package NextGen
 * @author  GoDaddy
 */
class Feedback_Modal {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'enqueue_block_editor_assets', [ $this, 'register_scripts' ] );

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
		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/feedback-modal.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		$api_urls = [
			'local' => 'https://wpnux.test/api/feedback',
			'dev'   => 'https://wpnux.dev-godaddy.com/v2/api/feedback',
			'test'  => 'https://wpnux.test-godaddy.com/v2/api/feedback',
			'prod'  => 'https://wpnux.godaddy.com/v2/api/feedback',
		];

		$env = getenv( 'SERVER_ENV', true );

		$api_url = ! empty( $api_urls[ $env ] ) ? $api_urls[ $env ] : $api_urls['prod'];

		$data = [
			'api_url'    => apply_filters( 'nextgen_feedback_api_url', $api_url ),
			'site_uid'   => defined( 'GD_ACCOUNT_UID' ) && GD_ACCOUNT_UID ? GD_ACCOUNT_UID : '',
			'export_uid' => get_option( 'wpnux_export_uid', '' ),
			'versions'   => [
				'coblocks'     => defined( 'COBLOCKS_VERSION' ) ? COBLOCKS_VERSION : '',
				'go_theme'     => defined( 'GO_VERSION' ) ? GO_VERSION : '',
				'wpaas_plugin' => class_exists( '\WPaaS\Plugin' ) ? \WPaaS\Plugin::version() : '',
			],
		];

		wp_enqueue_script(
			'nextgen-feedback-modal',
			GD_NEXTGEN_PLUGIN_URL . 'build/feedback-modal.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true // Enqueue script in the footer.
		);

		wp_localize_script(
			'nextgen-feedback-modal',
			'nextgenFeedbackModalData',
			$data
		);

		wp_set_script_translations( 'nextgen-feedback-modal', 'nextgen', GD_NEXTGEN_PLUGIN_DIR . '/languages' );

		// Editor Styles.
		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/feedback-modal-editor.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		wp_enqueue_style(
			'nextgen-feedback-modal-style',
			GD_NEXTGEN_PLUGIN_URL . 'build/feedback-modal-editor.css',
			[],
			$asset_file['version']
		);

	}

}
