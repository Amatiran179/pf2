<?php
/**
 * Organization schema builder.
 *
 * @package PF2\Schema\Global
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_global_organization' ) ) {
    /**
     * Generate the Organization schema object.
     *
     * @param array<string, mixed> $context Current request context.
     * @return array<string, mixed>
     */
    function pf2_schema_global_organization( $context ) {
        if ( ! pf2_schema_is_enabled( 'organization', $context ) ) {
            return array();
        }

        $site_name = isset( $context['site_name'] ) ? $context['site_name'] : get_bloginfo( 'name' );
        $site_url  = isset( $context['site_url'] ) ? $context['site_url'] : home_url( '/' );
        $phone     = (string) pf2_options_get( 'phone_wa', '' );

        $schema = array(
            '@type' => 'Organization',
            'name'  => $site_name,
            'url'   => $site_url,
        );

        if ( $phone ) {
            $schema['telephone'] = preg_replace( '/[^0-9+()\-\s\.]/', '', $phone );
        }

        if ( function_exists( 'pf2_schema_global_logo' ) ) {
            $logo = pf2_schema_global_logo( $context );
            if ( ! empty( $logo ) ) {
                $schema['logo'] = $logo;
            }
        }

        return pf2_schema_clean( $schema );
    }
}
