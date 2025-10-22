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

        if ( ! rest_sanitize_boolean( get_post_meta( $post->ID, 'pf2_schema_touristattraction_enabled', true ) ) ) {
            return array();
        }

        $name        = get_post_meta( $post->ID, 'pf2_schema_touristattraction_name', true );
        $description = get_post_meta( $post->ID, 'pf2_schema_touristattraction_description', true );
        $image_csv   = get_post_meta( $post->ID, 'pf2_schema_touristattraction_image_ids', true );
        $geo         = get_post_meta( $post->ID, 'pf2_schema_touristattraction_geo', true );

        $name        = is_string( $name ) && '' !== trim( $name ) ? $name : get_the_title( $post );
        $description = is_string( $description ) && '' !== trim( $description ) ? $description : get_the_excerpt( $post );

        $image_ids = array();
        if ( is_string( $image_csv ) && '' !== trim( $image_csv ) ) {
            $parts = explode( ',', $image_csv );
            foreach ( $parts as $part ) {
                $id = absint( trim( $part ) );
                if ( $id && ! in_array( $id, $image_ids, true ) ) {
                    $image_ids[] = $id;
                }
            }
        } elseif ( is_array( $image_csv ) ) { // Backwards compatibility for pre-migration data.
            foreach ( $image_csv as $part ) {
                $id = absint( $part );
                if ( $id && ! in_array( $id, $image_ids, true ) ) {
                    $image_ids[] = $id;
                }
            }
        }

        if ( empty( $image_ids ) ) {
            $legacy_images = get_post_meta( $post->ID, 'pf2_schema_touristattraction_images', true );
            if ( is_array( $legacy_images ) ) {
                foreach ( $legacy_images as $legacy_id ) {
                    $legacy_id = absint( $legacy_id );
                    if ( $legacy_id && ! in_array( $legacy_id, $image_ids, true ) ) {
                        $image_ids[] = $legacy_id;
                    }
                }
            }
        }

        $image_urls = array();
        foreach ( $image_ids as $image_id ) {
            $url = wp_get_attachment_image_url( $image_id, 'full' );
            if ( $url ) {
                $image_urls[] = esc_url_raw( $url );
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
