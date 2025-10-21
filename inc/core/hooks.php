<?php
/**
 * Shared hook registrations.
 *
 * Provides extension points that future batches can safely augment.
 *
 * @package PF2\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
        'init',
        function () {
                require_once get_template_directory() . '/inc/cpt/loader.php';
        }
);

add_filter(
        'the_content',
        function ( $content ) {
                /*
                 * Placeholder filter for content mutations.
		 *
		 * @param string $content The post content to filter.
		 * @return string
		 */
		return $content;
        }
);

add_action(
        'after_setup_theme',
        function () {
                $base = get_template_directory() . '/inc/seo/';

                require_once $base . 'helpers.php';
                require_once $base . 'meta.php';
                require_once $base . 'opengraph.php';
        },
        5
);
