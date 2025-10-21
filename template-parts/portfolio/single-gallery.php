<?php
/**
 * Portfolio single gallery renderer.
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
        echo '<figure class="pf2-gallery__fallback">';
        echo wp_kses_post(
                get_the_post_thumbnail(
                        get_the_ID(),
                        'large',
                        array(
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                                'class'    => 'pf2-gallery__fallback-image',
                        )
                )
        );
        echo '</figure>';
} else {
        echo '<p class="pf2-gallery__empty">' . esc_html__( 'Tidak ada gambar yang dapat ditampilkan saat ini.', 'pf2' ) . '</p>';
}

