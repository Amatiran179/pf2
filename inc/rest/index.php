<?php
/**
 * REST API bootstrap.
 *
 * Registers the pf2/v1 namespace and wires REST controllers.
 *
 * @package PF2\Rest
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

require_once get_template_directory() . '/inc/rest/metrics.php';

add_action(
        'rest_api_init',
        function () {
                register_rest_route(
                        'pf2/v1',
                        '/metrics',
                        array(
                                array(
                                        'methods'             => 'POST',
                                        'callback'            => 'pf2_rest_metrics_post',
                                        'permission_callback' => '__return_true',
                                ),
                                array(
                                        'methods'             => 'GET',
                                        'callback'            => 'pf2_rest_metrics_get',
                                        'permission_callback' => static function () {
                                                return current_user_can( 'manage_options' );
                                        },
                                ),
                        )
                );
        }
);
