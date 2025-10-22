<?php
/**
 * BreadcrumbList schema builder.
 *
 * @package PF2\Schema\Global
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_global_breadcrumb' ) ) {
    /**
     * Generate the BreadcrumbList schema object.
     *
     * @param array<string, mixed> $context Current request context.
     * @return array<string, mixed>
     */
    function pf2_schema_global_breadcrumb( $context ) {
        if ( ! pf2_schema_is_enabled( 'breadcrumb', $context ) ) {
            return array();
        }

        $crumbs = array(
            array(
                'label' => __( 'Home', 'pf2' ),
                'url'   => home_url( '/' ),
            ),
        );

        if ( is_post_type_archive( 'pf2_product' ) ) {
            $crumbs[] = array(
                'label' => __( 'Produk', 'pf2' ),
            );
        } elseif ( is_post_type_archive( 'pf2_portfolio' ) ) {
            $crumbs[] = array(
                'label' => __( 'Portofolio', 'pf2' ),
            );
        } elseif ( is_tax( 'pf2_product_cat' ) ) {
            $archive_link = get_post_type_archive_link( 'pf2_product' );
            $crumbs[]     = array(
                'label' => __( 'Produk', 'pf2' ),
                'url'   => $archive_link ? $archive_link : '',
            );

            $term = get_queried_object();
            if ( $term instanceof WP_Term ) {
                $crumbs[] = array(
                    'label' => $term->name,
                );
            }
        } elseif ( is_singular( 'pf2_product' ) ) {
            $archive_link = get_post_type_archive_link( 'pf2_product' );
            $crumbs[]     = array(
                'label' => __( 'Produk', 'pf2' ),
                'url'   => $archive_link ? $archive_link : '',
            );

            $terms = get_the_terms( get_the_ID(), 'pf2_product_cat' );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                $primary_term = array_shift( $terms );
                if ( $primary_term instanceof WP_Term ) {
                    $term_link = get_term_link( $primary_term );
                    if ( ! is_wp_error( $term_link ) ) {
                        $crumbs[] = array(
                            'label' => $primary_term->name,
                            'url'   => $term_link,
                        );
                    }
                }
            }

            $title = get_the_title();
            if ( ! $title ) {
                $title = __( '(Tanpa judul)', 'pf2' );
            }

            $crumbs[] = array(
                'label' => $title,
            );
        } elseif ( is_singular( 'pf2_portfolio' ) ) {
            $archive_link = get_post_type_archive_link( 'pf2_portfolio' );
            $crumbs[]     = array(
                'label' => __( 'Portofolio', 'pf2' ),
                'url'   => $archive_link ? $archive_link : '',
            );

            $title = get_the_title();
            if ( ! $title ) {
                $title = __( '(Tanpa judul)', 'pf2' );
            }

            $crumbs[] = array(
                'label' => $title,
            );
        } elseif ( is_singular() ) {
            $title = get_the_title();
            if ( ! $title ) {
                $title = __( '(Tanpa judul)', 'pf2' );
            }
            $crumbs[] = array(
                'label' => $title,
            );
        }

        $crumbs = array_filter(
            $crumbs,
            static function ( $crumb ) {
                return ! empty( $crumb['label'] );
            }
        );

        if ( count( $crumbs ) < 2 ) {
            return array();
        }

        $position = 1;
        $items    = array();

        foreach ( $crumbs as $crumb ) {
            $item = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $crumb['label'],
            );

            if ( ! empty( $crumb['url'] ) ) {
                $item['item'] = esc_url_raw( $crumb['url'] );
            }

            $items[] = $item;
            $position++;
        }

        $schema = array(
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        );

        return pf2_schema_clean( $schema );
    }
}
