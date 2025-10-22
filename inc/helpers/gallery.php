<?php
/**
 * Gallery helpers for frontend rendering.
 *
 * @package PF2\Helpers
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_gallery_prepare_image_data' ) ) {
        /**
         * Prepare attachment data for gallery rendering.
         *
         * @param int $attachment_id Attachment ID.
         *
         * @return array<string, mixed>|null
         */
        function pf2_gallery_prepare_image_data( $attachment_id ) {
                $attachment_id = absint( $attachment_id );

                if ( ! $attachment_id ) {
                        return null;
                }

                $large_image  = wp_get_attachment_image_src( $attachment_id, 'large' );
                $thumb_image  = wp_get_attachment_image_src( $attachment_id, 'medium' );
                $srcset       = wp_get_attachment_image_srcset( $attachment_id, 'large' );
                $sizes        = wp_get_attachment_image_sizes( $attachment_id, 'large' );
                $alt_text_raw = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                $alt_text     = is_string( $alt_text_raw ) ? wp_strip_all_tags( $alt_text_raw ) : '';
                $fallback_alt = wp_strip_all_tags( get_the_title( $attachment_id ) );

                if ( ! $large_image ) {
                        return null;
                }

                return array(
                        'id'      => $attachment_id,
                        'url'     => $large_image[0],
                        'width'   => (int) $large_image[1],
                        'height'  => (int) $large_image[2],
                        'thumb'   => $thumb_image ? $thumb_image[0] : $large_image[0],
                        'srcset'  => $srcset,
                        'sizes'   => $sizes,
                        'alt'     => $alt_text ? $alt_text : $fallback_alt,
                );
        }
}

if ( ! function_exists( 'pf2_gallery_get_images' ) ) {
        /**
         * Retrieve gallery images for a given post.
         *
         * @param int $post_id Post ID.
         *
         * @return array<int, array<string, mixed>>
         */
        function pf2_gallery_get_images( $post_id ) {
                $post_id = absint( $post_id );

                if ( ! $post_id ) {
                        return array();
                }

                $images     = array();
                $unique_ids = array();

                $csv_meta = get_post_meta( $post_id, 'pf2_gallery_ids', true );
                $meta_ids = array();

                if ( is_string( $csv_meta ) && '' !== $csv_meta ) {
                        $parts = preg_split( '/,/', $csv_meta );
                        if ( is_array( $parts ) ) {
                                foreach ( $parts as $part ) {
                                        $id = absint( trim( (string) $part ) );
                                        if ( $id && ! isset( $unique_ids[ $id ] ) ) {
                                                $meta_ids[]          = $id;
                                                $unique_ids[ $id ] = true;
                                        }
                                }
                        }
                }

                if ( empty( $meta_ids ) ) {
                        $attachment_ids = get_posts(
                                array(
                                        'post_parent'    => $post_id,
                                        'post_type'      => 'attachment',
                                        'post_mime_type' => 'image',
                                        'orderby'        => 'menu_order ID',
                                        'order'          => 'ASC',
                                        'numberposts'    => -1,
                                        'fields'         => 'ids',
                                )
                        );

                        if ( $attachment_ids && is_array( $attachment_ids ) ) {
                                foreach ( $attachment_ids as $attachment_id ) {
                                        $attachment_id = absint( $attachment_id );
                                        if ( $attachment_id && ! isset( $unique_ids[ $attachment_id ] ) ) {
                                                $meta_ids[]                 = $attachment_id;
                                                $unique_ids[ $attachment_id ] = true;
                                        }
                                }
                        }
                }

                foreach ( $meta_ids as $attachment_id ) {
                        $data = pf2_gallery_prepare_image_data( $attachment_id );

                        if ( $data ) {
                                $images[] = $data;
                        }
                }

                if ( empty( $images ) ) {
                        $featured_id = get_post_thumbnail_id( $post_id );

                        if ( $featured_id ) {
                                $data = pf2_gallery_prepare_image_data( $featured_id );
                                if ( $data ) {
                                        $images[] = $data;
                                }
                        }
                }

                return $images;
        }
}

