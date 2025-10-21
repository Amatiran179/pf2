<?php
/**
 * Product custom post type registration.
 *
 * @package PF2\CPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_register_product_post_type' ) ) {
    /**
     * Register the Product custom post type and taxonomy.
     *
     * @return void
     */
    function pf2_register_product_post_type() {
        $labels = array(
            'name'                  => _x( 'Produk', 'Post type general name', 'pf2' ),
            'singular_name'         => _x( 'Produk', 'Post type singular name', 'pf2' ),
            'menu_name'             => _x( 'Produk', 'Admin Menu text', 'pf2' ),
            'name_admin_bar'        => _x( 'Produk', 'Add New on Toolbar', 'pf2' ),
            'add_new'               => __( 'Tambah Produk', 'pf2' ),
            'add_new_item'          => __( 'Tambah Produk Baru', 'pf2' ),
            'new_item'              => __( 'Produk Baru', 'pf2' ),
            'edit_item'             => __( 'Edit Produk', 'pf2' ),
            'view_item'             => __( 'Lihat Produk', 'pf2' ),
            'all_items'             => __( 'Semua Produk', 'pf2' ),
            'search_items'          => __( 'Cari Produk', 'pf2' ),
            'parent_item_colon'     => __( 'Produk Induk:', 'pf2' ),
            'not_found'             => __( 'Tidak ada produk ditemukan.', 'pf2' ),
            'not_found_in_trash'    => __( 'Tidak ada produk di tong sampah.', 'pf2' ),
            'featured_image'        => __( 'Gambar Produk', 'pf2' ),
            'set_featured_image'    => __( 'Atur gambar produk', 'pf2' ),
            'remove_featured_image' => __( 'Hapus gambar produk', 'pf2' ),
            'use_featured_image'    => __( 'Gunakan sebagai gambar produk', 'pf2' ),
            'archives'              => __( 'Arsip Produk', 'pf2' ),
            'insert_into_item'      => __( 'Masukkan ke produk', 'pf2' ),
            'uploaded_to_this_item' => __( 'Unggah ke produk ini', 'pf2' ),
            'filter_items_list'     => __( 'Saring daftar produk', 'pf2' ),
            'items_list_navigation' => __( 'Navigasi daftar produk', 'pf2' ),
            'items_list'            => __( 'Daftar produk', 'pf2' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'hierarchical'       => false,
            'show_in_rest'       => true,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'menu_icon'          => 'dashicons-cart',
            'has_archive'        => true,
            'rewrite'            => array(
                'slug'       => 'produk',
                'with_front' => false,
            ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'menu_position'      => 6,
        );

        register_post_type( 'pf2_product', $args );

        $taxonomy_labels = array(
            'name'              => _x( 'Kategori Produk', 'taxonomy general name', 'pf2' ),
            'singular_name'     => _x( 'Kategori Produk', 'taxonomy singular name', 'pf2' ),
            'search_items'      => __( 'Cari Kategori Produk', 'pf2' ),
            'all_items'         => __( 'Semua Kategori Produk', 'pf2' ),
            'parent_item'       => __( 'Kategori Produk Induk', 'pf2' ),
            'parent_item_colon' => __( 'Kategori Produk Induk:', 'pf2' ),
            'edit_item'         => __( 'Edit Kategori Produk', 'pf2' ),
            'update_item'       => __( 'Perbarui Kategori Produk', 'pf2' ),
            'add_new_item'      => __( 'Tambah Kategori Produk Baru', 'pf2' ),
            'new_item_name'     => __( 'Nama Kategori Produk Baru', 'pf2' ),
            'menu_name'         => __( 'Kategori Produk', 'pf2' ),
        );

        $taxonomy_args = array(
            'hierarchical'      => true,
            'labels'            => $taxonomy_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array(
                'slug'       => 'kategori-produk',
                'with_front' => false,
            ),
            'show_in_rest'      => true,
        );

        register_taxonomy( 'pf2_product_cat', array( 'pf2_product' ), $taxonomy_args );
    }
}
