<?php
/**
 * Service area schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_service_area' ) ) {
        /**
         * Build a Service schema payload representing a service area.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_service_area( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id      = (int) $post->ID;
                $service_type = pf2_schema_get_meta_text( $post_id, 'pf2_service_type' );
                if ( '' === $service_type ) {
                        $service_type = wp_strip_all_tags( get_the_title( $post ) );
                }

                $areas_raw = get_post_meta( $post_id, 'pf2_service_area', true );
                $areas     = array();

                if ( is_array( $areas_raw ) ) {
                        $areas = array_map( 'wp_strip_all_tags', $areas_raw );
                } elseif ( is_string( $areas_raw ) ) {
                        $parts = array_map( 'trim', explode( ',', $areas_raw ) );
                        foreach ( $parts as $part ) {
                                if ( '' !== $part ) {
                                        $areas[] = wp_strip_all_tags( $part );
                                }
                        }
                }

                if ( empty( $areas ) ) {
                        $areas = array( wp_strip_all_tags( get_the_title( $post ) ) );
                }

                $data = array(
                        '@context'    => 'https://schema.org',
                        '@type'       => 'Service',
                        'serviceType' => $service_type,
                        'name'        => wp_strip_all_tags( get_the_title( $post ) ),
                        'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
                        'areaServed'  => array_values( array_filter( array_unique( $areas ) ) ),
                        'provider'    => array(
                                '@type' => 'Organization',
                                'name'  => get_bloginfo( 'name' ),
                                'url'   => esc_url_raw( home_url( '/' ) ),
                        ),
                );

                return pf2_schema_array_filter_recursive( $data );
        }
}
