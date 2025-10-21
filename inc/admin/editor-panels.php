<?php
/**
 * Gutenberg document setting panels for PF2 CPTs.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

if ( ! function_exists( 'pf2_admin_enqueue_editor_panels' ) ) {
		/**
		 * Enqueue custom editor panels for supported post types.
		 *
		 * @return void
		 */
		function pf2_admin_enqueue_editor_panels() {
				if ( ! function_exists( 'get_current_screen' ) ) {
						return;
				}

				$screen = get_current_screen();

				if ( ! $screen || ! isset( $screen->post_type ) ) {
						return;
				}

				$supported = array( 'pf2_product', 'pf2_portfolio' );

				if ( ! in_array( $screen->post_type, $supported, true ) ) {
						return;
				}

				$script_path = get_template_directory() . '/assets/js/admin/pf2-editor-panels.js';

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
						'wp-block-editor',
				);

				wp_enqueue_script(
						'pf2-editor-panels',
						get_template_directory_uri() . '/assets/js/admin/pf2-editor-panels.js',
						$dependencies,
						filemtime( $script_path ),
						true
				);

				$product_defaults = array(
						'material' => 'FIBERGLASS Premium Grade A',
						'model'    => 'Bisa Custom Sesuai Permintaan',
						'color'    => 'Bisa Custom Sesuai Permintaan',
						'size'     => 'Bisa Custom Sesuai Permintaan',
						'currency' => 'IDR',
				);

				$options = array(
						'phoneWa'    => '',
						'catalogUrl' => '',
				);

				if ( function_exists( 'pf2_options_get' ) ) {
						$options['phoneWa']    = (string) pf2_options_get( 'phone_wa', '' );
						$options['catalogUrl'] = (string) pf2_options_get( 'catalog_url', '' );
				}

				$config = array(
						'postType' => $screen->post_type,
						'defaults' => array(
								'product'   => $product_defaults,
								'portfolio' => array(),
						),
						'options'  => $options,
						'i18n'     => array(
								'galleryButton' => esc_html__( 'Pilih Gambar Galeri', 'pf2' ),
								'galleryEmpty'  => esc_html__( 'Belum ada gambar yang dipilih.', 'pf2' ),
								'removeImage'   => esc_html__( 'Hapus', 'pf2' ),
								'clearGallery'  => esc_html__( 'Kosongkan galeri', 'pf2' ),
						),
				);

				wp_localize_script( 'pf2-editor-panels', 'pf2EditorPanels', $config );
				wp_set_script_translations( 'pf2-editor-panels', 'pf2' );
		}
}
add_action( 'enqueue_block_editor_assets', 'pf2_admin_enqueue_editor_panels' );
