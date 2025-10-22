<?php
/**
 * LocalBusiness schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_localbusiness' ) ) {
    /**
     * Generate LocalBusiness schema when enabled and targeted.
     *
     * @param array<string, mixed> $context Request context.
     * @return array<string, mixed>
     */
    function pf2_schema_localbusiness( $context ) {
        if ( ! pf2_schema_is_enabled( 'localbusiness', $context ) ) {
            return array();
        }

        if ( ! is_page() ) {
            return array();
        }

        $post_id = isset( $context['post_id'] ) ? (int) $context['post_id'] : 0;
        if ( ! $post_id ) {
            return array();
        }

        $enabled = (bool) pf2_options_get( 'localbusiness_enabled', 0 );
        if ( ! $enabled ) {
            return array();
        }

        $target_pages = pf2_options_get( 'localbusiness_target_pages', array() );
        if ( ! is_array( $target_pages ) || empty( $target_pages ) ) {
            return array();
        }

        $target_pages = array_map( 'absint', $target_pages );
        if ( ! in_array( $post_id, $target_pages, true ) ) {
            return array();
        }

        $type        = (string) pf2_options_get( 'localbusiness_type', 'LocalBusiness' );
        $name        = (string) pf2_options_get( 'localbusiness_name', '' );
        $description = (string) pf2_options_get( 'localbusiness_description', '' );
        $street      = (string) pf2_options_get( 'localbusiness_street', '' );
        $locality    = (string) pf2_options_get( 'localbusiness_locality', '' );
        $region      = (string) pf2_options_get( 'localbusiness_region', '' );
        $postal      = (string) pf2_options_get( 'localbusiness_postal', '' );
        $country     = strtoupper( (string) pf2_options_get( 'localbusiness_country', '' ) );
        $latitude    = (string) pf2_options_get( 'localbusiness_latitude', '' );
        $longitude   = (string) pf2_options_get( 'localbusiness_longitude', '' );
        $telephone   = (string) pf2_options_get( 'localbusiness_telephone', '' );
        $url         = (string) pf2_options_get( 'localbusiness_url', '' );
        $price_range = (string) pf2_options_get( 'localbusiness_price_range', '' );
        $area_served = (string) pf2_options_get( 'localbusiness_area_served', '' );

        $same_as   = pf2_options_get( 'localbusiness_same_as', array() );
        $hours_raw = pf2_options_get( 'localbusiness_opening_hours', array() );

        if ( ! $url && ! empty( $context['url'] ) ) {
            $url = $context['url'];
        }

        $address = array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $street,
            'addressLocality' => $locality,
            'addressRegion'   => $region,
            'postalCode'      => $postal,
            'addressCountry'  => $country,
        );

        $geo = array();
        if ( '' !== $latitude || '' !== $longitude ) {
            $geo = array(
                '@type'    => 'GeoCoordinates',
                'latitude' => $latitude,
                'longitude'=> $longitude,
            );
        }

        $opening_hours = array();
        if ( is_array( $hours_raw ) ) {
            foreach ( $hours_raw as $row ) {
                if ( empty( $row['dayOfWeek'] ) || empty( $row['opens'] ) || empty( $row['closes'] ) ) {
                    continue;
                }

                $opening_hours[] = array(
                    '@type'    => 'OpeningHoursSpecification',
                    'dayOfWeek' => 'https://schema.org/' . $row['dayOfWeek'],
                    'opens'     => $row['opens'],
                    'closes'    => $row['closes'],
                );
            }
        }

        $schema = array(
            '@type'        => $type,
            '@id'          => $url ? untrailingslashit( $url ) . '#localbusiness' : '',
            'name'         => $name,
            'description'  => $description,
            'url'          => $url,
            'telephone'    => $telephone,
            'priceRange'   => $price_range,
            'areaServed'   => $area_served,
            'address'      => $address,
            'geo'          => $geo,
            'sameAs'       => is_array( $same_as ) ? array_values( array_filter( $same_as ) ) : array(),
            'openingHoursSpecification' => $opening_hours,
        );

        $logo = function_exists( 'pf2_schema_global_logo' ) ? pf2_schema_global_logo( $context ) : array();
        if ( ! empty( $logo ) ) {
            $schema['image'] = $logo;
        }

        return pf2_schema_clean( $schema );
    }
}
