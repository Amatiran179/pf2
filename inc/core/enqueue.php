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

if ( ! function_exists( 'pf2_can_use_vite_dev_server' ) ) {
        /**
         * Determine whether the Vite development server should be used.
         *
         * Performs a lightweight reachability probe to avoid enqueueing
         * broken dev URLs that would result in console 404s when the dev
         * server is offline.
         *
         * @return bool
         */
        function pf2_can_use_vite_dev_server() {
                if ( ! pf2_is_vite_dev() ) {
                        return false;
                }

                $host = pf2_get_vite_host();

                /**
                 * Allow integrations to short-circuit the dev server probe.
                 *
                 * @param bool|null $enabled Whether dev server should be forced on/off.
                 * @param string    $host    Dev server host URL.
                 */
                $pre = apply_filters( 'pf2_vite_dev_precheck', null, $host );

                if ( null !== $pre ) {
                        return (bool) $pre;
                }

                static $reachable = array();

                if ( array_key_exists( $host, $reachable ) ) {
                        return (bool) apply_filters( 'pf2_vite_dev_enabled', $reachable[ $host ], $host );
                }

                if ( ! function_exists( 'wp_remote_head' ) ) {
                        $reachable[ $host ] = false;

                        return (bool) apply_filters( 'pf2_vite_dev_enabled', false, $host );
                }

                $timeout = (float) apply_filters( 'pf2_vite_dev_probe_timeout', 0.75, $host );
                $timeout = $timeout > 0 ? $timeout : 0.5;

                $response = wp_remote_head(
                        trailingslashit( $host ) . '@vite/client',
                        array(
                                'timeout'     => $timeout,
                                'redirection' => 1,
                        )
                );

                if ( is_wp_error( $response ) ) {
                        $reachable[ $host ] = false;
                } else {
                        $code                = (int) wp_remote_retrieve_response_code( $response );
                        $reachable[ $host ] = $code >= 200 && $code < 500;
                }

                return (bool) apply_filters( 'pf2_vite_dev_enabled', $reachable[ $host ], $host );
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

                if ( pf2_can_use_vite_dev_server() ) {
                        pf2_enqueue_vite_dev_script( 'pf2-front', 'assets/js/front.js' );
                        pf2_localize_front_rest_config();
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
                }

                if ( wp_script_is( 'pf2-front', 'enqueued' ) ) {
                        pf2_localize_front_rest_config();
                }
        }
}
add_action( 'wp_enqueue_scripts', 'pf2_enqueue_front_assets' );

if ( ! function_exists( 'pf2_localize_front_rest_config' ) ) {
        /**
         * Localize REST configuration for front-end scripts.
         *
         * @return void
         */
        function pf2_localize_front_rest_config() {
                if ( ! wp_script_is( 'pf2-front', 'enqueued' ) && ! wp_script_is( 'pf2-front', 'registered' ) ) {
                        return;
                }

                $data = array(
                        'restUrl' => get_rest_url(),
                        'nonce'   => wp_create_nonce( 'wp_rest' ),
                        'postId'  => get_queried_object_id(),
                );

                /**
                 * Filter the localized REST configuration exposed to front scripts.
                 *
                 * @param array $data Rest configuration array.
                 */
                $data = apply_filters( 'pf2_front_rest_config', $data );

                if ( ! is_array( $data ) ) {
                        return;
                }

                $data = wp_parse_args(
                        $data,
                        array(
                                'restUrl' => '',
                                'nonce'   => '',
                                'postId'  => 0,
                        )
                );

                $data['restUrl'] = esc_url_raw( (string) $data['restUrl'] );
                $data['nonce']   = is_scalar( $data['nonce'] ) ? sanitize_text_field( (string) $data['nonce'] ) : '';
                $data['postId']  = absint( $data['postId'] );

                wp_localize_script( 'pf2-front', 'pf2Rest', $data );
        }
}

if ( ! function_exists( 'pf2_enqueue_admin_assets' ) ) {
	/**
	 * Enqueue WordPress admin assets.
	 *
	 * @param string $hook_suffix Current admin screen identifier.
	 * @return void
	 */
	function pf2_enqueue_admin_assets( $hook_suffix = '' ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$version = wp_get_theme()->get( 'Version' );

                if ( pf2_can_use_vite_dev_server() ) {
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
		}
	}
}
add_action( 'admin_enqueue_scripts', 'pf2_enqueue_admin_assets' );
