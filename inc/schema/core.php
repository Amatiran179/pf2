<?php
/**
 * Schema orchestrator.
 *
 * Wires global schema fragments and renders a single JSON-LD tag for search
 * engines. Modules can extend the schema graph via the `pf2_schema_collect`
 * filter.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_template_directory() . '/inc/schema/registry.php';
require_once get_template_directory() . '/inc/schema/global/schema-website.php';
require_once get_template_directory() . '/inc/schema/global/schema-webpage.php';
require_once get_template_directory() . '/inc/schema/global/schema-breadcrumb.php';
require_once get_template_directory() . '/inc/schema/global/schema-sitenav.php';
require_once get_template_directory() . '/inc/schema/global/schema-organization.php';
require_once get_template_directory() . '/inc/schema/global/schema-logo.php';
require_once get_template_directory() . '/inc/schema/localbusiness.php';

if ( ! function_exists( 'pf2_schema_output' ) ) {
    /**
     * Render consolidated schema graph to the head.
     *
     * @return void
     */
    function pf2_schema_output() {
        $context = pf2_schema_context();

        if ( ! pf2_schema_is_enabled( 'output', $context ) ) {
            return;
        }

        $schemas = array(
            pf2_schema_global_website( $context ),
            pf2_schema_global_webpage( $context ),
            pf2_schema_global_breadcrumb( $context ),
            pf2_schema_global_sitenav( $context ),
            pf2_schema_global_organization( $context ),
            pf2_schema_global_logo( $context ),
            pf2_schema_localbusiness( $context ),
        );

        $schemas = pf2_schema_merge( $schemas );

        /**
         * Filter schema graph prior to cleaning and rendering.
         *
         * @param array<int, array<string, mixed>> $schemas Current schema list.
         * @param array<string, mixed>              $context Request context.
         */
        $schemas = apply_filters( 'pf2_schema_collect', $schemas, $context );
        $schemas = pf2_schema_merge( $schemas );

        $renderable = array();
        $seen       = array();

        foreach ( $schemas as $schema ) {
            if ( ! is_array( $schema ) ) {
                continue;
            }

            $schema = pf2_schema_clean( $schema );
            if ( empty( $schema ) || empty( $schema['@type'] ) ) {
                continue;
            }

            $type = strtolower( (string) $schema['@type'] );
            $url  = isset( $schema['url'] ) ? strtolower( (string) $schema['url'] ) : '';
            $key  = $type . '|' . $url;

            if ( isset( $seen[ $key ] ) ) {
                continue;
            }

            $seen[ $key ]   = true;
            $renderable[]   = $schema;
        }

        if ( empty( $renderable ) ) {
            return;
        }

        if ( empty( $renderable[0]['@context'] ) ) {
            $renderable[0]['@context'] = 'https://schema.org';
        }

        $json = wp_json_encode( $renderable, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        if ( ! $json ) {
            return;
        }

        echo '<script type="application/ld+json" id="pf2-schema">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
add_action( 'wp_head', 'pf2_schema_output', 8 );
