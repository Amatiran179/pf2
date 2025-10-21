<?php
/**
 * Asset enqueue routines.
 *
 * Provides Vite-aware loading for both development (HMR) and production bundles.
 *
 * @package PF2\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pf2_is_vite_dev' ) ) {
	/**
	 * Check whether the theme should load assets from the Vite dev server.
	 *
	 * @return bool
	 */
	function pf2_is_vite_dev() {
		return defined( 'PF2_VITE_DEV' ) && PF2_VITE_DEV;
	}
}

if ( ! function_exists( 'pf2_get_vite_host' ) ) {
	/**
	 * Retrieve the configured Vite development host.
	 *
	 * @return string
	 */
	function pf2_get_vite_host() {
		$host = defined( 'PF2_VITE_DEV_HOST' ) ? PF2_VITE_DEV_HOST : 'http://localhost:5173';
		return untrailingslashit( $host );
	}
}

if ( ! function_exists( 'pf2_get_asset_path' ) ) {
	/**
	 * Resolve an asset path relative to the theme directory.
	 *
	 * @param string $relative Relative path from the theme root.
	 * @return string
	 */
	function pf2_get_asset_path( $relative ) {
		return trailingslashit( get_template_directory() ) . ltrim( $relative, '/' );
	}
}

if ( ! function_exists( 'pf2_get_asset_uri' ) ) {
	/**
	 * Resolve an asset URI relative to the theme directory.
	 *
	 * @param string $relative Relative path from the theme root.
	 * @return string
	 */
	function pf2_get_asset_uri( $relative ) {
		return trailingslashit( get_template_directory_uri() ) . ltrim( $relative, '/' );
	}
}

if ( ! function_exists( 'pf2_enqueue_vite_dev_script' ) ) {
	/**
	 * Enqueue a script entry from the Vite development server.
	 *
	 * @param string $handle Script handle.
	 * @param string $entry  Entry point relative to the dev server root.
	 * @return void
	 */
	function pf2_enqueue_vite_dev_script( $handle, $entry ) {
		$version = wp_get_theme()->get( 'Version' );
		$host    = pf2_get_vite_host();
		$entry   = ltrim( $entry, '/' );

		if ( ! wp_script_is( 'pf2-vite-client', 'enqueued' ) ) {
			wp_enqueue_script( 'pf2-vite-client', $host . '/@vite/client', array(), $version, true );
			wp_script_add_data( 'pf2-vite-client', 'type', 'module' );
		}

		wp_enqueue_script( $handle, $host . '/' . $entry, array(), $version, true );
		wp_script_add_data( $handle, 'type', 'module' );
	}
}

if ( ! function_exists( 'pf2_enqueue_front_assets' ) ) {
	/**
	 * Enqueue public-facing assets.
	 *
	 * @return void
	 */
	function pf2_enqueue_front_assets() {
		$version = wp_get_theme()->get( 'Version' );

		if ( pf2_is_vite_dev() ) {
			pf2_enqueue_vite_dev_script( 'pf2-front', 'assets/js/front.js' );
			return;
		}

		$style_path  = 'assets/css/front-bundle.css';
		$script_path = 'assets/js/front-bundle.js';

		if ( ! file_exists( pf2_get_asset_path( $style_path ) ) ) {
			$style_path = 'assets/css/front.css';
		}

		if ( ! file_exists( pf2_get_asset_path( $script_path ) ) ) {
			$script_path = 'assets/js/front.js';
		}

		if ( file_exists( pf2_get_asset_path( $style_path ) ) ) {
			wp_enqueue_style( 'pf2-front', pf2_get_asset_uri( $style_path ), array(), $version );
		}

               if ( file_exists( pf2_get_asset_path( $script_path ) ) ) {
                       wp_enqueue_script( 'pf2-front', pf2_get_asset_uri( $script_path ), array(), $version, true );
                       wp_script_add_data( 'pf2-front', 'type', 'module' );
               }
	}
}
add_action( 'wp_enqueue_scripts', 'pf2_enqueue_front_assets' );

if ( ! function_exists( 'pf2_enqueue_admin_assets' ) ) {
	/**
	 * Enqueue WordPress admin assets.
	 *
	 * @param string $hook_suffix Current admin screen identifier.
	 * @return void
	 */
	function pf2_enqueue_admin_assets( $hook_suffix = '' ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$version = wp_get_theme()->get( 'Version' );

		if ( pf2_is_vite_dev() ) {
			pf2_enqueue_vite_dev_script( 'pf2-admin', 'assets/js/admin.js' );
			return;
		}

		$style_path  = 'assets/css/admin-bundle.css';
		$script_path = 'assets/js/admin-bundle.js';

		if ( ! file_exists( pf2_get_asset_path( $style_path ) ) ) {
			$style_path = 'assets/css/admin.css';
		}

		if ( ! file_exists( pf2_get_asset_path( $script_path ) ) ) {
			$script_path = 'assets/js/admin.js';
		}

		if ( file_exists( pf2_get_asset_path( $style_path ) ) ) {
			wp_enqueue_style( 'pf2-admin', pf2_get_asset_uri( $style_path ), array(), $version );
		}

               if ( file_exists( pf2_get_asset_path( $script_path ) ) ) {
                       wp_enqueue_script( 'pf2-admin', pf2_get_asset_uri( $script_path ), array(), $version, true );
                       wp_script_add_data( 'pf2-admin', 'type', 'module' );
               }
	}
}
add_action( 'admin_enqueue_scripts', 'pf2_enqueue_admin_assets' );
