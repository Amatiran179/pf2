<?php
/**
 * Metrics REST controller and helpers.
 *
 * Provides storage helpers, aggregation routines, and REST callbacks
 * for CTA click analytics.
 *
 * @package PF2\Rest
 */


if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_metrics_get_option_key' ) ) {
        /**
         * Retrieve the option key used for persistent metric storage.
         *
         * @return string
         */
        function pf2_metrics_get_option_key() {
                return 'pf2_metrics_events';
        }
}

if ( ! function_exists( 'pf2_metrics_get_max_events' ) ) {
        /**
         * Retrieve the maximum number of events retained in memory.
         *
         * @return int
         */
        function pf2_metrics_get_max_events() {
                return 5000;
        }
}

if ( ! function_exists( 'pf2_metrics_supported_types' ) ) {
        /**
         * Retrieve the list of supported metric types.
         *
         * @return string[]
         */
        function pf2_metrics_supported_types() {
                return array( 'cta_click' );
        }
}

if ( ! function_exists( 'pf2_metrics_get_events' ) ) {
        /**
         * Fetch all recorded metric events.
         *
         * @return array<int, array<string, mixed>>
         */
        function pf2_metrics_get_events() {
                $stored = get_option( pf2_metrics_get_option_key(), array() );

                if ( ! is_array( $stored ) ) {
                        return array();
                }

                return array_values( array_filter( $stored, 'is_array' ) );
        }
}

if ( ! function_exists( 'pf2_metrics_save_events' ) ) {
        /**
         * Persist the provided metric collection.
         *
         * @param array<int, array<string, mixed>> $events Metric events.
         * @return void
         */
        function pf2_metrics_save_events( array $events ) {
                update_option( pf2_metrics_get_option_key(), array_values( $events ), false );
        }
}

if ( ! function_exists( 'pf2_metrics_normalize_extra' ) ) {
        /**
         * Sanitize the `extra` payload section.
         *
         * @param mixed $extra Raw extra payload.
         * @return array<string, string>
         */
        function pf2_metrics_normalize_extra( $extra ) {
                if ( ! is_array( $extra ) ) {
                        return array();
                }

                $sanitized = array();

                foreach ( $extra as $key => $value ) {
                        $sanitized_key = sanitize_key( (string) $key );

                        if ( '' === $sanitized_key ) {
                                continue;
                        }

                        if ( is_scalar( $value ) ) {
                                $sanitized[ $sanitized_key ] = sanitize_text_field( (string) $value );
                        }
                }

                return $sanitized;
        }
}

if ( ! function_exists( 'pf2_metrics_append_event' ) ) {
        /**
         * Append a single event to the ring buffer.
         *
         * @param array<string, mixed> $event Event payload.
         * @return void
         */
        function pf2_metrics_append_event( array $event ) {
                $events = pf2_metrics_get_events();
                $events[] = $event;

                $excess = count( $events ) - pf2_metrics_get_max_events();

                if ( $excess > 0 ) {
                        $events = array_slice( $events, $excess );
                }

                pf2_metrics_save_events( $events );
        }
}

if ( ! function_exists( 'pf2_metrics_normalize_range' ) ) {
        /**
         * Normalize the requested range parameter.
         *
         * @param string $range Raw range parameter.
         * @return string
         */
        function pf2_metrics_normalize_range( $range ) {
                $range   = sanitize_key( $range );
                $allowed = array( 'today', '7d', '30d', 'all' );

                if ( in_array( $range, $allowed, true ) ) {
                        return $range;
                }

                return '7d';
        }
}

if ( ! function_exists( 'pf2_metrics_get_range_start' ) ) {
        /**
         * Determine the UTC timestamp that bounds the requested range.
         *
         * @param string $range Normalized range identifier.
         * @return int
         */
        function pf2_metrics_get_range_start( $range ) {
                $range     = pf2_metrics_normalize_range( $range );
                $timezone  = wp_timezone();
                $now_local = new DateTimeImmutable( 'now', $timezone );
                $start     = $now_local->setTime( 0, 0, 0 );

                switch ( $range ) {
                        case 'today':
                                break;
                        case '7d':
                                $start = $start->modify( '-6 days' );
                                break;
                        case '30d':
                                $start = $start->modify( '-29 days' );
                                break;
                        case 'all':
                        default:
                                return 0;
                }

                return $start->setTimezone( new DateTimeZone( 'UTC' ) )->getTimestamp();
        }
}

