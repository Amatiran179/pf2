<?php
/**
 * Custom post type loader.
 *
 * Bootstraps pf2 enterprise CPT registrations.
 *
 * @package PF2\CPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_template_directory() . '/inc/cpt/register-product.php';
require_once get_template_directory() . '/inc/cpt/register-portfolio.php';
require_once get_template_directory() . '/inc/cpt/register-service.php';
require_once get_template_directory() . '/inc/cpt/register-team.php';
require_once get_template_directory() . '/inc/cpt/register-testimonial.php';

pf2_register_product_post_type();
pf2_register_portfolio_post_type();
pf2_register_service_post_type();
pf2_register_team_post_type();
pf2_register_testimonial_post_type();

