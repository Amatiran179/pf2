<?php
/**
 * Local business schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_local_business' ) ) {
        /**
         * Build a LocalBusiness schema payload.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_local_business( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id = (int) $post->ID;

                $telephone = pf2_schema_get_meta_text( $post_id, 'pf2_local_business_phone' );
                if ( '' === $telephone ) {
                        $telephone = pf2_schema_get_meta_text( $post_id, 'pf2_contact_phone' );
                }

                $price_range = pf2_schema_get_meta_text( $post_id, 'pf2_price_range' );

                $opening_hours = get_post_meta( $post_id, 'pf2_opening_hours', true );
                $hours         = array();
                if ( is_array( $opening_hours ) ) {
                        foreach ( $opening_hours as $spec ) {
                                if ( ! is_array( $spec ) ) {
                                        continue;
                                }

                                $day   = isset( $spec['day'] ) ? wp_strip_all_tags( $spec['day'] ) : '';
                                $opens = isset( $spec['opens'] ) ? wp_strip_all_tags( $spec['opens'] ) : '';
                                $close = isset( $spec['closes'] ) ? wp_strip_all_tags( $spec['closes'] ) : '';

                                if ( $day && $opens && $close ) {
                                        $hours[] = sprintf( '%s %s-%s', $day, $opens, $close );
                                }
                        }
                }

                $data = array(
                        '@context'    => 'https://schema.org',
                        '@type'       => 'LocalBusiness',
                        'name'        => wp_strip_all_tags( get_the_title( $post ) ),
                        'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
                        'image'       => pf2_schema_images_from_post( $post_id ),
                        'url'         => esc_url_raw( get_permalink( $post ) ),
                        'address'     => pf2_schema_build_postal_address( $post_id ),
                        'telephone'   => $telephone,
                        'priceRange'  => $price_range,
                        'openingHours' => $hours,
                );

                return pf2_schema_array_filter_recursive( $data );
        }
}
