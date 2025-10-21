<?php
/**
 * Archive template for PF2 Portfolio.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$post_type_object     = get_post_type_object( 'pf2_portfolio' );
$archive_description = $post_type_object && ! empty( $post_type_object->description ) ? $post_type_object->description : '';
?>
<main id="primary" class="site-main site-main--archive-portfolio">
    <?php get_template_part( 'inc/templates/parts/breadcrumbs' ); ?>

    <header class="pf2-archive__header">
        <h1 class="pf2-archive__title"><?php echo esc_html( post_type_archive_title( '', false ) ); ?></h1>
        <?php if ( $archive_description ) : ?>
            <p class="pf2-archive__description"><?php echo esc_html( $archive_description ); ?></p>
        <?php endif; ?>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="pf2-grid pf2-grid--archive">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'inc/templates/parts/card', 'portfolio' );
            endwhile;
            ?>
        </div>

        <div class="pf2-archive__pagination">
            <?php
            the_posts_pagination(
                array(
                    'mid_size'           => 2,
                    'prev_text'          => __( 'Sebelumnya', 'pf2' ),
                    'next_text'          => __( 'Selanjutnya', 'pf2' ),
                    'screen_reader_text' => __( 'Navigasi arsip', 'pf2' ),
                )
            );
            ?>
        </div>
    <?php else : ?>
        <p class="pf2-archive__empty"><?php esc_html_e( 'Portofolio belum tersedia saat ini.', 'pf2' ); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
