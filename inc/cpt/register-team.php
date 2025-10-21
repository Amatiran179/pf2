<?php
/**
 * Team custom post type registration.
 *
 * @package PF2\CPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_register_team_post_type' ) ) {
    /**
     * Register the Team custom post type.
     *
     * @return void
     */
    function pf2_register_team_post_type() {
        $labels = array(
            'name'                  => _x( 'Tim', 'Post type general name', 'pf2' ),
            'singular_name'         => _x( 'Tim', 'Post type singular name', 'pf2' ),
            'menu_name'             => _x( 'Tim', 'Admin Menu text', 'pf2' ),
            'name_admin_bar'        => _x( 'Tim', 'Add New on Toolbar', 'pf2' ),
            'add_new'               => __( 'Tambah Anggota', 'pf2' ),
            'add_new_item'          => __( 'Tambah Anggota Baru', 'pf2' ),
            'new_item'              => __( 'Anggota Baru', 'pf2' ),
            'edit_item'             => __( 'Edit Anggota Tim', 'pf2' ),
            'view_item'             => __( 'Lihat Anggota Tim', 'pf2' ),
            'all_items'             => __( 'Semua Anggota Tim', 'pf2' ),
            'search_items'          => __( 'Cari Anggota Tim', 'pf2' ),
            'parent_item_colon'     => __( 'Anggota Tim Induk:', 'pf2' ),
            'not_found'             => __( 'Tidak ada anggota tim ditemukan.', 'pf2' ),
            'not_found_in_trash'    => __( 'Tidak ada anggota tim di tong sampah.', 'pf2' ),
            'featured_image'        => __( 'Foto Anggota', 'pf2' ),
            'set_featured_image'    => __( 'Atur foto anggota', 'pf2' ),
            'remove_featured_image' => __( 'Hapus foto anggota', 'pf2' ),
            'use_featured_image'    => __( 'Gunakan sebagai foto anggota', 'pf2' ),
            'archives'              => __( 'Arsip Tim', 'pf2' ),
            'insert_into_item'      => __( 'Masukkan ke anggota tim', 'pf2' ),
            'uploaded_to_this_item' => __( 'Unggah ke anggota tim ini', 'pf2' ),
            'filter_items_list'     => __( 'Saring daftar anggota tim', 'pf2' ),
            'items_list_navigation' => __( 'Navigasi daftar anggota tim', 'pf2' ),
            'items_list'            => __( 'Daftar anggota tim', 'pf2' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'hierarchical'       => false,
            'show_in_rest'       => true,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'menu_icon'          => 'dashicons-groups',
            'has_archive'        => true,
            'rewrite'            => array(
                'slug'       => 'tim',
                'with_front' => false,
            ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'menu_position'      => 9,
        );

        register_post_type( 'pf2_team', $args );
    }
}
