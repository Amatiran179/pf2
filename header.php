<?php
/**
 * Header template wrapper.
 *
 * Provides the theme header skeleton while delegating layout markup to
 * template parts so child themes and plugins can override specific sections.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'pf2' ); ?></a>
<?php
wp_body_open();
?>
<?php
do_action( 'pf2_before_header' );

get_template_part( 'template-parts/header/header', 'default' );

do_action( 'pf2_after_header' );
