<?php
/**
 * Portfolio card component.
 *
 * @package PF2\Templates\Parts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title       = get_the_title();
$permalink   = get_permalink();
$card_label  = $title ? $title : __( 'Detail Portofolio', 'pf2' );
$excerpt_raw = get_the_excerpt();
$excerpt     = $excerpt_raw ? wp_trim_words( wp_strip_all_tags( $excerpt_raw ), 24 ) : '';
$aria_label  = sprintf( __( 'Buka portofolio %s', 'pf2' ), $card_label );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'pf2-card pf2-card--portfolio' ); ?>>
    <a class="pf2-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
        <div class="pf2-card__media">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large', array(
                    'class'   => 'pf2-card__image',
                    'loading' => 'lazy',
                    'alt'     => $card_label,
                ) ); ?>
            <?php else : ?>
                <div class="pf2-card__image pf2-card__image--placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>
        <div class="pf2-card__body">
            <h3 class="pf2-card__title"><?php echo esc_html( $card_label ); ?></h3>
            <?php if ( $excerpt ) : ?>
                <p class="pf2-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
            <?php endif; ?>
        </div>
    </a>
</article>
