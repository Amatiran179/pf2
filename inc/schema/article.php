<?php
/**
 * Article schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_article' ) ) {
        /**
         * Build an Article schema payload.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_article( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id = (int) $post->ID;
                $author  = get_userdata( $post->post_author );

                $data = array(
                        '@context'         => 'https://schema.org',
                        '@type'            => 'Article',
                        'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
                        'description'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
                        'datePublished'    => get_post_time( 'c', true, $post ),
                        'dateModified'     => get_post_modified_time( 'c', true, $post ),
                        'author'           => $author ? array(
                                '@type' => 'Person',
                                'name'  => wp_strip_all_tags( $author->display_name ),
                        ) : array(),
                        'image'            => pf2_schema_images_from_post( $post_id ),
                        'mainEntityOfPage' => esc_url_raw( get_permalink( $post ) ),
                        'publisher'        => array(
                                '@type' => 'Organization',
                                'name'  => get_bloginfo( 'name' ),
                                'logo'  => array(
                                        '@type' => 'ImageObject',
                                        'url'   => esc_url_raw( get_site_icon_url( 512 ) ),
                                ),
                        ),
                );

                return pf2_schema_array_filter_recursive( $data );
        }
}
