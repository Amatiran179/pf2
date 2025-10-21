<?php
/**
 * HowTo schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_howto' ) ) {
        /**
         * Build a HowTo schema payload.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_howto( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id = (int) $post->ID;

                $steps = get_post_meta( $post_id, 'pf2_howto_steps', true );
                if ( ! is_array( $steps ) ) {
                        $steps = array();
                }

                /**
                 * Allow third-parties to populate HowTo steps.
                 *
                 * @param array    $steps HowTo steps.
                 * @param \WP_Post $post  Post context.
                 */
                $steps = apply_filters( 'pf2_schema_howto_steps', $steps, $post );

                $prepared_steps = array();
                $position       = 1;

                foreach ( $steps as $step ) {
                        if ( is_array( $step ) ) {
                                $name = isset( $step['name'] ) ? wp_strip_all_tags( $step['name'] ) : '';
                                $text = isset( $step['text'] ) ? wp_strip_all_tags( $step['text'] ) : '';
                        } else {
                                $name = '';
                                $text = '';
                        }

                        if ( ! $name && ! $text ) {
                                continue;
                        }

                        $prepared_steps[] = array(
                                '@type'    => 'HowToStep',
                                'position' => $position++,
                                'name'     => $name,
                                'text'     => $text,
                        );
                }

                if ( empty( $prepared_steps ) ) {
                        return array();
                }

                $data = array(
                        '@context'    => 'https://schema.org',
                        '@type'       => 'HowTo',
                        'name'        => wp_strip_all_tags( get_the_title( $post ) ),
                        'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
                        'step'        => $prepared_steps,
                        'image'       => pf2_schema_images_from_post( $post_id ),
                );

                return pf2_schema_array_filter_recursive( $data );
        }
}
