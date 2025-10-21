<?php
/**
 * Lightweight autoloader for PF2 namespaced classes.
 *
 * Translates PSR-4 like namespaces into the theme's `inc/` directory structure.
 *
 * @package PF2\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'spl_autoload_register' ) ) {
	return;
}

/**
 * Register the PF2 namespace autoloader.
 *
 * Example:
 * ```php
 * new \PF2\Core\Example_Class();
 * ```
 * Loads: `inc/core/example_class.php`.
 *
 * @return void
 */
spl_autoload_register(
	function ( $class ) {
		$prefix = 'PF2\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative  = substr( $class, strlen( $prefix ) );
		$path      = strtolower( str_replace( '\\', '/', $relative ) );
		$target    = get_template_directory() . '/inc/' . $path . '.php';
		$real_path = apply_filters( 'pf2_autoload_path', $target, $class );

		if ( $real_path && file_exists( $real_path ) ) {
			require_once $real_path;
		}
	}
);
