<?php
/**
 * WebSite schema builder.
 *
 * @package PF2\Schema\Global
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_global_website' ) ) {
    /**
     * Generate the WebSite schema object.
     *
     * @param array<string, mixed> $context Current request context.
     * @return array<string, mixed>
     */
    function pf2_schema_global_website( $context ) {
        if ( ! pf2_schema_is_enabled( 'website', $context ) ) {
            return array();
        }

        $site_url  = isset( $context['site_url'] ) ? $context['site_url'] : home_url( '/' );
        $site_name = isset( $context['site_name'] ) ? $context['site_name'] : get_bloginfo( 'name' );

        $schema = array(
            '@type'    => 'WebSite',
            '@context' => 'https://schema.org',
            'url'      => $site_url,
            'name'     => $site_name,
        );

        $search_target = home_url( '/?s={search_term_string}' );
        $schema['potentialAction'] = array(
            '@type'       => 'SearchAction',
            'target'      => esc_url_raw( $search_target ),
            'query-input' => 'required name=search_term_string',
        );

        return pf2_schema_clean( $schema );
    }
}
