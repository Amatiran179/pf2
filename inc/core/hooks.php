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
		// Reserved for custom post type registration in Batch 3.
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
