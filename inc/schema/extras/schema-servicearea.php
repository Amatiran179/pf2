<?php
/**
 * Service area schema extras builder.
 *
 * @package PF2\Schema\Extras
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_extra_service_area' ) ) {
    /**
     * Build a Service schema representing the configured service area.
     *
     * @param \WP_Post $post Post instance.
     * @return array<string, mixed>
     */
    function pf2_schema_extra_service_area( $post ) {
        if ( ! $post instanceof \WP_Post ) {
            return array();
        }

        $type   = get_post_meta( $post->ID, 'pf2_schema_servicearea_type', true );
        $values = get_post_meta( $post->ID, 'pf2_schema_servicearea_values', true );
        $postal = get_post_meta( $post->ID, 'pf2_schema_servicearea_postal', true );
        $geo    = get_post_meta( $post->ID, 'pf2_schema_servicearea_geo', true );

        $type = is_string( $type ) ? trim( $type ) : '';

        if ( '' === $type ) {
            return array();
        }

        $area_served = array();

        if ( in_array( $type, array( 'City', 'Country', 'Region' ), true ) ) {
            $list = array();
            if ( is_array( $values ) ) {
                $list = $values;
            }

            foreach ( $list as $entry ) {
                if ( ! is_string( $entry ) ) {
                    continue;
                }

                $label = trim( $entry );
                if ( '' === $label ) {
                    continue;
                }

                $area_served[] = wp_strip_all_tags( $label );
            }
        } elseif ( 'PostalAddress' === $type && is_array( $postal ) ) {
            $address = array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => isset( $postal['streetAddress'] ) ? wp_strip_all_tags( (string) $postal['streetAddress'] ) : '',
                'addressLocality' => isset( $postal['addressLocality'] ) ? wp_strip_all_tags( (string) $postal['addressLocality'] ) : '',
                'addressRegion'   => isset( $postal['addressRegion'] ) ? wp_strip_all_tags( (string) $postal['addressRegion'] ) : '',
                'postalCode'      => isset( $postal['postalCode'] ) ? wp_strip_all_tags( (string) $postal['postalCode'] ) : '',
                'addressCountry'  => isset( $postal['addressCountry'] ) ? wp_strip_all_tags( (string) $postal['addressCountry'] ) : '',
            );

            $address = pf2_schema_clean( $address );
            if ( ! empty( $address ) ) {
                $area_served[] = $address;
            }
        } elseif ( 'GeoShape' === $type && is_array( $geo ) ) {
            $shape = array(
                '@type'  => 'GeoShape',
                'circle' => isset( $geo['circle'] ) ? sanitize_text_field( (string) $geo['circle'] ) : '',
                'polygon' => isset( $geo['polygon'] ) ? sanitize_textarea_field( (string) $geo['polygon'] ) : '',
            );

            $shape = pf2_schema_clean( $shape );
            if ( ! empty( $shape ) ) {
                $area_served[] = $shape;
            }
        }

        if ( empty( $area_served ) ) {
            return array();
        }

        $schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Service',
            'name'        => wp_strip_all_tags( get_the_title( $post ) ),
            'serviceType' => wp_strip_all_tags( get_the_title( $post ) ),
            'areaServed'  => $area_served,
        );

        $url = get_permalink( $post );
        if ( $url ) {
            $schema['url'] = esc_url_raw( $url );
        }

        return pf2_schema_clean( $schema );
    }
}
