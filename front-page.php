<?php
/**
 * Front page template.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="site-main" role="main">
	<?php get_template_part( 'template-parts/hero/hero', 'default' ); ?>

	<section class="pf2-section pf2-section--portfolio" aria-labelledby="pf2-latest-portfolio-heading">
		<div class="pf2-container">
			<header class="pf2-section__header">
				<h2 id="pf2-latest-portfolio-heading" class="pf2-section__title">
					<?php esc_html_e( 'Latest Portfolio', 'pf2' ); ?>
				</h2>
				<p class="pf2-section__subtitle">
					<?php esc_html_e( 'Discover our most recent highlights.', 'pf2' ); ?>
				</p>
			</header>

			<div class="pf2-grid pf2-grid--portfolio">
				<?php
				$portfolio_query = new WP_Query(
					array(
						'post_type'      => 'portfolio',
						'posts_per_page' => 6,
						'post_status'    => 'publish',
						'no_found_rows'  => true,
					)
				);

				if ( $portfolio_query->have_posts() ) {
					while ( $portfolio_query->have_posts() ) {
						$portfolio_query->the_post();
						get_template_part( 'template-parts/portfolio/loop', 'item' );
					}
				} else {
					?>
					<p class="pf2-empty">
						<?php esc_html_e( 'No portfolio yet.', 'pf2' ); ?>
					</p>
					<?php
				}

				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
