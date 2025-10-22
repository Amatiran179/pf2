<?php
/**
 * Video schema extras builder.
 *
 * @package PF2\Schema\Extras
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_extra_video' ) ) {
    /**
     * Build VideoObject schema from post meta.
     *
     * @param \WP_Post $post Post instance.
     * @return array<string, mixed>
     */
    function pf2_schema_extra_video( $post ) {
        if ( ! $post instanceof \WP_Post ) {
            return array();
        }

        $url = get_post_meta( $post->ID, 'pf2_schema_video_url', true );

        if ( ! is_string( $url ) || '' === trim( $url ) ) {
            return array();
        }

        $name        = get_post_meta( $post->ID, 'pf2_schema_video_name', true );
        $description = get_post_meta( $post->ID, 'pf2_schema_video_description', true );
        $thumbnail   = get_post_meta( $post->ID, 'pf2_schema_video_thumbnail', true );
        $upload_date = get_post_meta( $post->ID, 'pf2_schema_video_upload_date', true );

        $schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'VideoObject',
            'url'         => esc_url_raw( $url ),
            'name'        => wp_strip_all_tags( (string) $name ),
            'description' => wp_strip_all_tags( (string) $description ),
        );

        if ( is_string( $thumbnail ) && '' !== trim( $thumbnail ) ) {
            $schema['thumbnailUrl'] = esc_url_raw( $thumbnail );
        }

        if ( is_string( $upload_date ) && '' !== trim( $upload_date ) ) {
            $schema['uploadDate'] = sanitize_text_field( $upload_date );
        }

        return pf2_schema_clean( $schema );
    }
}
