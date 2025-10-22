<?php
/**
 * Assets for the PF2 Gallery Gutenberg document panel and classic editor metabox.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_gallery_resolve_post_type' ) ) {
        /**
         * Resolve the current screen post type.
         *
         * @return string
         */
        function pf2_gallery_resolve_post_type() {
                $post_type = '';

                if ( function_exists( 'get_current_screen' ) ) {
                        $screen = get_current_screen();

                        if ( $screen && isset( $screen->post_type ) ) {
                                $post_type = (string) $screen->post_type;
                        }
                }

                if ( $post_type ) {
                        return $post_type;
                }

                if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                        $post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

                        if ( $post_id ) {
                                $post = get_post( $post_id );

                                if ( $post ) {
                                        return (string) $post->post_type;
                                }
                        }
                }

                if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                        return sanitize_key( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                }

                return '';
        }
}

if ( ! function_exists( 'pf2_gallery_enqueue_assets' ) ) {
        /**
         * Enqueue admin assets for gallery management.
         *
         * @return void
         */
        function pf2_gallery_enqueue_assets() {
                static $enqueued = false;

                if ( $enqueued ) {
                        return;
                }

                $post_type = pf2_gallery_resolve_post_type();
                $supported = array( 'pf2_product', 'pf2_portfolio' );

                if ( ! in_array( $post_type, $supported, true ) ) {
                        return;
                }

                $script_path = get_template_directory() . '/assets/js/admin/pf2-gallery-panel.js';

                if ( ! file_exists( $script_path ) ) {
                        return;
                }

                wp_enqueue_media();

                $dependencies = array(
                        'wp-element',
                        'wp-components',
                        'wp-data',
                        'wp-edit-post',
                        'wp-plugins',
                        'wp-i18n',
                        'wp-compose',
                        'wp-core-data',
                        'wp-api-fetch',
                );

                $dependencies = array_values(
                        array_filter(
                                $dependencies,
                                static function( $handle ) {
                                        return 'wp-i18n' === $handle || wp_script_is( $handle, 'registered' );
                                }
                        )
                );

                if ( ! in_array( 'wp-i18n', $dependencies, true ) ) {
                        $dependencies[] = 'wp-i18n';
                }

                wp_enqueue_script(
                        'pf2-gallery-panel',
                        get_template_directory_uri() . '/assets/js/admin/pf2-gallery-panel.js',
                        $dependencies,
                        filemtime( $script_path ),
                        true
                );

                $style_path = get_template_directory() . '/assets/css/admin/pf2-gallery.css';

                if ( file_exists( $style_path ) ) {
                        wp_enqueue_style(
                                'pf2-gallery-admin',
                                get_template_directory_uri() . '/assets/css/admin/pf2-gallery.css',
                                array(),
                                filemtime( $style_path )
                        );
                }

                $config = array(
                        'metaKey'  => 'pf2_gallery_ids',
                        'postType' => $post_type,
                        'labels'   => array(
                                'panelTitle'   => esc_html__( 'PF2 Gallery', 'pf2' ),
                                'addImages'    => esc_html__( 'Upload/Select Images', 'pf2' ),
                                'emptyText'    => esc_html__( 'Belum ada gambar yang dipilih.', 'pf2' ),
                                'removeImage'  => esc_html__( 'Hapus', 'pf2' ),
                                'clearGallery' => esc_html__( 'Kosongkan galeri', 'pf2' ),
                                'dragHint'     => esc_html__( 'Tarik untuk mengurutkan ulang.', 'pf2' ),
                                'modalTitle'   => esc_html__( 'Pilih gambar untuk galeri PF2', 'pf2' ),
                                'noPreview'    => esc_html__( 'Tidak ada pratinjau', 'pf2' ),
                        ),
                );

                wp_localize_script( 'pf2-gallery-panel', 'pf2GalleryPanel', $config );
                wp_set_script_translations( 'pf2-gallery-panel', 'pf2' );

                $enqueued = true;
        }
}

if ( ! function_exists( 'pf2_gallery_enqueue_block_editor_assets' ) ) {
        /**
         * Hook gallery assets into the block editor.
         *
         * @return void
         */
        function pf2_gallery_enqueue_block_editor_assets() {
                pf2_gallery_enqueue_assets();
        }
}
add_action( 'enqueue_block_editor_assets', 'pf2_gallery_enqueue_block_editor_assets' );

if ( ! function_exists( 'pf2_gallery_enqueue_classic_assets' ) ) {
        /**
         * Hook gallery assets into the classic editor screens.
         *
         * @param string $hook Current admin page hook.
         * @return void
         */
        function pf2_gallery_enqueue_classic_assets( $hook ) {
                if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
                        return;
                }

                pf2_gallery_enqueue_assets();
        }
}
add_action( 'admin_enqueue_scripts', 'pf2_gallery_enqueue_classic_assets' );
