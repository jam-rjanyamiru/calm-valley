<?php
/**
 * Plugin Name: NextGen
 * Plugin URI: https://www.godaddy.com
 * Description: Next Generation WordPress Experience
 * Author: GoDaddy
 * Author URI: https://www.godaddy.com
 * Version: 1.0.0
 * Text Domain: nextgen
 * Domain Path: /languages
 * Tested up to: 5.6.0
 *
 * NextGen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with Content Management. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Content_Management
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

define( 'GD_NEXTGEN_VERSION', '1.0.0' );
define( 'GD_NEXTGEN_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'GD_NEXTGEN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once __DIR__ . '/includes/autoload.php';

/**
 * Plugin
 *
 * @package NextGen
 * @author  GoDaddy
 */
final class Plugin {

	use Singleton;

	const SESSION_KEY = 'nextgen';

	/**
	 * Class constructor.
	 */
	private function __construct() {

		add_filter( 'attach_session_information', [ $this, 'maybe_enable_nextgen_session' ], 10 );
		add_action( 'init', [ $this, 'init' ] );

	}

	/**
	 * NextGen Initialization
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( $this->should_activate() ) {

			$this->enable_nextgen_session();

		}

		if ( ! $this->is_user_session_enabled() ) {

			return;

		}

		load_plugin_textdomain( 'nextgen', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		new Site_Design();
		new Site_Content();
		new Block_Editor();
		new Feedback_Modal();
		new Publish_Guide();
		new NUX_Patterns();
		new Logo_Menu();
		new Image_Categories();
		new Media_Download();

	}

	/**
	 * Whether or not we meet the basic criteria of NextGen capable site.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	private function should_activate() {

		global $wp_version;

		// Is the user using Go theme and went through wpnux onboarding?
		$has_go_theme_template = ( 'go' === get_option( 'stylesheet' ) && ! empty( get_option( 'wpnux_imported' ) ) );
		$has_wordpress_55      = version_compare( $wp_version, '5.5', '>=' );
		$has_gutenberg_plugin  = defined( 'GUTENBERG_VERSION' ) ? version_compare( GUTENBERG_VERSION, '8.3', '>=' ) : self::is_plugin_active( 'gutenberg/gutenberg.php' );
		$has_nextgen_query_arg = filter_input( INPUT_GET, 'nextgen', FILTER_VALIDATE_BOOLEAN );
		$has_nextgen_override  = apply_filters( 'nextgen_force_load', false );

		$has_min_requirements = (bool) ( $has_go_theme_template && ( $has_wordpress_55 || $has_gutenberg_plugin ) );
		$nextgen_compatible   = (bool) get_option( 'nextgen_compatible', false );

		// If persistant storage is different than current min requirements, reconciliate API with custom action event.
		if ( $has_min_requirements !== $nextgen_compatible ) {

			do_action( 'nextgen_compatibility_change', $has_min_requirements );
			update_option( 'nextgen_compatible', $has_min_requirements, true );

		}

		return ( $has_min_requirements && ( $has_nextgen_query_arg || $has_nextgen_override ) );
	}

	/**
	 * Add nextgen flag to user session if criteria for this plugin are met.
	 *
	 * @param array $session_info session info.
	 *
	 * @return mixed
	 */
	public function maybe_enable_nextgen_session( $session_info ) {

		if ( ! $this->should_activate() || ! (bool) apply_filters( 'nextgen_enable_session', true ) ) {

			return $session_info;

		}

		$session_info[ self::SESSION_KEY ] = true;

		return $session_info;

	}

	/**
	 * Determines if a given plugin is active or not.
	 * Note: This is a wrapper for is_plugin_active() WordPress core method.
	 *
	 * @param string $basename Plugin basename.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_plugin_active( $basename ) {

		if ( ! function_exists( 'is_plugin_active' ) ) {

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		}

		return is_plugin_active( $basename );

	}

	/**
	 * Modify user session to be NextGen.
	 *
	 * @since 1.0.0
	 */
	private function enable_nextgen_session() {

		list( $session, $manager, $token ) = $this->get_user_session();

		if ( ! $session ) {

			return;

		}

		$session[ self::SESSION_KEY ] = true;

		$manager->update( $token, $session );

	}

	/**
	 * Get the current session token.
	 *
	 * @return array|false|null
	 */
	private function get_user_session() {

		if ( ! is_user_logged_in() ) {

			return false;

		}

		$token = wp_get_session_token();

		if ( ! $token ) {

			return false;

		}

		$manager = \WP_Session_Tokens::get_instance( get_current_user_id() );

		return [ $manager->get( $token ), $manager, $token ];

	}

	/**
	 * Wheter or not the session is NextGen enabled.
	 *
	 * @return bool
	 */
	public function is_user_session_enabled() {

		list( $session ) = $this->get_user_session();

		if ( ! isset( $session['nextgen'] ) || ! $session['nextgen'] ) {

			return false;

		}

		return (bool) apply_filters( 'nextgen_load_plugin', true );

	}

}

Plugin::load();
