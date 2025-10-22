<?php
/**
 * Schema extras loader.
 *
 * @package PF2\Schema\Extras
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_template_directory() . '/inc/schema/extras/schema-faq.php';
require_once get_template_directory() . '/inc/schema/extras/schema-howto.php';
require_once get_template_directory() . '/inc/schema/extras/schema-video.php';
require_once get_template_directory() . '/inc/schema/extras/schema-servicearea.php';
require_once get_template_directory() . '/inc/schema/extras/schema-touristattraction.php';

if ( ! function_exists( 'pf2_schema_collect_extras' ) ) {
    /**
     * Append enabled schema extras to the graph.
     *
     * @param array<int, array<string, mixed>> $schemas Existing schema graph.
     * @param array<string, mixed>             $context Request context.
     * @return array<int, array<string, mixed>>
     */
    function pf2_schema_collect_extras( $schemas, $context ) {
        $post_id = isset( $context['post_id'] ) ? (int) $context['post_id'] : 0;

        if ( empty( $context['is_singular'] ) || ! $post_id ) {
            return $schemas;
        }

        $post = get_post( $post_id );

        if ( ! $post instanceof \WP_Post ) {
            return $schemas;
        }

        $maybe_add = function ( $enabled, callable $builder ) use ( $post, &$schemas ) {
            if ( ! rest_sanitize_boolean( $enabled ) ) {
                return;
            }

            $schema = call_user_func( $builder, $post );

            if ( ! empty( $schema ) ) {
                $schemas[] = $schema;
            }
        };

        $maybe_add( get_post_meta( $post_id, 'pf2_schema_faq_enabled', true ), 'pf2_schema_extra_faq' );
        $maybe_add( get_post_meta( $post_id, 'pf2_schema_howto_enabled', true ), 'pf2_schema_extra_howto' );
        $maybe_add( get_post_meta( $post_id, 'pf2_schema_video_enabled', true ), 'pf2_schema_extra_video' );
        $maybe_add( get_post_meta( $post_id, 'pf2_schema_servicearea_enabled', true ), 'pf2_schema_extra_service_area' );
        $maybe_add( get_post_meta( $post_id, 'pf2_schema_touristattraction_enabled', true ), 'pf2_schema_extra_tourist_attraction' );

        return $schemas;
    }
}

add_filter( 'pf2_schema_collect', 'pf2_schema_collect_extras', 15, 2 );
