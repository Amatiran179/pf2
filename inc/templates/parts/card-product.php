<?php
/**
 * Product card component.
 *
 * @package PF2\Templates\Parts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title           = get_the_title();
$permalink       = get_permalink();
$default_label   = __( 'Detail Produk', 'pf2' );
$card_label      = $title ? wp_strip_all_tags( $title ) : $default_label;
$excerpt_raw = get_the_excerpt();
$excerpt     = $excerpt_raw ? wp_trim_words( wp_strip_all_tags( $excerpt_raw ), 24 ) : '';
$aria_label  = sprintf( __( 'Buka detail %s', 'pf2' ), $card_label );
$image_alt   = wp_strip_all_tags( $card_label ? $card_label : $default_label );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'pf2-card pf2-card--product' ); ?>>
    <a class="pf2-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
        <div class="pf2-card__media">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large', array(
                    'class'   => 'pf2-card__image',
                    'loading' => 'lazy',
                    'alt'     => $image_alt,
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
