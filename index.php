<?php
/**
 * Index template.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="site-main" role="main">
	<div class="pf2-container">
		<?php if ( have_posts() ) : ?>
			<div class="pf2-grid pf2-grid--blog">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'pf2-card pf2-card--blog' ); ?>>
						<header class="pf2-card__header">
							<h2 class="pf2-card__title">
								<a href="<?php echo esc_url( get_permalink() ); ?>" class="pf2-card__link">
									<?php echo esc_html( get_the_title() ); ?>
								</a>
							</h2>
						</header>

						<div class="pf2-card__body">
							<?php echo wp_kses_post( get_the_excerpt() ); ?>
						</div>

						<footer class="pf2-card__footer">
							<a class="pf2-button pf2-button--text" href="<?php echo esc_url( get_permalink() ); ?>">
								<?php esc_html_e( 'Read more', 'pf2' ); ?>
							</a>
						</footer>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php the_posts_navigation(); ?>

		<?php else : ?>
			<p class="pf2-empty">
				<?php esc_html_e( 'No content found.', 'pf2' ); ?>
			</p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
