<?php
/**
 * Breadcrumb navigation component.
 *
 * @package PF2\Templates\Parts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$breadcrumbs = array(
    array(
        'label' => __( 'Home', 'pf2' ),
        'url'   => home_url( '/' ),
    ),
);

$post_type_labels = array(
    'pf2_product'   => __( 'Produk', 'pf2' ),
    'pf2_portfolio' => __( 'Portofolio', 'pf2' ),
);

if ( is_post_type_archive( 'pf2_product' ) ) {
    $breadcrumbs[] = array(
        'label' => $post_type_labels['pf2_product'],
    );
} elseif ( is_post_type_archive( 'pf2_portfolio' ) ) {
    $breadcrumbs[] = array(
        'label' => $post_type_labels['pf2_portfolio'],
    );
} elseif ( is_tax( 'pf2_product_cat' ) ) {
    $archive_link = get_post_type_archive_link( 'pf2_product' );
    $breadcrumbs[] = array(
        'label' => $post_type_labels['pf2_product'],
        'url'   => $archive_link ? $archive_link : '',
    );

    $term = get_queried_object();
    if ( $term instanceof WP_Term ) {
        $breadcrumbs[] = array(
            'label' => $term->name,
        );
    }
} elseif ( is_singular( 'pf2_product' ) ) {
    $archive_link = get_post_type_archive_link( 'pf2_product' );
    $breadcrumbs[] = array(
        'label' => $post_type_labels['pf2_product'],
        'url'   => $archive_link ? $archive_link : '',
    );

    $terms = get_the_terms( get_the_ID(), 'pf2_product_cat' );
    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        $primary_term = array_shift( $terms );
        if ( $primary_term instanceof WP_Term ) {
            $term_link = get_term_link( $primary_term );
            if ( ! is_wp_error( $term_link ) ) {
                $breadcrumbs[] = array(
                    'label' => $primary_term->name,
                    'url'   => $term_link,
                );
            }
        }
    }

    $single_title = get_the_title( get_the_ID() );
    if ( empty( $single_title ) ) {
        $single_title = __( '(Tanpa judul)', 'pf2' );
    }

    $breadcrumbs[] = array(
        'label' => $single_title,
    );
} elseif ( is_singular( 'pf2_portfolio' ) ) {
    $archive_link = get_post_type_archive_link( 'pf2_portfolio' );
    $breadcrumbs[] = array(
        'label' => $post_type_labels['pf2_portfolio'],
        'url'   => $archive_link ? $archive_link : '',
    );

    $single_title = get_the_title( get_the_ID() );
    if ( empty( $single_title ) ) {
        $single_title = __( '(Tanpa judul)', 'pf2' );
    }

    $breadcrumbs[] = array(
        'label' => $single_title,
    );
}

$breadcrumbs = array_filter(
    $breadcrumbs,
    static function ( $crumb ) {
        return ! empty( $crumb['label'] );
    }
);

if ( count( $breadcrumbs ) < 2 ) {
    return;
}
?>
<nav class="pf2-breadcrumbs" aria-label="<?php echo esc_attr__( 'Breadcrumb', 'pf2' ); ?>">
    <ol class="pf2-breadcrumbs__list">
        <?php foreach ( $breadcrumbs as $index => $crumb ) : ?>
            <?php
            $is_last = ( $index === count( $breadcrumbs ) - 1 );
            $label   = is_string( $crumb['label'] ) ? $crumb['label'] : '';
            $url     = isset( $crumb['url'] ) ? $crumb['url'] : '';
            ?>
            <li class="pf2-breadcrumbs__item"<?php echo $is_last ? ' aria-current="page"' : ''; ?>>
                <?php if ( ! $is_last && ! empty( $url ) ) : ?>
                    <a class="pf2-breadcrumbs__link" href="<?php echo esc_url( $url ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </a>
                <?php else : ?>
                    <span class="pf2-breadcrumbs__current">
                        <?php echo esc_html( $label ); ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
