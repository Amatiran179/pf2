<?php
/**
 * Front page placeholder template.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main">
	<section class="pf2-hero">
		<div class="pf2-container">
			<h1><?php esc_html_e( 'PutraFiber AI Theme v2', 'pf2' ); ?></h1>
			<p><?php esc_html_e( 'Bootstrap batch in progress. Customize this layout in upcoming batches.', 'pf2' ); ?></p>
		</div>
	</section>
</main>
<?php
get_footer();
