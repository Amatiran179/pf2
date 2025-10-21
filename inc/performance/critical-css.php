<?php
/**
 * Critical CSS delivery for PF2.
 *
 * Inline a minimal subset of CSS for the above-the-fold experience while
 * allowing integrators to disable the behaviour via filters if their caching
 * layer handles critical rendering paths differently.
 *
 * @package PF2\Performance
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_output_critical_css' ) ) {
        /**
         * Echo inline critical CSS in the document head.
         *
         * The output is intentionally unescaped because it is sourced from a file
         * within the theme directory; any injected </style> token is treated as an
         * invalid file and ignored to prevent script injection.
         *
         * @return void
         */
        function pf2_output_critical_css() {
                if ( ! apply_filters( 'pf2_critical_css_enabled', true ) ) {
                        return;
                }

                $path = (string) apply_filters( 'pf2_critical_css_path', get_template_directory() . '/assets/css/critical.css' );

                if ( ! $path || ! file_exists( $path ) || ! is_readable( $path ) ) {
                        return;
                }

                $css = trim( (string) file_get_contents( $path ) );

                if ( '' === $css ) {
                        return;
                }

                if ( false !== strpos( $css, '</style>' ) ) {
                        return;
                }

                echo '<style id="pf2-critical-css">' . $css . '</style>';
        }
}

add_action( 'wp_head', 'pf2_output_critical_css', 5 );
