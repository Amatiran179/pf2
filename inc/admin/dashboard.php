<?php
/**
 * Analytics dashboard page.
 *
 * Renders CTA metric summaries for administrators.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_admin_dashboard_page' ) ) {
        /**
         * Render the analytics dashboard.
         *
         * @return void
         */
        function pf2_admin_dashboard_page() {
                if ( ! current_user_can( 'manage_options' ) ) {
                        return;
                }

                $range = isset( $_GET['pf2_range'] ) ? sanitize_key( wp_unslash( $_GET['pf2_range'] ) ) : '7d'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $range = pf2_metrics_normalize_range( $range );
                $data  = pf2_metrics_prepare_summary( $range );

                $totals    = isset( $data['totals'] ) && is_array( $data['totals'] ) ? $data['totals'] : array();
                $top_pages = isset( $data['top'] ) && is_array( $data['top'] ) ? $data['top'] : array();
                $timeline  = isset( $data['timeline'] ) && is_array( $data['timeline'] ) ? $data['timeline'] : array();

                $range_options = array(
                        'today' => __( 'Today', 'pf2' ),
                        '7d'    => __( 'Last 7 days', 'pf2' ),
                        '30d'   => __( 'Last 30 days', 'pf2' ),
                        'all'   => __( 'All data', 'pf2' ),
                );

                $selected_total = isset( $data['total'] ) ? (int) $data['total'] : 0;
                $timeline_max   = 0;

                foreach ( $timeline as $entry ) {
                        $timeline_max = max( $timeline_max, isset( $entry['count'] ) ? (int) $entry['count'] : 0 );
                }
                ?>
                <div class="wrap pf2-dashboard">
                        <h1><?php esc_html_e( 'PutraFiber Analytics Dashboard', 'pf2' ); ?></h1>

                        <p><?php esc_html_e( 'Monitor CTA engagement, review top performing content, and export historical data.', 'pf2' ); ?></p>

                        <form method="get" class="pf2-dashboard__range">
                                <input type="hidden" name="page" value="pf2" />
                                <label for="pf2_range">
                                        <?php esc_html_e( 'Select range:', 'pf2' ); ?>
                                        <select name="pf2_range" id="pf2_range">
                                                <?php foreach ( $range_options as $value => $label ) : ?>
                                                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $range, $value ); ?>><?php echo esc_html( $label ); ?></option>
                                                <?php endforeach; ?>
                                        </select>
                                </label>
                                <?php submit_button( __( 'Apply', 'pf2' ), 'secondary', '', false ); ?>
                        </form>

                        <div class="pf2-dashboard__cards">
                                <div class="pf2-dashboard__card">
                                        <h2><?php esc_html_e( 'CTA clicks (selected range)', 'pf2' ); ?></h2>
                                        <p class="pf2-dashboard__metric"><?php echo esc_html( number_format_i18n( $selected_total ) ); ?></p>
                                </div>
                                <div class="pf2-dashboard__card">
                                        <h2><?php esc_html_e( 'Today', 'pf2' ); ?></h2>
                                        <p class="pf2-dashboard__metric"><?php echo esc_html( number_format_i18n( isset( $totals['today'] ) ? (int) $totals['today'] : 0 ) ); ?></p>
                                </div>
                                <div class="pf2-dashboard__card">
                                        <h2><?php esc_html_e( '7 days', 'pf2' ); ?></h2>
                                        <p class="pf2-dashboard__metric"><?php echo esc_html( number_format_i18n( isset( $totals['7d'] ) ? (int) $totals['7d'] : 0 ) ); ?></p>
                                </div>
                                <div class="pf2-dashboard__card">
                                        <h2><?php esc_html_e( '30 days', 'pf2' ); ?></h2>
                                        <p class="pf2-dashboard__metric"><?php echo esc_html( number_format_i18n( isset( $totals['30d'] ) ? (int) $totals['30d'] : 0 ) ); ?></p>
                                </div>
                        </div>

                        <h2><?php esc_html_e( 'Top CTA sources', 'pf2' ); ?></h2>
                        <table class="widefat pf2-dashboard__table">
                                <thead>
                                        <tr>
                                                <th><?php esc_html_e( 'Page', 'pf2' ); ?></th>
                                                <th><?php esc_html_e( 'URL', 'pf2' ); ?></th>
                                                <th><?php esc_html_e( 'Clicks', 'pf2' ); ?></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <?php if ( empty( $top_pages ) ) : ?>
                                                <tr>
                                                        <td colspan="3"><?php esc_html_e( 'No CTA clicks recorded for this range.', 'pf2' ); ?></td>
                                                </tr>
                                        <?php else : ?>
                                                <?php foreach ( $top_pages as $row ) :
                                                        $title = isset( $row['title'] ) ? $row['title'] : __( 'Unknown', 'pf2' );
                                                        $url   = isset( $row['url'] ) ? $row['url'] : '';
                                                        $count = isset( $row['count'] ) ? (int) $row['count'] : 0;
                                                        ?>
                                                        <tr>
                                                                <td><?php echo esc_html( $title ); ?></td>
                                                                <td>
                                                                        <?php if ( $url ) : ?>
                                                                                <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $url ); ?></a>
                                                                        <?php else : ?>
                                                                                <em><?php esc_html_e( 'Not captured', 'pf2' ); ?></em>
                                                                        <?php endif; ?>
                                                                </td>
                                                                <td><?php echo esc_html( number_format_i18n( $count ) ); ?></td>
                                                        </tr>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                </tbody>
                        </table>

                        <h2><?php esc_html_e( 'Timeline', 'pf2' ); ?></h2>
                        <table class="widefat pf2-dashboard__table">
                                <thead>
                                        <tr>
                                                <th><?php esc_html_e( 'Date', 'pf2' ); ?></th>
                                                <th><?php esc_html_e( 'Clicks', 'pf2' ); ?></th>
                                                <th><?php esc_html_e( 'Activity', 'pf2' ); ?></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <?php if ( empty( $timeline ) ) : ?>
                                                <tr>
                                                        <td colspan="3"><?php esc_html_e( 'No events captured for the selected range.', 'pf2' ); ?></td>
                                                </tr>
                                        <?php else : ?>
                                                <?php foreach ( $timeline as $entry ) :
                                                        $date  = isset( $entry['date'] ) ? $entry['date'] : '';
                                                        $count = isset( $entry['count'] ) ? (int) $entry['count'] : 0;
                                                        $ratio = ( $timeline_max > 0 ) ? round( ( $count / $timeline_max ) * 20 ) : 0;
                                                        $ratio = max( 0, min( 20, $ratio ) );
                                                        ?>
                                                        <tr>
                                                                <td><?php echo esc_html( $date ); ?></td>
                                                                <td><?php echo esc_html( number_format_i18n( $count ) ); ?></td>
                                                                <td><code><?php echo esc_html( str_repeat( '█', $ratio ) ); ?></code></td>
                                                        </tr>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                </tbody>
                        </table>

                        <h2><?php esc_html_e( 'Export', 'pf2' ); ?></h2>
                        <p><?php esc_html_e( 'Download the captured events as CSV or JSON for archival analysis.', 'pf2' ); ?></p>
                        <div class="pf2-dashboard__export">
                                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="pf2-dashboard__export-form">
                                        <?php wp_nonce_field( 'pf2_metrics_export' ); ?>
                                        <input type="hidden" name="action" value="pf2_export_metrics" />
                                        <input type="hidden" name="range" value="<?php echo esc_attr( $range ); ?>" />
                                        <input type="hidden" name="format" value="csv" />
                                        <?php submit_button( __( 'Export CSV', 'pf2' ), 'secondary', 'submit', false ); ?>
                                </form>
                                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="pf2-dashboard__export-form">
                                        <?php wp_nonce_field( 'pf2_metrics_export' ); ?>
                                        <input type="hidden" name="action" value="pf2_export_metrics" />
                                        <input type="hidden" name="range" value="<?php echo esc_attr( $range ); ?>" />
                                        <input type="hidden" name="format" value="json" />
                                        <?php submit_button( __( 'Export JSON', 'pf2' ), 'secondary', 'submit', false ); ?>
                                </form>
                        </div>
                </div>
                <?php
        }
}