if ( ! function_exists( 'pf2_metrics_filter_events_by_range' ) ) {
        /**
         * Filter events within the provided range.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @param string                            $range  Range identifier.
         * @return array<int, array<string, mixed>>
         */
        function pf2_metrics_filter_events_by_range( array $events, $range ) {
                $range    = pf2_metrics_normalize_range( $range );
                $start_ts = pf2_metrics_get_range_start( $range );

                if ( 0 === $start_ts ) {
                        return array_values( $events );
                }

                return array_values(
                        array_filter(
                                $events,
                                static function ( $event ) use ( $start_ts ) {
                                        $ts = isset( $event['ts'] ) ? (int) $event['ts'] : 0;

                                        return $ts >= $start_ts;
                                }
                        )
                );
        }
}

if ( ! function_exists( 'pf2_metrics_only_type' ) ) {
        /**
         * Filter events by metric type.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @param string                            $type   Metric type.
         * @return array<int, array<string, mixed>>
         */
        function pf2_metrics_only_type( array $events, $type ) {
                $type = sanitize_key( $type );

                return array_values(
                        array_filter(
                                $events,
                                static function ( $event ) use ( $type ) {
                                        return isset( $event['type'] ) && sanitize_key( (string) $event['type'] ) === $type;
                                }
                        )
                );
        }
}

if ( ! function_exists( 'pf2_metrics_count_for_range' ) ) {
        /**
         * Count the number of events captured within a range.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @param string                            $range  Range identifier.
         * @return int
         */
        function pf2_metrics_count_for_range( array $events, $range ) {
                return count( pf2_metrics_filter_events_by_range( $events, $range ) );
        }
}

if ( ! function_exists( 'pf2_metrics_build_top_pages' ) ) {
        /**
         * Build a leaderboard of top-performing pages.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @param int                               $limit  Maximum results to return.
         * @return array<int, array<string, mixed>>
         */
        function pf2_metrics_build_top_pages( array $events, $limit = 10 ) {
                $tally = array();

                foreach ( $events as $event ) {
                        $pid = isset( $event['pid'] ) ? absint( $event['pid'] ) : 0;
                        $ref = isset( $event['ref'] ) ? sanitize_text_field( (string) $event['ref'] ) : '';

                        $key = $pid > 0 ? 'pid:' . $pid : 'ref:' . $ref;

                        if ( ! isset( $tally[ $key ] ) ) {
                                $tally[ $key ] = array(
                                        'pid'   => $pid,
                                        'ref'   => $ref,
                                        'count' => 0,
                                );
                        }

                        $tally[ $key ]['count']++;
                }

                uasort(
                        $tally,
                        static function ( $left, $right ) {
                                return (int) $right['count'] <=> (int) $left['count'];
                        }
                );

                $tally = array_slice( $tally, 0, max( 1, absint( $limit ) ) );
                $results = array();

                foreach ( $tally as $item ) {
                        $pid   = isset( $item['pid'] ) ? absint( $item['pid'] ) : 0;
                        $title = $pid > 0 ? get_the_title( $pid ) : '';
                        $url   = $pid > 0 ? get_permalink( $pid ) : $item['ref'];
                        $url   = $url ? esc_url_raw( (string) $url ) : '';

                        $results[] = array(
                                'pid'   => $pid,
                                'title' => $title ? $title : __( 'Unknown', 'pf2' ),
                                'url'   => $url,
                                'count' => isset( $item['count'] ) ? (int) $item['count'] : 0,
                        );
                }

                return $results;
        }
}

if ( ! function_exists( 'pf2_metrics_get_timeline_bucket_count' ) ) {
        /**
         * Determine the number of days to render within the timeline.
         *
         * @param string $range Range identifier.
         * @return int
         */
        function pf2_metrics_get_timeline_bucket_count( $range ) {
                switch ( $range ) {
                        case 'today':
                                return 1;
                        case '30d':
                                return 30;
                        case 'all':
                                return 30;
                        case '7d':
                        default:
                                return 7;
                }
        }
}

