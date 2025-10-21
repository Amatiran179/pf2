<?php
/**
 * FAQ schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_build_faq' ) ) {
        /**
         * Build an FAQPage schema payload.
         *
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build_faq( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $post_id = (int) $post->ID;

                $items = get_post_meta( $post_id, 'pf2_faq_items', true );
                if ( ! is_array( $items ) ) {
                        $items = array();
                }

                /**
                 * Allow third-parties to populate FAQ entries.
                 *
                 * @param array    $items FAQ entries.
                 * @param \WP_Post $post  Post context.
                 */
                $items = apply_filters( 'pf2_schema_faq_items', $items, $post );

                $questions = array();

                foreach ( $items as $item ) {
                        if ( is_array( $item ) ) {
                                $question = isset( $item['question'] ) ? wp_strip_all_tags( $item['question'] ) : '';
                                $answer   = isset( $item['answer'] ) ? wp_strip_all_tags( $item['answer'] ) : '';
                        } else {
                                $question = '';
                                $answer   = '';
                        }

                        if ( ! $question || ! $answer ) {
                                continue;
                        }

                        $questions[] = array(
                                '@type'          => 'Question',
                                'name'           => $question,
                                'acceptedAnswer' => array(
                                        '@type' => 'Answer',
                                        'text'  => $answer,
                                ),
                        );
                }

                if ( empty( $questions ) ) {
                        return array();
                }

                $data = array(
                        '@context'    => 'https://schema.org',
                        '@type'       => 'FAQPage',
                        'mainEntity'  => $questions,
                        'name'        => wp_strip_all_tags( get_the_title( $post ) ),
                        'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
                );

                return pf2_schema_array_filter_recursive( $data );
        }
}
