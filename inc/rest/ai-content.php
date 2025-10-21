<?php
/**
 * AI content REST controller.
 *
 * Handles validation, rate limiting, and provider orchestration for
 * AI-assisted content generation.
 *
 * @package PF2\Rest
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

use PF2\AI\AdapterInterface;
use PF2\AI\MockAdapter;
use PF2\AI\OpenAIAdapter;
use WP_REST_Request;
use WP_REST_Response;
use Throwable;

if ( ! function_exists( 'pf2_rest_ai_generate' ) ) {
        /**
         * REST callback for AI content generation.
         *
         * @param WP_REST_Request $request REST request.
         *
         * @return WP_REST_Response
         */
        function pf2_rest_ai_generate( WP_REST_Request $request ) {
                $topic = $request->get_param( 'topic' );
                $tone  = $request->get_param( 'tone' );
                $lang  = $request->get_param( 'lang' );

                $topic = is_string( $topic ) ? sanitize_text_field( wp_unslash( $topic ) ) : '';
                $tone  = is_string( $tone ) ? sanitize_key( wp_unslash( $tone ) ) : 'neutral';
                $lang  = is_string( $lang ) ? sanitize_text_field( wp_unslash( $lang ) ) : 'id';
                $lang  = '' !== $lang ? strtolower( $lang ) : 'id';

                $allowed_tones = array( 'neutral', 'friendly', 'formal', 'salesy' );

                if ( ! in_array( $tone, $allowed_tones, true ) ) {
                        $tone = 'neutral';
                }

                if ( '' === $topic ) {
                        return new WP_REST_Response(
                                array(
                                        'ok'    => false,
                                        'error' => __( 'Topic is required for AI generation.', 'pf2' ),
                                ),
                                400
                        );
                }

                $keywords_param = $request->get_param( 'keywords' );
                $keywords       = array();

                if ( is_array( $keywords_param ) ) {
                        foreach ( $keywords_param as $keyword ) {
                                if ( ! is_scalar( $keyword ) ) {
                                        continue;
                                }

                                $keyword = sanitize_text_field( wp_unslash( (string) $keyword ) );

                                if ( '' !== $keyword ) {
                                        $keywords[] = $keyword;
                                }
                        }
                }

                $nonce = $request->get_header( 'x-wp-nonce' );
                $nonce = is_string( $nonce ) ? sanitize_text_field( $nonce ) : '';

                if ( ! current_user_can( 'edit_posts' ) ) {
                        if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                                return new WP_REST_Response(
                                        array(
                                                'ok'    => false,
                                                'error' => __( 'You are not allowed to generate AI content.', 'pf2' ),
                                        ),
                                        403
                                );
                        }
                }

                $rate_key = pf2_rest_ai_rate_limit_key( $request );

                if ( false !== get_transient( $rate_key ) ) {
                        return new WP_REST_Response(
                                array(
                                        'ok'    => false,
                                        'error' => __( 'Please wait before requesting new AI content.', 'pf2' ),
                                ),
                                429
                        );
                }

                set_transient( $rate_key, time(), 3 );

                $provider = apply_filters( 'pf2_ai/provider', 'mock' );
                $provider = is_string( $provider ) ? sanitize_key( $provider ) : 'mock';

                $adapter = pf2_rest_ai_resolve_adapter( $provider );

                if ( ! $adapter instanceof AdapterInterface ) {
                        $adapter  = new MockAdapter();
                        $provider = 'mock';
                }

                $prompt_args = array(
                        'topic'    => $topic,
                        'tone'     => $tone,
                        'keywords' => $keywords,
                        'lang'     => $lang,
                );

                $prompt_args = apply_filters( 'pf2_ai/prompt', $prompt_args, $request );

                if ( ! is_array( $prompt_args ) ) {
                        $prompt_args = array();
                }

                $prompt_args = wp_parse_args(
                        $prompt_args,
                        array(
                                'topic'    => $topic,
                                'tone'     => $tone,
                                'keywords' => $keywords,
                                'lang'     => $lang,
                        )
                );

                if ( ! is_array( $prompt_args['keywords'] ?? null ) ) {
                        $prompt_args['keywords'] = $keywords;
                }

                try {
                        $result = $adapter->generate( $prompt_args );
                } catch ( Throwable $throwable ) {
                        $message = $throwable->getMessage();

                        if ( empty( $message ) ) {
                                $message = __( 'AI provider failed to generate content.', 'pf2' );
                        }

                        return new WP_REST_Response(
                                array(
                                        'ok'    => false,
                                        'error' => $message,
                                ),
                                500
                        );
                }

                if ( ! is_array( $result ) ) {
                        return new WP_REST_Response(
                                array(
                                        'ok'    => false,
                                        'error' => __( 'Invalid AI provider response.', 'pf2' ),
                                ),
                                502
                        );
                }

                $provider_used = $provider;

                if ( isset( $result['provider'] ) && is_string( $result['provider'] ) ) {
                        $provider_used = sanitize_key( $result['provider'] );
                        unset( $result['provider'] );
                }

                $title = isset( $result['title'] ) ? sanitize_text_field( $result['title'] ) : '';
                $title = pf2_str_limit( $title, 60 );

                $description = isset( $result['description'] ) ? wp_strip_all_tags( (string) $result['description'] ) : '';
                $description = pf2_str_limit( $description, 160 );

                if ( mb_strlen( $description ) < 140 ) {
                        $description = pf2_str_limit(
                                trim( $description . ' ' . $title ),
                                150
                        );
                }

                $outline_items = array();

                if ( isset( $result['outline'] ) && is_array( $result['outline'] ) ) {
                        foreach ( $result['outline'] as $item ) {
                                if ( ! is_scalar( $item ) ) {
                                        continue;
                                }

                                $item = sanitize_text_field( (string) $item );

                                if ( '' === $item ) {
                                        continue;
                                }

                                $outline_items[] = $item;

                                if ( count( $outline_items ) >= 8 ) {
                                        break;
                                }
                        }
                }

                if ( count( $outline_items ) < 5 ) {
                        $fallback_outline = ( new MockAdapter() )->generate( $prompt_args );

                        if ( isset( $fallback_outline['outline'] ) && is_array( $fallback_outline['outline'] ) ) {
                                foreach ( $fallback_outline['outline'] as $fallback_item ) {
                                        $fallback_item = sanitize_text_field( (string) $fallback_item );

                                        if ( '' === $fallback_item || in_array( $fallback_item, $outline_items, true ) ) {
                                                continue;
                                        }

                                        $outline_items[] = $fallback_item;

                                        if ( count( $outline_items ) >= 5 ) {
                                                break;
                                        }
                                }
                        }
                }

                $outline_items = array_values( array_unique( array_slice( $outline_items, 0, 8 ) ) );

                if ( count( $outline_items ) < 5 ) {
                        $mock_fill = ( new MockAdapter() )->generate( $prompt_args );

                        if ( isset( $mock_fill['outline'] ) && is_array( $mock_fill['outline'] ) ) {
                                foreach ( $mock_fill['outline'] as $mock_item ) {
                                        $mock_item = sanitize_text_field( (string) $mock_item );

                                        if ( '' === $mock_item || in_array( $mock_item, $outline_items, true ) ) {
                                                continue;
                                        }

                                        $outline_items[] = $mock_item;

                                        if ( count( $outline_items ) >= 5 ) {
                                                break;
                                        }
                                }
                        }
                }

                if ( count( $outline_items ) > 8 ) {
                        $outline_items = array_slice( $outline_items, 0, 8 );
                }

                $payload = array(
                        'ok'          => true,
                        'title'       => $title,
                        'description' => $description,
                        'outline'     => array_values( $outline_items ),
                        'provider'    => $provider_used,
                );

                $payload = apply_filters( 'pf2_ai/response', $payload, $prompt_args, $request );

                return rest_ensure_response( $payload );
        }
}

