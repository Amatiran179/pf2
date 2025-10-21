<?php
/**
 * Theme setup features.
 *
 * Handles textdomain loading, theme supports, and navigation menu registration.
 *
 * @package PF2\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'pf2', get_template_directory() . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
		add_theme_support( 'responsive-embeds' );

		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary Menu', 'pf2' ),
				'footer'  => esc_html__( 'Footer Menu', 'pf2' ),
			)
		);

		// add_image_size( 'pf2-featured', 1200, 630, true ); // Reserved for future batches.
	}
);
