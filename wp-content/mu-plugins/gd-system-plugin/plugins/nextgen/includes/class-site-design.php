<?php
/**
 * NextGen Site Design
 *
 * @since 1.0.0
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

/**
 * Site_Design
 *
 * @package NextGen
 * @author  GoDaddy
 */
class Site_Design {

	use Helper;

	const USER_CAP             = 'customize';
	const EDITOR_WRAPPER_CLASS = 'editor-styles-wrapper';

	const FONTS = [
		[
			'Poppins'   => [
				'600',
			],
			'Quicksand' => [
				'400',
				'600',
			],
		],
		[
			'Heebo'      => [
				'400',
				'800',
			],
			'Fira Code'  => [
				'400',
				'400i',
				'700',
			],
			'Montserrat' => [
				'400',
				'700',
			],
		],
		[
			'Work Sans' => [
				'300',
				'700',
			],
			'Karla'     => [
				'400',
				'400i',
				'700',
			],
		],
		[
			'Trocchi'         => [
				'400',
				'600',
			],
			'Noto Sans'       => [
				'400',
				'400i',
				'700',
			],
			'Source Code Pro' => [
				'400',
				'700',
			],
		],
		[
			'Crimson Text' => [
				'400',
				'400i',
				'700',
				'700i',
			],
			'Nunito Sans'  => [
				'400',
				'400i',
				'600',
				'700',
			],
		],
	];

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'register_scripts' ] );
		add_action( 'wp_ajax_site_design_update_design_style', [ $this, 'update_design_style' ] );

		/**
		 * Remove Go theme inline editor styles
		 */
		add_action(
			'wp_loaded',
			function() {
				remove_action( 'admin_init', 'Go\Core\editor_styles' );
			}
		);

		/**
		 * Add the shared styles to the editor
		 */
		add_action(
			'admin_init',
			function() {
				$suffix = SCRIPT_DEBUG ? '' : '.min';
				$rtl    = ! is_rtl() ? '' : '-rtl';
				// Enqueue  shared editor styles.
				add_editor_style(
					"dist/css/style-editor{$rtl}{$suffix}.css"
				);
			}
		);

	}

	/**
	 * Enqueue the scripts and styles.
	 */
	public function register_scripts() {

		if ( ! current_user_can( self::USER_CAP ) ) {

			return;

		}

		$default_asset_file = [
			'dependencies' => [],
			'version'      => GD_NEXTGEN_VERSION,
		];

		// short-circuit.
		$active_theme = wp_get_theme();
		if ( 'Go' !== $active_theme->get( 'Name' ) ) {
			return;
		}

		// Editor Script.
		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/site-design.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		wp_enqueue_script(
			'nextgen-site-design',
			GD_NEXTGEN_PLUGIN_URL . 'build/site-design.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true // Enqueue script in the footer.
		);

		wp_set_script_translations( 'nextgen-site-design', 'nextgen', GD_NEXTGEN_PLUGIN_DIR . '/languages' );

		$current_design_style = \Go\Core\get_design_style();

		$data = [
			'editorClass'           => self::EDITOR_WRAPPER_CLASS,
			'availableDesignStyles' => \Go\Core\get_available_design_styles(),
			'currentDesignStyle'    => get_theme_mod( 'design_style', \Go\Core\get_default_design_style() ),
			'currentColorScheme'    => get_theme_mod( 'color_scheme', \Go\Core\get_default_color_scheme() ),
			'currentColors'         => [
				'primary'    => get_theme_mod( 'primary_color' ),
				'secondary'  => get_theme_mod( 'secondary_color' ),
				'tertiary'   => get_theme_mod( 'tertiary_color' ),
				'background' => get_theme_mod( 'background_color' ),
			],
		];

		if ( version_compare( GO_VERSION, '1.3.6', '>=' ) ) {
			$data = array_merge(
				$data,
				[
					'currentFonts' => get_theme_mod( 'fonts', $current_design_style['fonts'] ),
					'fontSize'     => get_theme_mod( 'font_size', $current_design_style['font_size'] ),
					'typeRatio'    => get_theme_mod( 'type_ratio', $current_design_style['type_ratio'] ),
					'fonts'        => self::FONTS,
				]
			);
		}

		wp_localize_script(
			'nextgen-site-design',
			'nextgenSiteDesign',
			$data
		);

		// Editor Styles.
		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/site-design-editor.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		wp_enqueue_style(
			'nextgen-site-design-editor',
			GD_NEXTGEN_PLUGIN_URL . 'build/site-design-editor.css',
			[],
			$asset_file['version']
		);
	}

	/**
	 * Retreive the selected design style styles and return them for injection into the DOM
	 */
	public function update_design_style() {

		self::validate_rest_nonce( self::USER_CAP );

		$selected_style = filter_input( INPUT_POST, 'design_style', FILTER_SANITIZE_STRING );
		$color_palette  = filter_input( INPUT_POST, 'color_palette', FILTER_SANITIZE_STRING );
		$fonts          = filter_input( INPUT_POST, 'fonts', FILTER_VALIDATE_INT );
		$font_size      = filter_input( INPUT_POST, 'font_size', FILTER_SANITIZE_STRING );
		$type_ratio     = filter_input( INPUT_POST, 'type_ratio', FILTER_VALIDATE_FLOAT );
		$should_update  = filter_input( INPUT_POST, 'should_update', FILTER_VALIDATE_BOOLEAN );
		$initial_load   = filter_input( INPUT_POST, 'initial_load', FILTER_VALIDATE_BOOLEAN );

		$custom_colors = [
			'primary_color'    => filter_input( INPUT_POST, 'primary_color', FILTER_SANITIZE_STRING ),
			'secondary_color'  => filter_input( INPUT_POST, 'secondary_color', FILTER_SANITIZE_STRING ),
			'tertiary_color'   => filter_input( INPUT_POST, 'tertiary_color', FILTER_SANITIZE_STRING ),
			'background_color' => filter_input( INPUT_POST, 'background_color', FILTER_SANITIZE_STRING ),
		];

		if ( ! $selected_style ) {

			wp_send_json_error();
			exit;

		}

		$design_styles = \Go\Core\get_available_design_styles();

		if ( ! isset( $design_styles[ $selected_style ] ) ) {

			wp_send_json_error();
			exit;

		}

		$design_style = $design_styles[ $selected_style ];

		if ( $should_update ) {

			set_theme_mod( 'design_style', $selected_style );
			set_theme_mod( 'color_scheme', $color_palette );

			if ( isset( $fonts, $font_size, $type_ratio ) && array_key_exists( $fonts, self::FONTS ) ) {

				set_theme_mod( 'fonts', self::FONTS[ $fonts ] );
				set_theme_mod( 'font_size', $font_size );
				set_theme_mod( 'type_ratio', $type_ratio );

			}
		}

		if ( $should_update ) {
			foreach ( $custom_colors as $theme_mod => $color ) {
				$theme_mod_string = str_replace( '_color', '', $theme_mod );
				$color            = ! empty( $color ) ? $color : $design_style['color_schemes'][ $color_palette ][ $theme_mod_string ];

				set_theme_mod( $theme_mod, $color );
			}
		}

		ob_start();
		include_once sprintf( '%1$s/go/%2$s', get_theme_root(), str_replace( '.min', '', $design_style['editor_style'] ) );
		$stylesheet = ob_get_clean();

		$fonts_string = [];

		foreach ( self::FONTS as $index => $fonts ) {
			foreach ( $fonts as $font_name => $font_weights ) {
				$fonts_string[] = sprintf( '%1$s:%2$s', $font_name, implode( ',', $font_weights ) );
			}
		}

		// Fonts take too long to load. So we are only fetching on first page load.
		$font_styles = $initial_load ? file_get_contents( // @codingStandardsIgnoreLine
			esc_url_raw(
				add_query_arg(
					[
						'family' => rawurlencode( implode( '|', $fonts_string ) ),
						'subset' => rawurlencode( 'latin,latin-ext' ),
					],
					'https://fonts.googleapis.com/css'
				)
			)
		) : false;

		wp_send_json_success(
			[
				'stylesheet' => str_replace(
					'../../../dist/images/',
					'/wp-content/themes/go/dist/images/',
					str_replace( ':root', '.' . self::EDITOR_WRAPPER_CLASS, $stylesheet )
				),
				'fontStyles' => $font_styles,
			]
		);

		exit;

	}

}
