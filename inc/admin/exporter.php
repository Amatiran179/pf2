<?php
/**
 * Metrics exporter handlers.
 *
 * Provides CSV and JSON download endpoints for CTA metrics.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_admin_handle_metrics_export' ) ) {
        /**
         * Handle metric export requests.
         *
         * @return void
         */
        function pf2_admin_handle_metrics_export() {
                if ( ! current_user_can( 'manage_options' ) ) {
                        wp_die( esc_html__( 'You are not allowed to export metrics.', 'pf2' ), esc_html__( 'Access denied', 'pf2' ), array( 'response' => 403 ) );
                }

                check_admin_referer( 'pf2_metrics_export' );

                $format = isset( $_POST['format'] ) ? sanitize_key( wp_unslash( $_POST['format'] ) ) : 'csv';
                $range  = isset( $_POST['range'] ) ? sanitize_key( wp_unslash( $_POST['range'] ) ) : 'all';

                $range  = pf2_metrics_normalize_range( $range );
                $events = pf2_metrics_summary_for_range( $range );
                $suffix = $range ? $range : 'all';
                $stamp  = gmdate( 'Ymd-His' );

                if ( 'json' === $format ) {
                        pf2_admin_metrics_export_json( $events, $suffix, $stamp );
                        return;
                }

                pf2_admin_metrics_export_csv( $events, $suffix, $stamp );
        }
}
add_action( 'admin_post_pf2_export_metrics', 'pf2_admin_handle_metrics_export' );

if ( ! function_exists( 'pf2_admin_metrics_export_json' ) ) {
        /**
         * Stream metrics as a JSON document.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @param string                            $suffix File name suffix.
         * @param string                            $stamp  Timestamp suffix.
         * @return void
         */
        function pf2_admin_metrics_export_json( array $events, $suffix, $stamp ) {
                nocache_headers();
                header( 'Content-Type: application/json; charset=utf-8' );
                header( 'Content-Disposition: attachment; filename=pf2-metrics-' . $suffix . '-' . $stamp . '.json' );

                echo wp_json_encode( array_values( $events ) );
                exit;
        }
}

if ( ! function_exists( 'pf2_admin_metrics_export_csv' ) ) {
        /**
         * Stream metrics as a CSV document.
         *
         * @param array<int, array<string, mixed>> $events Event collection.
         * @param string                            $suffix File name suffix.
         * @param string                            $stamp  Timestamp suffix.
         * @return void
         */
        function pf2_admin_metrics_export_csv( array $events, $suffix, $stamp ) {
                nocache_headers();
                header( 'Content-Type: text/csv; charset=utf-8' );
                header( 'Content-Disposition: attachment; filename=pf2-metrics-' . $suffix . '-' . $stamp . '.csv' );

                $handle = fopen( 'php://output', 'w' );

                if ( ! $handle ) {
                        exit;
                }

                fputcsv( $handle, array( 'ts', 'date', 'type', 'pid', 'ref', 'cta_type' ) );

                foreach ( $events as $event ) {
                        $ts       = isset( $event['ts'] ) ? (int) $event['ts'] : 0;
                        $date     = $ts ? wp_date( DATE_RFC3339, $ts ) : '';
                        $type     = isset( $event['type'] ) ? sanitize_key( $event['type'] ) : '';
                        $pid      = isset( $event['pid'] ) ? absint( $event['pid'] ) : 0;
                        $ref      = isset( $event['ref'] ) ? sanitize_text_field( (string) $event['ref'] ) : '';
                        $cta_type = isset( $event['extra']['cta_type'] ) ? sanitize_text_field( $event['extra']['cta_type'] ) : '';

                        fputcsv( $handle, array( $ts, $date, $type, $pid, $ref, $cta_type ) );
                }

                fclose( $handle );
                exit;
        }
}
