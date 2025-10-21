<?php
/**
 * OpenGraph and Twitter Cards generator.
 *
 * Outputs social metadata derived from pf2 meta helpers with extensibility hooks.
 *
 * @package PF2\SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

add_action(
        'wp_head',
        function () {
                if ( pf2_seo_disabled() ) {
                        return;
                }

                $og_enabled      = apply_filters( 'pf2_og_enabled', true );
                $twitter_enabled = apply_filters( 'pf2_twitter_enabled', true );

                if ( ! $og_enabled && ! $twitter_enabled ) {
                        return;
                }

                $title = pf2_seo_title();
                $desc  = pf2_seo_description();
                $image = pf2_seo_image();
                $url   = pf2_seo_canonical();

                if ( $og_enabled ) {
                        if ( '' !== $title ) {
                                echo '<meta property="og:title" content="' . pf2_seo_escape( $title ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }

                        if ( '' !== $desc ) {
                                echo '<meta property="og:description" content="' . pf2_seo_escape( $desc ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }

                        if ( '' !== $url ) {
                                echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }

                        $type = is_singular() ? 'article' : 'website';
                        echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                        if ( '' !== $image ) {
                                echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }

                        $site_name = pf2_seo_site_name();
                        if ( '' !== $site_name ) {
                                echo '<meta property="og:site_name" content="' . pf2_seo_escape( $site_name ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                }

                if ( $twitter_enabled ) {
                        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                        if ( '' !== $title ) {
                                echo '<meta name="twitter:title" content="' . pf2_seo_escape( $title ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }

                        if ( '' !== $desc ) {
                                echo '<meta name="twitter:description" content="' . pf2_seo_escape( $desc ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }

                        if ( '' !== $image ) {
                                echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                }
        },
        4
);

if ( ! function_exists( 'pf2_seo_image' ) ) {
        /**
         * Determine the social sharing image URL.
         *
         * @return string
         */
        function pf2_seo_image() {
                $image = '';
                $post_id = get_queried_object_id();

                if ( $post_id && has_post_thumbnail( $post_id ) ) {
                        $image = get_the_post_thumbnail_url( $post_id, 'large' );
                }

                if ( ! $image ) {
                        $image = (string) pf2_options_get( 'logo_url', '' );
                }

                if ( ! $image ) {
                        $custom_logo_id = get_theme_mod( 'custom_logo' );

                        if ( $custom_logo_id ) {
                                $custom_logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
                                if ( is_array( $custom_logo ) && ! empty( $custom_logo[0] ) ) {
                                        $image = $custom_logo[0];
                                }
                        }
                }

                if ( ! $image ) {
                        $image = get_site_icon_url( 512 );
                }

                return apply_filters( 'pf2_seo_image', esc_url_raw( $image ) );
        }
}
