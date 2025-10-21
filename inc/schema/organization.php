<?php
/**
 * Organization schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_organization' ) ) {
        /**
         * Build an Organization schema payload.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_organization( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id = (int) $post->ID;

                $logo_id  = get_theme_mod( 'custom_logo' );
                $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

                $contact_phone = pf2_schema_get_meta_text( $post_id, 'pf2_contact_phone' );
                $contact_email = get_post_meta( $post_id, 'pf2_contact_email', true );
                if ( is_string( $contact_email ) ) {
                        $contact_email = sanitize_email( $contact_email );
                } else {
                        $contact_email = '';
                }

                $contact_point = array();
                if ( $contact_phone || $contact_email ) {
                        $contact_point = array(
                                '@type'        => 'ContactPoint',
                                'telephone'    => $contact_phone,
                                'email'        => $contact_email,
                                'contactType'  => 'customer support',
                                'areaServed'   => array( 'ID' ),
                                'availableLanguage' => get_bloginfo( 'language' ),
                        );
                }

                $same_as = get_post_meta( $post_id, 'pf2_social_links', true );
                $same_as = is_array( $same_as ) ? $same_as : array();
                $same_as = array_map( 'esc_url_raw', array_filter( $same_as ) );

                $data = array(
                        '@context'     => 'https://schema.org',
                        '@type'        => 'Organization',
                        'name'         => get_bloginfo( 'name' ),
                        'url'          => esc_url_raw( home_url( '/' ) ),
                        'description'  => wp_strip_all_tags( get_bloginfo( 'description' ) ),
                        'logo'         => $logo_url ? array(
                                '@type' => 'ImageObject',
                                'url'   => esc_url_raw( $logo_url ),
                        ) : array(),
                        'contactPoint' => $contact_point ? array( $contact_point ) : array(),
                        'sameAs'       => array_values( $same_as ),
                );

                return pf2_schema_array_filter_recursive( $data );
        }
}
