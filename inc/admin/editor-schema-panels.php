<?php
/**
 * Gutenberg schema extras panels.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_admin_enqueue_schema_panels' ) ) {
    /**
     * Enqueue the schema extras panel script for supported post types.
     *
     * @return void
     */
    function pf2_admin_enqueue_schema_panels() {
        if ( ! function_exists( 'get_current_screen' ) ) {
            return;
        }

        $screen = get_current_screen();

        if ( ! $screen || ! isset( $screen->post_type ) ) {
            return;
        }

        $supported = array( 'post', 'page', 'pf2_product', 'pf2_portfolio' );

        if ( ! in_array( $screen->post_type, $supported, true ) ) {
            return;
        }

        $script_path = get_template_directory() . '/assets/js/admin/pf2-schema-panels.js';

        if ( ! file_exists( $script_path ) ) {
            return;
        }

        $deps = array(
            'wp-element',
            'wp-components',
            'wp-data',
            'wp-core-data',
            'wp-edit-post',
            'wp-plugins',
            'wp-i18n',
            'wp-compose',
            'wp-block-editor',
        );

        wp_enqueue_script(
            'pf2-schema-panels',
            get_template_directory_uri() . '/assets/js/admin/pf2-schema-panels.js',
            $deps,
            filemtime( $script_path ),
            true
        );

        $config = array(
            'postType' => $screen->post_type,
            'i18n'     => array(
                'panelTitle'        => esc_html__( 'PF2 Schema', 'pf2' ),
                'tabFaq'            => esc_html__( 'FAQ', 'pf2' ),
                'tabHowTo'          => esc_html__( 'HowTo', 'pf2' ),
                'tabVideo'          => esc_html__( 'Video', 'pf2' ),
                'tabServiceArea'    => esc_html__( 'Service Area', 'pf2' ),
                'tabTourist'        => esc_html__( 'Tourist Attraction', 'pf2' ),
                'addItem'           => esc_html__( 'Tambah', 'pf2' ),
                'removeItem'        => esc_html__( 'Hapus', 'pf2' ),
                'emptyFaq'          => esc_html__( 'Belum ada FAQ.', 'pf2' ),
                'emptySteps'        => esc_html__( 'Belum ada langkah HowTo.', 'pf2' ),
                'serviceAreaValues' => esc_html__( 'Daftar area (pisahkan baris).', 'pf2' ),
                'selectImage'       => esc_html__( 'Pilih gambar', 'pf2' ),
                'replaceImage'      => esc_html__( 'Ganti gambar', 'pf2' ),
                'clearImage'        => esc_html__( 'Hapus gambar', 'pf2' ),
                'noImage'           => esc_html__( 'Belum ada gambar.', 'pf2' ),
                'selectThumbnail'   => esc_html__( 'Pilih thumbnail', 'pf2' ),
                'replaceThumbnail'  => esc_html__( 'Ganti thumbnail', 'pf2' ),
                'clearThumbnail'    => esc_html__( 'Hapus thumbnail', 'pf2' ),
            ),
            'serviceAreaTypes' => array(
                array(
                    'value' => '',
                    'label' => esc_html__( 'Pilih tipe area', 'pf2' ),
                ),
                array(
                    'value' => 'City',
                    'label' => esc_html__( 'Kota', 'pf2' ),
                ),
                array(
                    'value' => 'Country',
                    'label' => esc_html__( 'Negara', 'pf2' ),
                ),
                array(
                    'value' => 'Region',
                    'label' => esc_html__( 'Provinsi/Region', 'pf2' ),
                ),
                array(
                    'value' => 'PostalAddress',
                    'label' => esc_html__( 'Alamat lengkap', 'pf2' ),
                ),
                array(
                    'value' => 'GeoShape',
                    'label' => esc_html__( 'GeoShape', 'pf2' ),
                ),
            ),
        );

        wp_localize_script( 'pf2-schema-panels', 'pf2SchemaPanels', $config );
        wp_set_script_translations( 'pf2-schema-panels', 'pf2' );
    }
}

add_action( 'enqueue_block_editor_assets', 'pf2_admin_enqueue_schema_panels' );
