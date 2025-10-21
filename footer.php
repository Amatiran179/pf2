<?php
/**
 * Footer template wrapper.
 *
 * Ensures WordPress core footer hooks execute while keeping footer layout
 * encapsulated inside template parts for maintainability.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

/**
 * Allow child themes or plugins to inject markup before the footer renders.
 */
do_action( 'pf2_before_footer' );

get_template_part( 'template-parts/footer/footer', 'default' );

/**
 * Allow child themes or plugins to append markup after the footer renders.
 */
do_action( 'pf2_after_footer' );

wp_footer();
?>
</body>
</html>
