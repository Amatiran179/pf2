<?php
/**
 * Single template for PF2 Products.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main id="content" class="site-main site-main--single-product" role="main">
    <?php get_template_part( 'inc/templates/parts/breadcrumbs' ); ?>

    <?php
    while ( have_posts() ) :
        the_post();
        $current_post_id = get_the_ID();
        $author_name     = get_the_author_meta( 'display_name' );
        $publish_date    = get_the_date();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'pf2-single pf2-single--product' ); ?>>
            <header class="pf2-single__header">
                <h1 class="pf2-single__title"><?php the_title(); ?></h1>
                <?php if ( $publish_date || $author_name ) : ?>
                    <p class="pf2-single__meta">
                        <?php if ( $publish_date ) : ?>
                            <span class="pf2-single__date"><?php echo esc_html( $publish_date ); ?></span>
                        <?php endif; ?>
                        <?php if ( $author_name ) : ?>
                            <span class="pf2-single__author">
                                <?php
                                printf(
                                    esc_html__( 'oleh %s', 'pf2' ),
                                    esc_html( $author_name )
                                );
                                ?>
                            </span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </header>

            <div class="pf2-single__content">
                <?php the_content(); ?>
            </div>

            <section class="pf2-single__gallery" aria-label="<?php echo esc_attr__( 'Galeri produk', 'pf2' ); ?>">
                <?php get_template_part( 'template-parts/product/single', 'gallery' ); ?>
            </section>
        </article>
        <?php
        $related_args = array(
            'post_type'           => 'pf2_product',
            'posts_per_page'      => 3,
            'post__not_in'        => array( $current_post_id ),
            'ignore_sticky_posts' => true,
        );

        $terms = get_the_terms( $current_post_id, 'pf2_product_cat' );
        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
            $term_ids = wp_list_pluck( $terms, 'term_id' );
            if ( ! empty( $term_ids ) ) {
                $related_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'pf2_product_cat',
                        'field'    => 'term_id',
                        'terms'    => $term_ids,
                    ),
                );
            }
        }

        $related_query = new WP_Query( $related_args );

        if ( $related_query->have_posts() ) :
            ?>
            <section class="pf2-related pf2-related--product" aria-labelledby="pf2-related-products">
                <h2 id="pf2-related-products" class="pf2-related__title">
                    <?php esc_html_e( 'Produk terkait', 'pf2' ); ?>
                </h2>
                <div class="pf2-grid pf2-grid--related">
                    <?php
                    while ( $related_query->have_posts() ) :
                        $related_query->the_post();
                        get_template_part( 'inc/templates/parts/card', 'product' );
                    endwhile;
                    ?>
                </div>
            </section>
            <?php
        endif;

        wp_reset_postdata();
    endwhile;
    ?>
</main>
<?php get_footer(); ?>
