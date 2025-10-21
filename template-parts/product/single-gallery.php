<?php
/**
 * Product single gallery renderer.
 *
 * @package PF2\TemplateParts
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

$images = pf2_gallery_get_images( get_the_ID() );

if ( ! empty( $images ) ) {
        pf2_gallery_render( $images );
} elseif ( has_post_thumbnail() ) {
        $fallback_alt = wp_strip_all_tags( get_the_title() );
        if ( '' === $fallback_alt ) {
                $fallback_alt = wp_strip_all_tags( __( 'Gallery image', 'pf2' ) );
        }

        echo '<figure class="pf2-gallery__fallback">';
        echo wp_kses_post(
                get_the_post_thumbnail(
                        get_the_ID(),
                        'large',
                        array(
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                                'class'    => 'pf2-gallery__fallback-image',
                                'alt'      => $fallback_alt,
                        )
                )
        );
        echo '</figure>';
} else {
        echo '<p class="pf2-gallery__empty">' . esc_html__( 'Tidak ada gambar yang dapat ditampilkan saat ini.', 'pf2' ) . '</p>';
}

