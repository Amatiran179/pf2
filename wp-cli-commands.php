<?php
/**
 * Custom WP-CLI commands for PF2 theme.
 *
 * Provides operational tooling for cache maintenance. Commands are registered
 * when WP_CLI is defined to avoid impacting web requests.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! class_exists( 'WP_CLI' ) || ! function_exists( 'pf2_cache_flush_all' ) ) {
        return;
}

WP_CLI::add_command(
        'pf2:flush-cache',
        /**
         * Flush the PF2 transient cache namespace.
         *
         * @return void
         */
        function () {
                $deleted = pf2_cache_flush_all();

                WP_CLI::success( sprintf( 'Flushed %d PF2 cache entr%s.', (int) $deleted, 1 === (int) $deleted ? 'y' : 'ies' ) );
        }
);
