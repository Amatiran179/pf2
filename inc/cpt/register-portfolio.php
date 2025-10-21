<?php
/**
 * Portfolio custom post type registration.
 *
 * @package PF2\CPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_register_portfolio_post_type' ) ) {
    /**
     * Register the Portfolio custom post type.
     *
     * @return void
     */
    function pf2_register_portfolio_post_type() {
        $labels = array(
            'name'                  => _x( 'Portofolio', 'Post type general name', 'pf2' ),
            'singular_name'         => _x( 'Portofolio', 'Post type singular name', 'pf2' ),
            'menu_name'             => _x( 'Portofolio', 'Admin Menu text', 'pf2' ),
            'name_admin_bar'        => _x( 'Portofolio', 'Add New on Toolbar', 'pf2' ),
            'add_new'               => __( 'Tambah Portofolio', 'pf2' ),
            'add_new_item'          => __( 'Tambah Portofolio Baru', 'pf2' ),
            'new_item'              => __( 'Portofolio Baru', 'pf2' ),
            'edit_item'             => __( 'Edit Portofolio', 'pf2' ),
            'view_item'             => __( 'Lihat Portofolio', 'pf2' ),
            'all_items'             => __( 'Semua Portofolio', 'pf2' ),
            'search_items'          => __( 'Cari Portofolio', 'pf2' ),
            'parent_item_colon'     => __( 'Portofolio Induk:', 'pf2' ),
            'not_found'             => __( 'Tidak ada portofolio ditemukan.', 'pf2' ),
            'not_found_in_trash'    => __( 'Tidak ada portofolio di tong sampah.', 'pf2' ),
            'featured_image'        => __( 'Gambar Portofolio', 'pf2' ),
            'set_featured_image'    => __( 'Atur gambar portofolio', 'pf2' ),
            'remove_featured_image' => __( 'Hapus gambar portofolio', 'pf2' ),
            'use_featured_image'    => __( 'Gunakan sebagai gambar portofolio', 'pf2' ),
            'archives'              => __( 'Arsip Portofolio', 'pf2' ),
            'insert_into_item'      => __( 'Masukkan ke portofolio', 'pf2' ),
            'uploaded_to_this_item' => __( 'Unggah ke portofolio ini', 'pf2' ),
            'filter_items_list'     => __( 'Saring daftar portofolio', 'pf2' ),
            'items_list_navigation' => __( 'Navigasi daftar portofolio', 'pf2' ),
            'items_list'            => __( 'Daftar portofolio', 'pf2' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'hierarchical'       => false,
            'show_in_rest'       => true,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'menu_icon'          => 'dashicons-portfolio',
            'has_archive'        => true,
            'rewrite'            => array(
                'slug'       => 'portofolio',
                'with_front' => false,
            ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'menu_position'      => 7,
        );

        register_post_type( 'pf2_portfolio', $args );
    }
}
