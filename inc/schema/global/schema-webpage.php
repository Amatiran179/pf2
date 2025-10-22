<?php
/**
 * WebPage schema builder.
 *
 * @package PF2\Schema\Global
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_global_webpage' ) ) {
    /**
     * Generate the WebPage schema object.
     *
     * @param array<string, mixed> $context Current request context.
     * @return array<string, mixed>
     */
    function pf2_schema_global_webpage( $context ) {
        if ( ! pf2_schema_is_enabled( 'webpage', $context ) ) {
            return array();
        }

        $url        = isset( $context['url'] ) ? $context['url'] : ''; // Already sanitized in context helper.
        $language   = isset( $context['lang'] ) ? $context['lang'] : get_bloginfo( 'language' );
        $site_name  = isset( $context['site_name'] ) ? $context['site_name'] : get_bloginfo( 'name' );
        $site_desc  = isset( $context['site_description'] ) ? $context['site_description'] : get_bloginfo( 'description', 'display' );
        $post_title = isset( $context['post_title'] ) ? $context['post_title'] : '';
        $excerpt    = isset( $context['post_excerpt'] ) ? $context['post_excerpt'] : '';

        $name        = $post_title ? $post_title : $site_name;
        $description = $excerpt ? $excerpt : $site_desc;

        $schema = array(
            '@type'       => 'WebPage',
            'name'        => $name,
            'description' => $description,
            'url'         => $url,
            'inLanguage'  => $language,
        );

        return pf2_schema_clean( $schema );
    }
}
