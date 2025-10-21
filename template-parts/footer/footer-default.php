<?php
/**
 * Default footer template.
 *
 * @package PF2\TemplateParts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="pf2-footer pf2-footer--default">
	<div class="pf2-container">
		<p class="pf2-footer__text">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'pf2' ); ?></p>
	</div>
</footer>
