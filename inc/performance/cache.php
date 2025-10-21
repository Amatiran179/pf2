<?php
/**
 * Transient cache helpers for PF2.
 *
 * Provides a lightweight wrapper for namespaced transient caching with
 * filterable bypasses to ensure compatibility with external caching layers.
 *
 * @package PF2\Performance
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! defined( 'PF2_CACHE_PREFIX' ) ) {
        define( 'PF2_CACHE_PREFIX', 'pf2_' );
}

if ( ! function_exists( 'pf2_cache_is_enabled' ) ) {
        /**
         * Determine whether cache helpers are enabled.
         *
         * @return bool
         */
        function pf2_cache_is_enabled() {
                /**
                 * Filters whether PF2 cache helpers are active.
                 *
                 * Allows disabling the built-in transient layer when an external cache
                 * already manages fragment caching.
                 *
                 * @param bool $enabled Default true.
                 */
                return (bool) apply_filters( 'pf2_cache_enabled', true );
        }
}

if ( ! function_exists( 'pf2_cache_bypass' ) ) {
        /**
         * Check whether cache should be bypassed for the current request.
         *
         * Logged-in editors and preview contexts receive uncached responses.
         *
         * @return bool
         */
        function pf2_cache_bypass() {
                if ( ! pf2_cache_is_enabled() ) {
                        return true;
                }

                if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
                        return true;
                }

                if ( is_preview() || is_customize_preview() ) {
                        return true;
                }

                /**
                 * Filters whether cache should be bypassed.
                 *
                 * @param bool $bypass Default false.
                 */
                return (bool) apply_filters( 'pf2_cache_bypass', false );
        }
}

if ( ! function_exists( 'pf2_cache_key' ) ) {
        /**
         * Build a namespaced transient key.
         *
         * @param string               $slug Cache identifier slug.
         * @param array<string, mixed> $args Optional context arguments.
         *
         * @return string
         */
        function pf2_cache_key( $slug, $args = array() ) {
                $slug = sanitize_key( (string) $slug );

                if ( empty( $args ) ) {
                        $arguments = '';
                } else {
                        $arguments = wp_json_encode( $args );

                        if ( false === $arguments ) {
                                $arguments = maybe_serialize( $args );
                        }
                }

                $hash = $arguments ? md5( (string) $arguments ) : '';
                $key       = PF2_CACHE_PREFIX . $slug;

                if ( $hash ) {
                        $key .= '_' . $hash;
                }

                /**
                 * Filters the generated cache key before use.
                 *
                 * @param string               $key  Generated key.
                 * @param string               $slug Original slug.
                 * @param array<string, mixed> $args Context arguments.
                 */
                return (string) apply_filters( 'pf2_cache_key', $key, $slug, $args );
        }
}

if ( ! function_exists( 'pf2_cache_get' ) ) {
        /**
         * Retrieve a cached value when caching is enabled.
         *
         * @param string $key Cache key.
         *
         * @return mixed False when missing or bypassed.
         */
        function pf2_cache_get( $key ) {
                if ( pf2_cache_bypass() ) {
                        return false;
                }

                return get_transient( $key );
        }
}

if ( ! function_exists( 'pf2_cache_set' ) ) {
        /**
         * Store a value in cache.
         *
         * @param string $key   Cache key.
         * @param mixed  $value Value to cache.
         * @param int    $ttl   Time to live in seconds. Defaults to one hour.
         *
         * @return bool True when cached, false otherwise.
         */
        function pf2_cache_set( $key, $value, $ttl = 3600 ) {
                if ( pf2_cache_bypass() ) {
                        return false;
                }

                $ttl = max( 1, absint( $ttl ) );

                return set_transient( $key, $value, $ttl );
        }
}

if ( ! function_exists( 'pf2_cache_delete' ) ) {
        /**
         * Delete a cached value.
         *
         * @param string $key Cache key.
         *
         * @return bool
         */
        function pf2_cache_delete( $key ) {
                return (bool) delete_transient( $key );
        }
}

if ( ! function_exists( 'pf2_cache_flush_all' ) ) {
        /**
         * Flush all PF2 namespaced transients.
         *
         * @global wpdb $wpdb WordPress database abstraction.
         *
         * @return int Number of deleted entries.
         */
        function pf2_cache_flush_all() {
                global $wpdb;

                if ( ! isset( $wpdb ) ) {
                        return 0;
                }

                $deleted = 0;

                $option_like = $wpdb->esc_like( '_transient_' . PF2_CACHE_PREFIX ) . '%';
                $deleted    += (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $option_like ) );

                $timeout_like = $wpdb->esc_like( '_transient_timeout_' . PF2_CACHE_PREFIX ) . '%';
                $deleted     += (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $timeout_like ) );

                $site_transient_like = $wpdb->esc_like( '_site_transient_' . PF2_CACHE_PREFIX ) . '%';
                $deleted            += (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $site_transient_like ) );

                $site_timeout_like = $wpdb->esc_like( '_site_transient_timeout_' . PF2_CACHE_PREFIX ) . '%';
                $deleted          += (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $site_timeout_like ) );

                if ( is_multisite() ) {
                        $network_like = $wpdb->esc_like( '_site_transient_' . PF2_CACHE_PREFIX ) . '%';
                        $deleted     += (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s", $network_like ) );

                        $network_timeout_like = $wpdb->esc_like( '_site_transient_timeout_' . PF2_CACHE_PREFIX ) . '%';
                        $deleted             += (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s", $network_timeout_like ) );
                }

                return $deleted;
        }
}
