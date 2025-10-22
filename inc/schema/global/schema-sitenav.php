<?php
/**
 * SiteNavigationElement schema builder.
 *
 * @package PF2\Schema\Global
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_global_sitenav' ) ) {
    /**
     * Generate the SiteNavigationElement list schema object.
     *
     * @param array<string, mixed> $context Current request context.
     * @return array<string, mixed>
     */
    function pf2_schema_global_sitenav( $context ) {
        if ( ! pf2_schema_is_enabled( 'sitenavigationelement', $context ) ) {
            return array();
        }

        $locations = get_nav_menu_locations();
        if ( empty( $locations ) || ! is_array( $locations ) ) {
            return array();
        }

        $target_locations = array( 'primary', 'footer' );
        $elements         = array();
        $position         = 1;

        foreach ( $target_locations as $location ) {
            if ( empty( $locations[ $location ] ) ) {
                continue;
            }

            $menu_items = wp_get_nav_menu_items( $locations[ $location ] );
            if ( empty( $menu_items ) ) {
                continue;
            }

            foreach ( $menu_items as $item ) {
                if ( empty( $item->title ) || empty( $item->url ) ) {
                    continue;
                }

                $elements[] = array(
                    '@type'    => 'SiteNavigationElement',
                    'position' => $position,
                    'name'     => wp_strip_all_tags( $item->title ),
                    'url'      => esc_url_raw( $item->url ),
                );

                $position++;
            }
        }

        if ( empty( $elements ) ) {
            return array();
        }

        $schema = array(
            '@type'           => 'ItemList',
            'name'            => __( 'Site Navigation', 'pf2' ),
            'itemListElement' => $elements,
        );

        return pf2_schema_clean( $schema );
    }
}
