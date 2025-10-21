<?php
/**
 * Single template for PF2 Portfolio.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main site-main--single-portfolio">
    <?php get_template_part( 'inc/templates/parts/breadcrumbs' ); ?>

    <?php
    while ( have_posts() ) :
        the_post();
        $current_post_id = get_the_ID();
        $author_name     = get_the_author_meta( 'display_name' );
        $publish_date    = get_the_date();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'pf2-single pf2-single--portfolio' ); ?>>
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

            <section class="pf2-single__gallery" aria-label="<?php echo esc_attr__( 'Galeri portofolio', 'pf2' ); ?>">
                <?php get_template_part( 'template-parts/portfolio/single', 'gallery' ); ?>
            </section>
        </article>
        <?php
        $related_args  = array(
            'post_type'           => 'pf2_portfolio',
            'posts_per_page'      => 3,
            'post__not_in'        => array( $current_post_id ),
            'ignore_sticky_posts' => true,
        );
        $related_query = new WP_Query( $related_args );

        if ( $related_query->have_posts() ) :
            ?>
            <section class="pf2-related pf2-related--portfolio" aria-labelledby="pf2-related-portfolios">
                <h2 id="pf2-related-portfolios" class="pf2-related__title">
                    <?php esc_html_e( 'Portofolio lainnya', 'pf2' ); ?>
                </h2>
                <div class="pf2-grid pf2-grid--related">
                    <?php
                    while ( $related_query->have_posts() ) :
                        $related_query->the_post();
                        get_template_part( 'inc/templates/parts/card', 'portfolio' );
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
