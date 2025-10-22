<?php
/**
 * Tourist attraction schema extras builder.
 *
 * @package PF2\Schema\Extras
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_extra_tourist_attraction' ) ) {
    /**
     * Build TouristAttraction schema from post meta.
     *
     * @param \WP_Post $post Post instance.
     * @return array<string, mixed>
     */
    function pf2_schema_extra_tourist_attraction( $post ) {
        if ( ! $post instanceof \WP_Post ) {
            return array();
        }

        $name        = get_post_meta( $post->ID, 'pf2_schema_touristattraction_name', true );
        $description = get_post_meta( $post->ID, 'pf2_schema_touristattraction_description', true );
        $images      = get_post_meta( $post->ID, 'pf2_schema_touristattraction_images', true );
        $geo         = get_post_meta( $post->ID, 'pf2_schema_touristattraction_geo', true );

        $name        = is_string( $name ) && '' !== trim( $name ) ? $name : get_the_title( $post );
        $description = is_string( $description ) && '' !== trim( $description ) ? $description : get_the_excerpt( $post );

        $image_urls = array();
        if ( is_array( $images ) ) {
            foreach ( $images as $image_id ) {
                $image_id = absint( $image_id );
                if ( ! $image_id ) {
                    continue;
                }

                $url = wp_get_attachment_image_url( $image_id, 'full' );
                if ( $url ) {
                    $image_urls[] = esc_url_raw( $url );
                }
            }
        }

        if ( empty( $image_urls ) ) {
            $image_urls = pf2_schema_images_from_post( $post->ID );
        }

        $schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'TouristAttraction',
            'name'        => wp_strip_all_tags( $name ),
            'description' => wp_strip_all_tags( (string) $description ),
            'url'         => esc_url_raw( get_permalink( $post ) ),
        );

        if ( ! empty( $image_urls ) ) {
            $schema['image'] = array_values( array_unique( $image_urls ) );
        }

        if ( is_array( $geo ) ) {
            $latitude  = isset( $geo['latitude'] ) ? trim( (string) $geo['latitude'] ) : '';
            $longitude = isset( $geo['longitude'] ) ? trim( (string) $geo['longitude'] ) : '';

            if ( '' !== $latitude && '' !== $longitude ) {
                $schema['geo'] = array(
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => sanitize_text_field( $latitude ),
                    'longitude' => sanitize_text_field( $longitude ),
                );
            }
        }

        return pf2_schema_clean( $schema );
    }
}
