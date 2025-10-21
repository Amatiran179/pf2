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
require_once get_template_directory() . '/inc/core/setup.php';
require_once get_template_directory() . '/inc/core/enqueue.php';
require_once get_template_directory() . '/inc/core/hooks.php';
