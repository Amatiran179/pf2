<?php
/**
 * Admin menu registrations.
 *
 * Declares the PutraFiber top-level menu and routes to the settings renderer.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pf2_admin_menu' ) ) {
	/**
	 * Register the PutraFiber admin menu.
	 *
	 * @return void
	 */
        function pf2_admin_menu() {
                add_menu_page(
                        __( 'PutraFiber Dashboard', 'pf2' ),
                        __( 'PutraFiber', 'pf2' ),
                        'manage_options',
                        'pf2',
                        'pf2_admin_dashboard_page',
                        'dashicons-admin-generic',
                        61
                );

                add_submenu_page(
                        'pf2',
                        __( 'Dashboard', 'pf2' ),
                        __( 'Dashboard', 'pf2' ),
                        'manage_options',
                        'pf2',
                        'pf2_admin_dashboard_page'
                );

                add_submenu_page(
                        'pf2',
                        __( 'Settings', 'pf2' ),
                        __( 'Settings', 'pf2' ),
                        'manage_options',
                        'pf2-settings',
                        'pf2_admin_settings_page'
                );
        }
}
add_action( 'admin_menu', 'pf2_admin_menu' );

if ( ! function_exists( 'pf2_admin_settings_page' ) ) {
	/**
	 * Render the PutraFiber settings page.
	 *
	 * @return void
	 */
	function pf2_admin_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( function_exists( 'pf2_admin_render_settings_page' ) ) {
			pf2_admin_render_settings_page();
		}
	}
}
