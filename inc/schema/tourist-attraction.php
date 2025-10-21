<?php
/**
 * Tourist attraction schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_tourist_attraction' ) ) {
        /**
         * Build a TouristAttraction schema payload.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_tourist_attraction( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id = (int) $post->ID;

                $data = array(
                        '@context'   => 'https://schema.org',
                        '@type'      => 'TouristAttraction',
                        'name'       => wp_strip_all_tags( get_the_title( $post ) ),
                        'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
                        'image'      => pf2_schema_images_from_post( $post_id ),
                        'url'        => esc_url_raw( get_permalink( $post ) ),
                        'address'    => pf2_schema_build_postal_address( $post_id ),
                        'telephone'  => pf2_schema_get_meta_text( $post_id, 'pf2_contact_phone' ),
                        'openingHoursSpecification' => array(),
                );

                $opening_hours = get_post_meta( $post_id, 'pf2_opening_hours', true );
                if ( is_array( $opening_hours ) ) {
                        $specs = array();
                        foreach ( $opening_hours as $spec ) {
                                if ( ! is_array( $spec ) ) {
                                        continue;
                                }

                                $specs[] = array(
                                        '@type'     => 'OpeningHoursSpecification',
                                        'dayOfWeek' => isset( $spec['day'] ) ? wp_strip_all_tags( $spec['day'] ) : '',
                                        'opens'     => isset( $spec['opens'] ) ? wp_strip_all_tags( $spec['opens'] ) : '',
                                        'closes'    => isset( $spec['closes'] ) ? wp_strip_all_tags( $spec['closes'] ) : '',
                                );
                        }

                        $data['openingHoursSpecification'] = $specs;
                }

                return pf2_schema_array_filter_recursive( $data );
        }
}
