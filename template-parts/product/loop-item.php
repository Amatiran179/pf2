<?php
/**
 * Product loop item template.
 *
 * @package PF2\TemplateParts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'pf2-card pf2-card--product' ); ?>>
	<a class="pf2-card__link" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
		<figure class="pf2-card__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'pf2-card__image', 'alt' => esc_attr( get_the_title() ) ) ); ?>
			<?php else : ?>
				<div class="pf2-card__placeholder">
					<span class="pf2-card__placeholder-text"><?php esc_html_e( 'Preview coming soon', 'pf2' ); ?></span>
				</div>
			<?php endif; ?>
		</figure>
		<header class="pf2-card__header">
			<h3 class="pf2-card__title"><?php echo esc_html( get_the_title() ); ?></h3>
		</header>
	</a>
</article>