if ( ! function_exists( 'pf2_metrics_build_timeline' ) ) {
        /**
         * Construct a day-by-day timeline for the provided events.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @param string                            $range  Range identifier.
         * @return array<int, array<string, int|string>>
         */
        function pf2_metrics_build_timeline( array $events, $range ) {
                $range       = pf2_metrics_normalize_range( $range );
                $timezone    = wp_timezone();
                $bucket_days = pf2_metrics_get_timeline_bucket_count( $range );
                $start_local = new DateTimeImmutable( 'now', $timezone );
                $start_local = $start_local->setTime( 0, 0, 0 );

                $buckets = array();

                for ( $i = $bucket_days - 1; $i >= 0; $i-- ) {
                        $date = $start_local->modify( sprintf( '-%d days', $i ) );
                        $key  = $date->format( 'Y-m-d' );
                        $buckets[ $key ] = 0;
                }

                foreach ( $events as $event ) {
                        $ts = isset( $event['ts'] ) ? (int) $event['ts'] : 0;

                        if ( $ts <= 0 ) {
                                continue;
                        }

                        $event_date = ( new DateTimeImmutable( '@' . $ts ) )->setTimezone( $timezone )->format( 'Y-m-d' );

                        if ( isset( $buckets[ $event_date ] ) ) {
                                $buckets[ $event_date ]++;
                        }
                }

                $timeline = array();

                foreach ( $buckets as $date => $count ) {
                        $timeline[] = array(
                                'date'  => $date,
                                'count' => (int) $count,
                        );
                }

                return $timeline;
        }
}

if ( ! function_exists( 'pf2_metrics_prepare_totals' ) ) {
        /**
         * Prepare standard range totals for dashboard widgets.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @return array<string, int>
         */
        function pf2_metrics_prepare_totals( array $events ) {
                return array(
                        'today' => pf2_metrics_count_for_range( $events, 'today' ),
                        '7d'    => pf2_metrics_count_for_range( $events, '7d' ),
                        '30d'   => pf2_metrics_count_for_range( $events, '30d' ),
                        'all'   => count( $events ),
                );
        }
}

if ( ! function_exists( 'pf2_metrics_prepare_summary' ) ) {
        /**
         * Prepare a summary payload for the dashboard or REST consumer.
         *
         * @param string $range Range identifier.
         * @return array<string, mixed>
         */
        function pf2_metrics_prepare_summary( $range ) {
                $range      = pf2_metrics_normalize_range( $range );
                $events     = pf2_metrics_only_type( pf2_metrics_get_events(), 'cta_click' );
                $in_range   = pf2_metrics_filter_events_by_range( $events, $range );
                $totals     = pf2_metrics_prepare_totals( $events );
                $top_pages  = pf2_metrics_build_top_pages( $in_range );
                $timeline   = pf2_metrics_build_timeline( $in_range, $range );
                $range_meta = array(
                        'range'       => $range,
                        'total'       => count( $in_range ),
                        'totals'      => $totals,
                        'top'         => $top_pages,
                        'timeline'    => $timeline,
                        'generatedAt' => current_time( 'mysql' ),
                );

                return $range_meta;
        }
}

if ( ! function_exists( 'pf2_metrics_summary_for_range' ) ) {
        /**
         * Retrieve the raw event list for export within a range.
         *
         * @param string $range Range identifier.
         * @return array<int, array<string, mixed>>
         */
        function pf2_metrics_summary_for_range( $range ) {
                $range = pf2_metrics_normalize_range( $range );
                $events = pf2_metrics_only_type( pf2_metrics_get_events(), 'cta_click' );

                return pf2_metrics_filter_events_by_range( $events, $range );
        }
}

