<?php
/**
 * NextGen Helpers
 *
 * @since 1.0.0
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

trait Helper {

	/**
	 * Validate REST Nonce from the request
	 *
	 * @param string $cap Capability to check as well of the nonce.
	 *
	 * @return void
	 */
	public static function validate_rest_nonce( $cap ) {

		if ( isset( $cap ) && ! current_user_can( $cap ) ) {

			wp_send_json_error( 'User priviledge not high enough' );

			exit;

		}

		$result = wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' );

		if ( ! $result ) {

			wp_send_json_error( 'Invalid nonce' );

			exit;

		}

	}

}