if ( ! function_exists( 'pf2_rest_ai_rate_limit_key' ) ) {
        /**
         * Build a transient key for rate limiting based on user/IP.
         *
         * @param WP_REST_Request $request REST request.
         *
         * @return string
         */
        function pf2_rest_ai_rate_limit_key( WP_REST_Request $request ) {
                $user_id = get_current_user_id();

                if ( $user_id ) {
                        $identifier = 'user_' . $user_id;
                } else {
                        $ip = $request->get_header( 'x-forwarded-for' );

                        if ( is_string( $ip ) && '' !== $ip ) {
                                $ip = explode( ',', $ip );
                                $ip = trim( $ip[0] );
                        } else {
                                $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : 'guest';
                        }

                        $identifier = 'ip_' . md5( sanitize_text_field( $ip ) );
                }

                return 'pf2_ai_rl_' . md5( $identifier );
        }
}

if ( ! function_exists( 'pf2_rest_ai_resolve_adapter' ) ) {
        /**
         * Resolve the adapter instance for the requested provider.
         *
         * @param string $provider Provider slug.
         *
         * @return AdapterInterface
         */
        function pf2_rest_ai_resolve_adapter( string $provider ): AdapterInterface {
                switch ( $provider ) {
                        case 'openai':
                                return new OpenAIAdapter();
                        default:
                                return new MockAdapter();
                }
        }
}
