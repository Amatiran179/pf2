<?php
/**
 * Product schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_product' ) ) {
        /**
         * Build a Product schema payload.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_product( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id = (int) $post->ID;

                $price = pf2_schema_get_meta_text( $post_id, 'pf2_product_price' );
                if ( '' === $price ) {
                        $price = '1000';
                }

                $brand = pf2_schema_get_meta_text( $post_id, 'pf2_product_brand' );
                if ( '' === $brand ) {
                        $brand = get_bloginfo( 'name' );
                }

                $data = array(
                        '@context' => 'https://schema.org',
                        '@type'    => 'Product',
                        'name'     => wp_strip_all_tags( get_the_title( $post ) ),
                        'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
                        'image'    => pf2_schema_images_from_post( $post_id ),
                        'brand'    => $brand ? array(
                                '@type' => 'Brand',
                                'name'  => $brand,
                        ) : array(),
                        'sku'      => pf2_schema_get_meta_text( $post_id, 'pf2_product_sku' ),
                        'offers'   => array(
                                '@type'         => 'Offer',
                                'priceCurrency' => 'IDR',
                                'price'         => $price,
                                'availability'  => 'https://schema.org/InStock',
                                'url'           => esc_url_raw( get_permalink( $post ) ),
                                'validFrom'     => get_post_time( 'c', true, $post ),
                        ),
                );

                return pf2_schema_array_filter_recursive( $data );
        }
}
