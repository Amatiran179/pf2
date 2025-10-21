<?php
/**
 * Service custom post type registration.
 *
 * @package PF2\CPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_register_service_post_type' ) ) {
    /**
     * Register the Service custom post type.
     *
     * @return void
     */
    function pf2_register_service_post_type() {
        $labels = array(
            'name'                  => _x( 'Layanan', 'Post type general name', 'pf2' ),
            'singular_name'         => _x( 'Layanan', 'Post type singular name', 'pf2' ),
            'menu_name'             => _x( 'Layanan', 'Admin Menu text', 'pf2' ),
            'name_admin_bar'        => _x( 'Layanan', 'Add New on Toolbar', 'pf2' ),
            'add_new'               => __( 'Tambah Layanan', 'pf2' ),
            'add_new_item'          => __( 'Tambah Layanan Baru', 'pf2' ),
            'new_item'              => __( 'Layanan Baru', 'pf2' ),
            'edit_item'             => __( 'Edit Layanan', 'pf2' ),
            'view_item'             => __( 'Lihat Layanan', 'pf2' ),
            'all_items'             => __( 'Semua Layanan', 'pf2' ),
            'search_items'          => __( 'Cari Layanan', 'pf2' ),
            'parent_item_colon'     => __( 'Layanan Induk:', 'pf2' ),
            'not_found'             => __( 'Tidak ada layanan ditemukan.', 'pf2' ),
            'not_found_in_trash'    => __( 'Tidak ada layanan di tong sampah.', 'pf2' ),
            'featured_image'        => __( 'Gambar Layanan', 'pf2' ),
            'set_featured_image'    => __( 'Atur gambar layanan', 'pf2' ),
            'remove_featured_image' => __( 'Hapus gambar layanan', 'pf2' ),
            'use_featured_image'    => __( 'Gunakan sebagai gambar layanan', 'pf2' ),
            'archives'              => __( 'Arsip Layanan', 'pf2' ),
            'insert_into_item'      => __( 'Masukkan ke layanan', 'pf2' ),
            'uploaded_to_this_item' => __( 'Unggah ke layanan ini', 'pf2' ),
            'filter_items_list'     => __( 'Saring daftar layanan', 'pf2' ),
            'items_list_navigation' => __( 'Navigasi daftar layanan', 'pf2' ),
            'items_list'            => __( 'Daftar layanan', 'pf2' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'hierarchical'       => false,
            'show_in_rest'       => true,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'menu_icon'          => 'dashicons-hammer',
            'has_archive'        => true,
            'rewrite'            => array(
                'slug'       => 'layanan',
                'with_front' => false,
            ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'menu_position'      => 8,
        );

        register_post_type( 'pf2_service', $args );
    }
}
