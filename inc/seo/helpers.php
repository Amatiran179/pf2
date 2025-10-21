<?php
/**
 * SEO helper utilities.
 *
 * Provides sanitization helpers, safe string truncation, and plugin detection utilities.
 *
 * @package PF2\SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_seo_disabled' ) ) {
        /**
         * Determine if the internal SEO module should be disabled.
         *
         * Detects popular SEO plugins and respects the pf2_seo_disabled filter for extensibility.
         *
         * @return bool
         */
        function pf2_seo_disabled() {
                $is_disabled = defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' );

                /**
                 * Filter the SEO disable flag.
                 *
                 * @param bool $is_disabled Whether the SEO stack should be disabled.
                 */
                return (bool) apply_filters( 'pf2_seo_disabled', $is_disabled );
        }
}

if ( ! function_exists( 'pf2_seo_escape' ) ) {
        /**
         * Escape a string for safe meta output.
         *
         * @param string $string Raw string.
         * @return string
         */
        function pf2_seo_escape( $string ) {
                if ( ! is_scalar( $string ) ) {
                        return '';
                }

                return esc_attr( wp_strip_all_tags( (string) $string ) );
        }
}

if ( ! function_exists( 'pf2_str_limit' ) ) {
        /**
         * Truncate a string to a maximum length with ellipsis.
         *
         * Ensures multibyte support where available while keeping the output safe for meta contexts.
         *
         * @param string $text  Input string.
         * @param int    $limit Maximum length.
         * @return string
         */
        function pf2_str_limit( $text, $limit = 150 ) {
                $limit = (int) $limit;

                if ( $limit <= 0 ) {
                        return '';
                }

                $clean = trim( wp_strip_all_tags( (string) $text ) );

                if ( '' === $clean ) {
                        return '';
                }

                $length = function_exists( 'mb_strlen' ) ? mb_strlen( $clean, 'UTF-8' ) : strlen( $clean );

                if ( $length <= $limit ) {
                        return $clean;
                }

                $slice = function_exists( 'mb_substr' ) ? mb_substr( $clean, 0, $limit, 'UTF-8' ) : substr( $clean, 0, $limit );

                // Avoid cutting mid-word when possible.
                $last_space = strrpos( $slice, ' ' );

                if ( false !== $last_space && $last_space > (int) floor( $limit * 0.6 ) ) {
                        $slice = substr( $slice, 0, $last_space );
                }

                return rtrim( $slice ) . '...';
        }
}

if ( ! function_exists( 'pf2_seo_site_name' ) ) {
        /**
         * Retrieve the site name with pf2 option fallback.
         *
         * @return string
         */
        function pf2_seo_site_name() {
                $site_name = get_bloginfo( 'name' );

                if ( empty( $site_name ) ) {
                        $site_name = (string) pf2_options_get( 'hero_title', '' );
                }

                if ( '' === $site_name ) {
                        $site_name = get_option( 'blogname', '' );
                }

                return wp_strip_all_tags( $site_name );
        }
}

if ( ! function_exists( 'pf2_seo_site_tagline' ) ) {
        /**
         * Retrieve the site tagline with pf2 option fallback.
         *
         * @return string
         */
        function pf2_seo_site_tagline() {
                $tagline = get_bloginfo( 'description' );

                if ( empty( $tagline ) ) {
                        $tagline = (string) pf2_options_get( 'footer_note', '' );
                }

                if ( '' === $tagline ) {
                        $tagline = get_option( 'blogdescription', '' );
                }

                return wp_strip_all_tags( $tagline );
        }
}

if ( ! function_exists( 'pf2_seo_site_phone' ) ) {
        /**
         * Retrieve the public phone number configured in theme options.
         *
         * @return string
         */
        function pf2_seo_site_phone() {
                $phone = (string) pf2_options_get( 'phone_wa', '' );

                return wp_strip_all_tags( $phone );
        }
}
