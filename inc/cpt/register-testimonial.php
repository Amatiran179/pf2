<?php
/**
 * Testimonial custom post type registration.
 *
 * @package PF2\CPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_register_testimonial_post_type' ) ) {
    /**
     * Register the Testimonial custom post type.
     *
     * @return void
     */
    function pf2_register_testimonial_post_type() {
        $labels = array(
            'name'                  => _x( 'Testimoni', 'Post type general name', 'pf2' ),
            'singular_name'         => _x( 'Testimoni', 'Post type singular name', 'pf2' ),
            'menu_name'             => _x( 'Testimoni', 'Admin Menu text', 'pf2' ),
            'name_admin_bar'        => _x( 'Testimoni', 'Add New on Toolbar', 'pf2' ),
            'add_new'               => __( 'Tambah Testimoni', 'pf2' ),
            'add_new_item'          => __( 'Tambah Testimoni Baru', 'pf2' ),
            'new_item'              => __( 'Testimoni Baru', 'pf2' ),
            'edit_item'             => __( 'Edit Testimoni', 'pf2' ),
            'view_item'             => __( 'Lihat Testimoni', 'pf2' ),
            'all_items'             => __( 'Semua Testimoni', 'pf2' ),
            'search_items'          => __( 'Cari Testimoni', 'pf2' ),
            'parent_item_colon'     => __( 'Testimoni Induk:', 'pf2' ),
            'not_found'             => __( 'Tidak ada testimoni ditemukan.', 'pf2' ),
            'not_found_in_trash'    => __( 'Tidak ada testimoni di tong sampah.', 'pf2' ),
            'featured_image'        => __( 'Foto Testimoni', 'pf2' ),
            'set_featured_image'    => __( 'Atur foto testimoni', 'pf2' ),
            'remove_featured_image' => __( 'Hapus foto testimoni', 'pf2' ),
            'use_featured_image'    => __( 'Gunakan sebagai foto testimoni', 'pf2' ),
            'archives'              => __( 'Arsip Testimoni', 'pf2' ),
            'insert_into_item'      => __( 'Masukkan ke testimoni', 'pf2' ),
            'uploaded_to_this_item' => __( 'Unggah ke testimoni ini', 'pf2' ),
            'filter_items_list'     => __( 'Saring daftar testimoni', 'pf2' ),
            'items_list_navigation' => __( 'Navigasi daftar testimoni', 'pf2' ),
            'items_list'            => __( 'Daftar testimoni', 'pf2' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'hierarchical'       => false,
            'show_in_rest'       => true,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'menu_icon'          => 'dashicons-format-quote',
            'has_archive'        => true,
            'rewrite'            => array(
                'slug'       => 'testimoni',
                'with_front' => false,
            ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'menu_position'      => 10,
        );

        register_post_type( 'pf2_testimonial', $args );
    }
}
