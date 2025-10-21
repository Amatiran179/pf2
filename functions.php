<?php
/**
 * Theme bootstrap entry point.
 *
 * Wires core modules that are required for every request lifecycle.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/core/autoload.php';
require_once get_template_directory() . '/inc/core/options.php';
require_once get_template_directory() . '/inc/core/setup.php';
require_once get_template_directory() . '/inc/core/enqueue.php';
require_once get_template_directory() . '/inc/core/hooks.php';
require_once get_template_directory() . '/inc/helpers/cta.php';
require_once get_template_directory() . '/inc/helpers/gallery.php';
require_once get_template_directory() . '/inc/schema/core.php';
require_once get_template_directory() . '/inc/rest/index.php';
require_once get_template_directory() . '/inc/performance/cache.php';
require_once get_template_directory() . '/inc/performance/critical-css.php';
require_once get_template_directory() . '/inc/performance/lazyload.php';

if ( is_admin() ) {
        require_once get_template_directory() . '/inc/admin/settings-ui.php';
        require_once get_template_directory() . '/inc/admin/menu.php';
        require_once get_template_directory() . '/inc/admin/dashboard.php';
        require_once get_template_directory() . '/inc/admin/exporter.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
        require_once get_template_directory() . '/wp-cli-commands.php';
}

$pf2_std = get_template_directory() . '/inc/core/theme-standard.php';
if ( file_exists( $pf2_std ) ) {
        require_once $pf2_std;
}
