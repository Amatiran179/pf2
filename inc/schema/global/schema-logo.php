<?php
/**
 * Logo ImageObject schema builder.
 *
 * @package PF2\Schema\Global
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_global_logo' ) ) {
    /**
     * Generate the ImageObject schema for the site logo.
     *
     * @param array<string, mixed> $context Current request context.
     * @return array<string, mixed>
     */
    function pf2_schema_global_logo( $context ) {
        if ( ! pf2_schema_is_enabled( 'logo', $context ) ) {
            return array();
        }

        $logo_url = '';

        if ( ! empty( $context['logo_url'] ) ) {
            $logo_url = $context['logo_url'];
        } elseif ( ! empty( $context['option_logo'] ) ) {
            $logo_url = $context['option_logo'];
        } elseif ( ! empty( $context['site_icon'] ) ) {
            $logo_url = $context['site_icon'];
        } else {
            $fallback = get_site_icon_url( 512 );
            if ( $fallback ) {
                $logo_url = esc_url_raw( $fallback );
            }
        }

        if ( ! $logo_url ) {
            return array();
        }

        $schema = array(
            '@type' => 'ImageObject',
            'url'   => $logo_url,
            'name'  => isset( $context['site_name'] ) ? $context['site_name'] : get_bloginfo( 'name' ),
        );

        if ( ! empty( $context['logo_id'] ) ) {
            $meta = wp_get_attachment_metadata( (int) $context['logo_id'] );
            if ( is_array( $meta ) ) {
                if ( ! empty( $meta['width'] ) ) {
                    $schema['width'] = (int) $meta['width'];
                }
                if ( ! empty( $meta['height'] ) ) {
                    $schema['height'] = (int) $meta['height'];
                }
            }
        }

        return pf2_schema_clean( $schema );
    }
}