if ( ! function_exists( 'pf2_gallery_normalize_classes' ) ) {
        /**
         * Convert class string/array into sanitized string.
         *
         * @param string|array<int, string> $classes Class list.
         */
        function pf2_gallery_normalize_classes( $classes ) {
                $classes = is_array( $classes ) ? $classes : preg_split( '/\s+/', (string) $classes, -1, PREG_SPLIT_NO_EMPTY );

                if ( ! $classes ) {
                        return '';
                }

                $sanitized = array();

                foreach ( $classes as $class ) {
                        $class = sanitize_html_class( $class );
                        if ( '' !== $class ) {
                                $sanitized[] = $class;
                        }
                }

                return implode( ' ', $sanitized );
        }
}

if ( ! function_exists( 'pf2_gallery_render' ) ) {
        /**
         * Render gallery markup.
         *
         * @param array<int, array<string, mixed>> $images Image data.
         * @param array<string, mixed>              $args   Render arguments.
         */
        function pf2_gallery_render( $images, $args = array() ) {
                if ( empty( $images ) || ! is_array( $images ) ) {
                        return;
                }

                $defaults = array(
                        'container_class' => 'pf2-gallery',
                        'swiper_class'    => 'pf2-swiper swiper',
                        'lightbox_group'  => 'pf2-gallery',
                );

                $args = wp_parse_args( $args, $defaults );

                $container_class = pf2_gallery_normalize_classes( $args['container_class'] );
                $swiper_class    = pf2_gallery_normalize_classes( $args['swiper_class'] );
                $lightbox_group  = sanitize_html_class( $args['lightbox_group'] );
                $has_multiple    = count( $images ) > 1;

                $container_attr = $container_class ? ' class="' . esc_attr( $container_class ) . '"' : '';
                $swiper_attr    = $swiper_class ? ' class="' . esc_attr( $swiper_class ) . '"' : '';
                $group_attr     = $lightbox_group ? $lightbox_group : 'pf2-gallery';

                echo '<div' . $container_attr . ' data-pf2-gallery>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
                echo '<div' . $swiper_attr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
                echo '<div class="swiper-wrapper">';

                foreach ( $images as $image ) {
                        $image_url   = isset( $image['url'] ) ? $image['url'] : '';
                        $thumb_url   = isset( $image['thumb'] ) ? $image['thumb'] : $image_url;
                        $srcset_attr = isset( $image['srcset'] ) ? $image['srcset'] : '';
                        $sizes_attr  = isset( $image['sizes'] ) ? $image['sizes'] : '';
                        $alt_text    = isset( $image['alt'] ) ? $image['alt'] : '';
                        $width       = isset( $image['width'] ) ? (int) $image['width'] : 0;
                        $height      = isset( $image['height'] ) ? (int) $image['height'] : 0;

                        if ( ! $image_url ) {
                                continue;
                        }

                        echo '<div class="swiper-slide">';
                        echo '<a class="pf2-gl" href="' . esc_url( $image_url ) . '" data-lightbox="' . esc_attr( $group_attr ) . '" data-thumbnail="' . esc_url( $thumb_url ) . '">';
                        echo '<img src="' . esc_url( $image_url ) . '"';

                        if ( $width && $height ) {
                                echo ' width="' . esc_attr( (string) $width ) . '" height="' . esc_attr( (string) $height ) . '"';
                        }

                        if ( $srcset_attr ) {
                                echo ' srcset="' . esc_attr( $srcset_attr ) . '"';
                        }

                        if ( $sizes_attr ) {
                                echo ' sizes="' . esc_attr( $sizes_attr ) . '"';
                        }

                        $alt_output = $alt_text ? esc_attr( $alt_text ) : esc_attr__( 'Gallery image', 'pf2' );
                        echo ' alt="' . $alt_output . '" loading="lazy" decoding="async" />';
                        echo '</a>';
                        echo '</div>';
                }

                echo '</div>';

                if ( $has_multiple ) {
                        echo '<div class="swiper-button-prev" aria-label="' . esc_attr__( 'Previous slide', 'pf2' ) . '" tabindex="0"></div>';
                        echo '<div class="swiper-button-next" aria-label="' . esc_attr__( 'Next slide', 'pf2' ) . '" tabindex="0"></div>';
                        echo '<div class="swiper-pagination" aria-hidden="true"></div>';
                }

                echo '</div>';
                echo '</div>';
        }
}
