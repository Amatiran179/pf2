<?php
/**
 * FAQ schema extras builder.
 *
 * @package PF2\Schema\Extras
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_extra_faq' ) ) {
    /**
     * Build FAQ schema from post meta.
     *
     * @param \WP_Post $post Post instance.
     * @return array<string, mixed>
     */
    function pf2_schema_extra_faq( $post ) {
        if ( ! $post instanceof \WP_Post ) {
            return array();
        }

        if ( ! rest_sanitize_boolean( get_post_meta( $post->ID, 'pf2_schema_faq_enabled', true ) ) ) {
            return array();
        }

        $items = get_post_meta( $post->ID, 'pf2_schema_faq_items', true );

        if ( ! is_array( $items ) || empty( $items ) ) {
            return array();
        }

        $entities = array();

        foreach ( $items as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }

            $question = isset( $item['question'] ) ? trim( (string) $item['question'] ) : '';
            $answer   = isset( $item['answer'] ) ? trim( (string) $item['answer'] ) : '';

            if ( '' === $question || '' === $answer ) {
                continue;
            }

            $entities[] = array(
                '@type'          => 'Question',
                'name'           => wp_strip_all_tags( $question ),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( $answer ),
                ),
            );
        }

        if ( empty( $entities ) ) {
            return array();
        }

        $schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $entities,
        );

        return pf2_schema_clean( $schema );
    }
}
