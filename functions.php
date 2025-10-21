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

if ( is_admin() ) {
	require_once get_template_directory() . '/inc/admin/settings-ui.php';
	require_once get_template_directory() . '/inc/admin/menu.php';
}
