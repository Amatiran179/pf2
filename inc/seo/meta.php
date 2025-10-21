<?php
/**
 * Core meta tag generator.
 *
 * Outputs SEO-friendly title, description, and canonical link tags when native plugins are absent.
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

                remove_action( 'wp_head', '_wp_render_title_tag', 1 );

                $title      = pf2_seo_title();
                $desc       = pf2_seo_description();
                $canonical  = pf2_seo_canonical();

                if ( '' !== $title ) {
                        echo '<title>' . esc_html( $title ) . "</title>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }

                if ( '' !== $desc ) {
                        echo '<meta name="description" content="' . pf2_seo_escape( $desc ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }

                if ( '' !== $canonical ) {
                        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
        },
        3
);

if ( ! function_exists( 'pf2_seo_title' ) ) {
        /**
         * Generate the document title.
         *
         * @return string
         */
        function pf2_seo_title() {
                $site_name = pf2_seo_site_name();
                $title     = $site_name;

                if ( is_front_page() ) {
                        $tagline = pf2_seo_site_tagline();

                        if ( '' !== $tagline ) {
                                $title = $site_name . ' – ' . $tagline;
                        }
                } elseif ( is_home() ) {
                        $posts_page = (int) get_option( 'page_for_posts' );

                        if ( $posts_page > 0 ) {
                                $page_title = get_the_title( $posts_page );
                                if ( $page_title ) {
                                        $title = $page_title . ' | ' . $site_name;
                                }
                        } elseif ( '' !== $site_name ) {
                                $title = $site_name;
                        }
                } elseif ( is_singular() ) {
                        $single_title = single_post_title( '', false );

                        if ( '' !== $single_title ) {
                                $title = $single_title . ' | ' . $site_name;
                        }
                } else {
                        $document_title = wp_get_document_title();

                        if ( $document_title ) {
                                $title = $document_title;

                                if ( false === stripos( $document_title, $site_name ) && '' !== $site_name ) {
                                        $title = $document_title . ' | ' . $site_name;
                                }
                        }
                }

                if ( '' === $title ) {
                        $title = $site_name;
                }

                $title = pf2_str_limit( $title, 70 );

                return apply_filters( 'pf2_seo_title', wp_strip_all_tags( $title ) );
        }
}

if ( ! function_exists( 'pf2_seo_description' ) ) {
        /**
         * Generate the meta description.
         *
         * @return string
         */
        function pf2_seo_description() {
                $description = '';

                if ( is_singular() ) {
                        $post_id = get_queried_object_id();

                        if ( $post_id ) {
                                $description = get_post_meta( $post_id, '_pf2_meta_desc', true );

                                if ( '' === $description ) {
                                        $excerpt = get_post_field( 'post_excerpt', $post_id, 'display' );

                                        if ( '' === $excerpt ) {
                                                $content = get_post_field( 'post_content', $post_id );
                                                $description = wp_trim_words( wp_strip_all_tags( $content ), 25, '...' );
                                        } else {
                                                $description = wp_trim_words( wp_strip_all_tags( $excerpt ), 25, '...' );
                                        }
                                }
                        }
                } else {
                        $description = pf2_seo_site_tagline();
                }

                if ( '' === $description ) {
                        $description = (string) pf2_options_get( 'hero_subtitle', '' );
                }

                if ( '' === $description ) {
                        $description = (string) pf2_options_get( 'footer_note', '' );
                }

                $description = pf2_str_limit( $description, 160 );

                return apply_filters( 'pf2_seo_description', wp_strip_all_tags( $description ) );
        }
}

if ( ! function_exists( 'pf2_seo_canonical' ) ) {
        /**
         * Generate the canonical URL.
         *
         * @return string
         */
        function pf2_seo_canonical() {
                if ( is_singular() ) {
                        $url = get_permalink();
                } elseif ( is_front_page() ) {
                        $url = home_url( '/' );
                } elseif ( is_home() ) {
                        $posts_page = (int) get_option( 'page_for_posts' );
                        $url        = $posts_page ? get_permalink( $posts_page ) : home_url( '/' );
                } else {
                        $url = get_pagenum_link( 1 );
                }

                if ( is_paged() ) {
                        $paged_link = get_pagenum_link( get_query_var( 'paged' ) );

                        if ( $paged_link ) {
                                $url = $paged_link;
                        }
                }

                return apply_filters( 'pf2_seo_canonical', esc_url_raw( $url ) );
        }
}
