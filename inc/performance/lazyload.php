<?php
/**
 * Lazy loading enhancements for PF2 media.
 *
 * Applies defensive lazy loading attributes to media elements with filters to
 * opt-out when third-party optimisers perform similar logic.
 *
 * @package PF2\Performance
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_lazyload_is_enabled' ) ) {
        /**
         * Determine whether lazy loading is active.
         *
         * @return bool
         */
        function pf2_lazyload_is_enabled() {
                return (bool) apply_filters( 'pf2_lazyload_enabled', true );
        }
}

if ( ! function_exists( 'pf2_lazyload_should_skip_tag' ) ) {
        /**
         * Decide if a tag should be skipped.
         *
         * @param string $tag     Raw HTML tag.
         * @param string $context Filter context identifier.
         *
         * @return bool
         */
        function pf2_lazyload_should_skip_tag( $tag, $context ) {
                if ( ! $tag ) {
                        return true;
                }

                if ( false !== stripos( $tag, 'no-lazy' ) ) {
                        return true;
                }

                if ( false !== stripos( $tag, 'loading=' ) ) {
                        return true;
                }

                /**
                 * Filters whether a given media tag should be excluded from lazy loading.
                 *
                 * @param bool   $skip    Default false.
                 * @param string $tag     HTML tag string.
                 * @param string $context Context identifier.
                 */
                return (bool) apply_filters( 'pf2_lazyload_skip_tag', false, $tag, $context );
        }
}

if ( ! function_exists( 'pf2_lazyload_inject_attributes' ) ) {
        /**
         * Inject lazy loading attributes into an HTML tag.
         *
         * @param string $tag     Tag HTML.
         * @param string $context Context identifier for filters.
         *
         * @return string
         */
        function pf2_lazyload_inject_attributes( $tag, $context ) {
                if ( ! pf2_lazyload_is_enabled() || pf2_lazyload_should_skip_tag( $tag, $context ) ) {
                        return $tag;
                }

                $replacement = '<img';

                if ( false === stripos( $tag, 'loading=' ) ) {
                        $replacement .= ' loading="lazy"';
                }

                if ( false === stripos( $tag, 'decoding=' ) ) {
                        $replacement .= ' decoding="async"';
                }

                $updated = preg_replace( '/<img\b/i', $replacement, $tag, 1 );

                return is_string( $updated ) ? $updated : $tag;
        }
}

if ( ! function_exists( 'pf2_lazyload_inject_iframe_attributes' ) ) {
        /**
         * Inject lazy loading attributes into iframe tags.
         *
         * @param string $tag     Tag HTML.
         * @param string $context Context identifier for filters.
         *
         * @return string
         */
        function pf2_lazyload_inject_iframe_attributes( $tag, $context ) {
                if ( ! pf2_lazyload_is_enabled() ) {
                        return $tag;
                }

                if ( false !== stripos( $tag, 'loading=' ) ) {
                        return $tag;
                }

                if ( (bool) apply_filters( 'pf2_lazyload_skip_iframe', false, $tag, $context ) ) {
                        return $tag;
                }

                $updated = preg_replace( '/<iframe\b/i', '<iframe loading="lazy"', $tag, 1 );

                return is_string( $updated ) ? $updated : $tag;
        }
}

if ( ! function_exists( 'pf2_lazyload_images_in_content' ) ) {
        /**
         * Filter post content to inject lazy loading attributes.
         *
         * @param string $content Post content.
         *
         * @return string
         */
        function pf2_lazyload_images_in_content( $content ) {
                if ( ! pf2_lazyload_is_enabled() ) {
                        return $content;
                }

                $has_images = false !== stripos( $content, '<img' );
                $has_iframe = false !== stripos( $content, '<iframe' );

                if ( ! $has_images && ! $has_iframe ) {
                        return $content;
                }

                if ( $has_images ) {
                        $content = preg_replace_callback(
                                '/<img[^>]*>/i',
                                function ( $matches ) {
                                        return pf2_lazyload_inject_attributes( $matches[0], 'the_content' );
                                },
                                $content
                        );
                }

                if ( $has_iframe ) {
                        $content = preg_replace_callback(
                                '/<iframe[^>]*>/i',
                                function ( $matches ) {
                                        return pf2_lazyload_inject_iframe_attributes( $matches[0], 'the_content' );
                                },
                                $content
                        );
                }

                return $content;
        }
}

add_filter( 'the_content', 'pf2_lazyload_images_in_content', 20 );

if ( ! function_exists( 'pf2_lazyload_thumbnail' ) ) {
        /**
         * Filter the post thumbnail HTML.
         *
         * @param string       $html              Image HTML.
         * @param int          $post_id           Post ID.
         * @param int          $post_thumbnail_id Thumbnail ID.
         * @param string|int[] $size              Image size.
         * @param string[]     $attr              Attributes.
         *
         * @return string
         */
        function pf2_lazyload_thumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
                unset( $post_id, $post_thumbnail_id, $size, $attr );

                return pf2_lazyload_inject_attributes( $html, 'post_thumbnail' );
        }
}

add_filter( 'post_thumbnail_html', 'pf2_lazyload_thumbnail', 20, 5 );

if ( ! function_exists( 'pf2_lazyload_img_attrs' ) ) {
        /**
         * Filter attachment image attributes.
         *
         * @param array<string, string> $attr       Attributes.
         * @param WP_Post               $attachment Attachment object.
         * @param string|int[]          $size       Image size.
         *
         * @return array<string, string>
         */
        function pf2_lazyload_img_attrs( $attr, $attachment, $size ) {
                unset( $attachment, $size );

                if ( ! pf2_lazyload_is_enabled() ) {
                        return $attr;
                }

                if ( ! empty( $attr['loading'] ) ) {
                        return $attr;
                }

                if ( (bool) apply_filters( 'pf2_lazyload_skip_tag', false, '<img>', 'attributes' ) ) {
                        return $attr;
                }

                $attr['loading']  = 'lazy';
                $attr['decoding'] = empty( $attr['decoding'] ) ? 'async' : $attr['decoding'];

                return $attr;
        }
}

add_filter( 'wp_get_attachment_image_attributes', 'pf2_lazyload_img_attrs', 20, 3 );