if ( ! function_exists( 'pf2_metrics_get_client_fingerprint' ) ) {
        /**
         * Generate a lightweight fingerprint for rate limiting.
         *
         * @param WP_REST_Request $request The REST request.
         * @return string
         */
        function pf2_metrics_get_client_fingerprint( WP_REST_Request $request ) {
                $remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
                $user_agent  = $request->get_header( 'user-agent' );

                if ( empty( $user_agent ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
                        $user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
                }

                $user_agent = is_string( $user_agent ) ? $user_agent : '';

                return md5( $remote_addr . '|' . $user_agent );
        }
}

if ( ! function_exists( 'pf2_metrics_rate_limit_allows' ) ) {
        /**
         * Check and enforce a lightweight rate limit for metric submissions.
         *
         * @param WP_REST_Request $request The REST request.
         * @param string          $type    Metric type.
         * @return bool
         */
        function pf2_metrics_rate_limit_allows( WP_REST_Request $request, $type ) {
                $fingerprint = pf2_metrics_get_client_fingerprint( $request );

                /**
                 * Allow short-circuiting the rate limit decision.
                 *
                 * @param bool|null        $allow      Null to continue evaluation, boolean to force a result.
                 * @param WP_REST_Request  $request    The REST request object.
                 * @param string           $type       Metric type slug.
                 * @param string           $fingerprint Client fingerprint hash.
                 */
                $pre = apply_filters( 'pf2_metrics_rate_limit_pre', null, $request, $type, $fingerprint );

                if ( null !== $pre ) {
                        return (bool) $pre;
                }

                if ( '' === $fingerprint ) {
                        return (bool) apply_filters( 'pf2_metrics_rate_limit_allows', true, $request, $type, $fingerprint, 0 );
                }

                $key = 'pf2_rl_' . md5( $fingerprint . '|' . sanitize_key( $type ) );

                if ( get_transient( $key ) ) {
                        return (bool) apply_filters( 'pf2_metrics_rate_limit_allows', false, $request, $type, $fingerprint, 0 );
                }

                $ttl = (int) apply_filters( 'pf2_metrics_rate_limit_ttl', 3, $request, $type, $fingerprint );
                $ttl = max( 1, $ttl );

                set_transient( $key, 1, $ttl );

                return (bool) apply_filters( 'pf2_metrics_rate_limit_allows', true, $request, $type, $fingerprint, $ttl );
        }
}

if ( ! function_exists( 'pf2_rest_metrics_post' ) ) {
        /**
         * REST callback for recording a CTA metric event.
         *
         * @param WP_REST_Request $request The REST request.
         * @return WP_Error|WP_REST_Response
         */
        function pf2_rest_metrics_post( WP_REST_Request $request ) {
                $nonce = $request->get_header( 'x-wp-nonce' );

                if ( ! $nonce ) {
                        $nonce = $request->get_header( 'x_wp_nonce' );
                }

                $nonce = is_string( $nonce ) ? sanitize_text_field( wp_unslash( $nonce ) ) : '';

                if ( '' === $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                        return new WP_Error( 'pf2_metrics_invalid_nonce', __( 'Invalid submission nonce.', 'pf2' ), array( 'status' => rest_authorization_required_code() ) );
                }

                $payload = $request->get_json_params();

                if ( ! is_array( $payload ) ) {
                        return new WP_Error( 'pf2_metrics_invalid_payload', __( 'Invalid payload.', 'pf2' ), array( 'status' => 400 ) );
                }

                $type = isset( $payload['type'] ) ? sanitize_key( $payload['type'] ) : '';

                if ( ! in_array( $type, pf2_metrics_supported_types(), true ) ) {
                        return new WP_Error( 'pf2_metrics_unsupported_type', __( 'Unsupported metric type.', 'pf2' ), array( 'status' => 400 ) );
                }

                if ( ! pf2_metrics_rate_limit_allows( $request, $type ) ) {
                        return new WP_Error( 'pf2_metrics_rate_limited', __( 'Too many requests. Please slow down.', 'pf2' ), array( 'status' => 429 ) );
                }

                $pid = isset( $payload['pid'] ) ? absint( $payload['pid'] ) : 0;
                $ref_raw = isset( $payload['ref'] ) ? $payload['ref'] : '';
                $ref     = is_string( $ref_raw ) ? sanitize_text_field( wp_unslash( $ref_raw ) ) : '';

                if ( $ref ) {
                        $ref = function_exists( 'mb_substr' ) ? mb_substr( $ref, 0, 250 ) : substr( $ref, 0, 250 );
                }
                $extra = pf2_metrics_normalize_extra( isset( $payload['extra'] ) ? $payload['extra'] : array() );

                $event = array(
                        'ts'    => current_time( 'timestamp', true ),
                        'type'  => $type,
                        'pid'   => $pid,
                        'ref'   => $ref,
                        'extra' => $extra,
                );

                pf2_metrics_append_event( $event );

                return new WP_REST_Response(
                        array(
                                'ok' => true,
                        ),
                        201
                );
        }
}

if ( ! function_exists( 'pf2_rest_metrics_get' ) ) {
        /**
         * REST callback for retrieving aggregated CTA metrics.
         *
         * @param WP_REST_Request $request The REST request.
         * @return WP_Error|WP_REST_Response
         */
        function pf2_rest_metrics_get( WP_REST_Request $request ) {
                $range   = $request->get_param( 'range' );
                $summary = pf2_metrics_prepare_summary( $range );

                return rest_ensure_response( $summary );
        }
}
