<?php
/**
 * HowTo schema extras builder.
 *
 * @package PF2\Schema\Extras
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_extra_howto' ) ) {
    /**
     * Build HowTo schema from post meta.
     *
     * @param \WP_Post $post Post instance.
     * @return array<string, mixed>
     */
    function pf2_schema_extra_howto( $post ) {
        if ( ! $post instanceof \WP_Post ) {
            return array();
        }

        if ( ! rest_sanitize_boolean( get_post_meta( $post->ID, 'pf2_schema_howto_enabled', true ) ) ) {
            return array();
        }

        $steps = get_post_meta( $post->ID, 'pf2_schema_howto_steps', true );

        if ( ! is_array( $steps ) || empty( $steps ) ) {
            return array();
        }

        $name = get_post_meta( $post->ID, 'pf2_schema_howto_name', true );
        if ( ! is_string( $name ) || '' === trim( $name ) ) {
            $name = get_the_title( $post );
        }

        $prepared = array();
        $position = 1;

        foreach ( $steps as $step ) {
            if ( ! is_array( $step ) ) {
                continue;
            }

            $label = isset( $step['name'] ) ? trim( (string) $step['name'] ) : '';
            $text  = isset( $step['text'] ) ? trim( (string) $step['text'] ) : '';
            $image = 0;
            if ( isset( $step['image_id'] ) ) {
                $image = absint( $step['image_id'] );
            } elseif ( isset( $step['image'] ) ) { // Backwards compatibility.
                $image = absint( $step['image'] );
            }

            if ( '' === $label && '' === $text && ! $image ) {
                continue;
            }

            $entry = array(
                '@type'    => 'HowToStep',
                'position' => $position++,
                'name'     => wp_strip_all_tags( $label ),
                'text'     => wp_strip_all_tags( $text ),
            );

            if ( $image ) {
                $image_url = wp_get_attachment_image_url( $image, 'full' );
                if ( $image_url ) {
                    $entry['image'] = esc_url_raw( $image_url );
                }
            }

            $prepared[] = pf2_schema_clean( $entry );
        }

        if ( empty( $prepared ) ) {
            return array();
        }

        $schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'HowTo',
            'name'        => wp_strip_all_tags( $name ),
            'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
            'step'        => $prepared,
        );

        $images = pf2_schema_images_from_post( $post->ID );
        if ( ! empty( $images ) ) {
            $schema['image'] = $images;
        }

        return pf2_schema_clean( $schema );
    }
}
