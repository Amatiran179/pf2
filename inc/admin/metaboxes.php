<?php
/**
 * Classic editor metaboxes for PF2 CPTs.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

if ( ! function_exists( 'pf2_admin_generate_product_sku' ) ) {
		/**
		 * Generate the next SKU for PF2 products.
		 *
		 * @param int $post_id Post identifier.
		 * @return string
		 */
		function pf2_admin_generate_product_sku( $post_id ) {
				global $wpdb;

				$post_id  = absint( $post_id );
				$meta_key = 'pf2_sku';

				$query = $wpdb->prepare(
						"SELECT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND p.post_type = %s AND p.post_status NOT IN ('trash', 'auto-draft')",
						$meta_key,
						'pf2_product'
				);

				$values = $wpdb->get_col( $query );
				$max    = 0;

				if ( $values ) {
						foreach ( $values as $value ) {
								if ( preg_match( '/^PF(\d{1,})$/', (string) $value, $matches ) ) {
										$number = (int) $matches[1];
										if ( $number > $max ) {
												$max = $number;
										}
								}
						}
				}

				$candidate = sprintf( 'PF%04d', $max + 1 );

				$collision = $wpdb->get_var(
						$wpdb->prepare(
								"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s AND post_id <> %d LIMIT 1",
								$meta_key,
								$candidate,
								$post_id
						)
				);

				if ( $collision || '' === $candidate ) {
						$candidate = 'PF' . $post_id;
				}

				return sanitize_text_field( $candidate );
		}
}

if ( ! function_exists( 'pf2_admin_add_meta_boxes' ) ) {
		/**
		 * Register custom metaboxes for classic editor support.
		 *
		 * @return void
		 */
		function pf2_admin_add_meta_boxes() {
				add_meta_box(
						'pf2_product_details',
						esc_html__( 'Detail Produk PF2', 'pf2' ),
						'pf2_admin_render_product_metabox',
						'pf2_product',
						'normal',
						'high'
				);

				add_meta_box(
						'pf2_portfolio_details',
						esc_html__( 'Detail Portofolio PF2', 'pf2' ),
						'pf2_admin_render_portfolio_metabox',
						'pf2_portfolio',
						'normal',
						'high'
				);
		}
}
add_action( 'add_meta_boxes', 'pf2_admin_add_meta_boxes' );

if ( ! function_exists( 'pf2_admin_render_product_metabox' ) ) {
		/**
		 * Render product metabox fields.
		 *
		 * @param \WP_Post $post Post object.
		 * @return void
		 */
		function pf2_admin_render_product_metabox( $post ) {
				$defaults = array(
						'pf2_material'    => 'FIBERGLASS Premium Grade A',
						'pf2_model'       => 'Bisa Custom Sesuai Permintaan',
						'pf2_color'       => 'Bisa Custom Sesuai Permintaan',
						'pf2_size'        => 'Bisa Custom Sesuai Permintaan',
						'pf2_currency'    => 'IDR',
						'pf2_price'       => '',
						'pf2_features'    => '',
						'pf2_gallery_ids' => '',
						'pf2_wa'          => '',
						'pf2_sku'         => '',
				);

				$values = array();

				foreach ( $defaults as $key => $default ) {
						$raw_value        = get_post_meta( $post->ID, $key, true );
						$values[ $key ] = '' !== $raw_value && null !== $raw_value ? $raw_value : $default;
				}

				wp_nonce_field( 'pf2_product_meta_nonce', 'pf2_product_meta_nonce' );
				?>
				<table class="form-table pf2-meta-table">
						<tbody>
								<tr>
										<th scope="row"><label for="pf2_sku"><?php esc_html_e( 'SKU Produk', 'pf2' ); ?></label></th>
										<td>
												<input type="text" id="pf2_sku" name="pf2_sku" class="regular-text" value="<?php echo esc_attr( $values['pf2_sku'] ); ?>" />
												<p class="description"><?php esc_html_e( 'Kosongkan untuk mengisi otomatis dengan format PF0001, PF0002, ...', 'pf2' ); ?></p>
										</td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_material"><?php esc_html_e( 'Material', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_material" name="pf2_material" class="regular-text" value="<?php echo esc_attr( $values['pf2_material'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_model"><?php esc_html_e( 'Model', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_model" name="pf2_model" class="regular-text" value="<?php echo esc_attr( $values['pf2_model'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_color"><?php esc_html_e( 'Warna', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_color" name="pf2_color" class="regular-text" value="<?php echo esc_attr( $values['pf2_color'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_size"><?php esc_html_e( 'Ukuran', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_size" name="pf2_size" class="regular-text" value="<?php echo esc_attr( $values['pf2_size'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_price"><?php esc_html_e( 'Harga', 'pf2' ); ?></label></th>
										<td>
												<input type="number" id="pf2_price" name="pf2_price" class="regular-text" step="0.01" value="<?php echo esc_attr( $values['pf2_price'] ); ?>" />
												<p class="description"><?php esc_html_e( 'Kosongkan bila harga tidak ingin ditampilkan.', 'pf2' ); ?></p>
										</td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_currency"><?php esc_html_e( 'Mata Uang', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_currency" name="pf2_currency" class="regular-text" value="<?php echo esc_attr( $values['pf2_currency'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_wa"><?php esc_html_e( 'Nomor WhatsApp Khusus', 'pf2' ); ?></label></th>
										<td>
												<input type="text" id="pf2_wa" name="pf2_wa" class="regular-text" value="<?php echo esc_attr( $values['pf2_wa'] ); ?>" />
												<p class="description"><?php esc_html_e( 'Isi untuk mengganti nomor WhatsApp default dari pengaturan tema.', 'pf2' ); ?></p>
										</td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_features"><?php esc_html_e( 'Fitur / Catatan Singkat', 'pf2' ); ?></label></th>
										<td><textarea id="pf2_features" name="pf2_features" rows="4" class="large-text"><?php echo esc_textarea( $values['pf2_features'] ); ?></textarea></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_gallery_ids"><?php esc_html_e( 'Galeri Gambar (ID attachment, pisahkan dengan koma)', 'pf2' ); ?></label></th>
										<td><textarea id="pf2_gallery_ids" name="pf2_gallery_ids" rows="3" class="large-text code"><?php echo esc_textarea( $values['pf2_gallery_ids'] ); ?></textarea></td>
								</tr>
						</tbody>
				</table>
				<?php
		}
}

if ( ! function_exists( 'pf2_admin_render_portfolio_metabox' ) ) {
		/**
		 * Render portfolio metabox fields.
		 *
		 * @param \WP_Post $post Post object.
		 * @return void
		 */
		function pf2_admin_render_portfolio_metabox( $post ) {
				$defaults = array(
						'pf2_client'       => '',
						'pf2_location'     => '',
						'pf2_product_name' => '',
						'pf2_gallery_ids'  => '',
				);

				$values = array();

				foreach ( $defaults as $key => $default ) {
						$raw_value        = get_post_meta( $post->ID, $key, true );
						$values[ $key ] = '' !== $raw_value && null !== $raw_value ? $raw_value : $default;
				}

				wp_nonce_field( 'pf2_portfolio_meta_nonce', 'pf2_portfolio_meta_nonce' );
				?>
				<table class="form-table pf2-meta-table">
						<tbody>
								<tr>
										<th scope="row"><label for="pf2_client"><?php esc_html_e( 'Nama Klien', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_client" name="pf2_client" class="regular-text" value="<?php echo esc_attr( $values['pf2_client'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_location"><?php esc_html_e( 'Lokasi / URL Google Maps', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_location" name="pf2_location" class="regular-text" value="<?php echo esc_attr( $values['pf2_location'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_product_name"><?php esc_html_e( 'Produk / Layanan', 'pf2' ); ?></label></th>
										<td><input type="text" id="pf2_product_name" name="pf2_product_name" class="regular-text" value="<?php echo esc_attr( $values['pf2_product_name'] ); ?>" /></td>
								</tr>
								<tr>
										<th scope="row"><label for="pf2_gallery_ids"><?php esc_html_e( 'Galeri Gambar (ID attachment, pisahkan dengan koma)', 'pf2' ); ?></label></th>
										<td><textarea id="pf2_gallery_ids" name="pf2_gallery_ids" rows="3" class="large-text code"><?php echo esc_textarea( $values['pf2_gallery_ids'] ); ?></textarea></td>
								</tr>
						</tbody>
				</table>
				<?php
		}
}

if ( ! function_exists( 'pf2_admin_save_product_meta' ) ) {
		/**
		 * Persist product metadata from classic editor submissions.
		 *
		 * @param int     $post_id Post identifier.
		 * @param \WP_Post $post    Post object.
		 * @return void
		 */
		function pf2_admin_save_product_meta( $post_id, $post ) {
				unset( $post );

				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
						return;
				}

				if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
						return;
				}

				if ( ! isset( $_POST['pf2_product_meta_nonce'] ) ) {
						return;
				}

				$nonce = sanitize_text_field( wp_unslash( $_POST['pf2_product_meta_nonce'] ) );

				if ( ! wp_verify_nonce( $nonce, 'pf2_product_meta_nonce' ) ) {
						return;
				}

				if ( ! current_user_can( 'edit_post', $post_id ) ) {
						return;
				}

				$fields = array(
						'pf2_sku'         => 'pf2_meta_sanitize_text',
						'pf2_material'    => 'pf2_meta_sanitize_text',
						'pf2_model'       => 'pf2_meta_sanitize_text',
						'pf2_color'       => 'pf2_meta_sanitize_text',
						'pf2_size'        => 'pf2_meta_sanitize_text',
						'pf2_currency'    => 'pf2_meta_sanitize_text',
						'pf2_wa'          => 'pf2_meta_sanitize_text',
						'pf2_features'    => 'pf2_meta_sanitize_textarea',
						'pf2_gallery_ids' => 'pf2_meta_sanitize_gallery_csv',
				);

				foreach ( $fields as $key => $callback ) {
						$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
						$value = call_user_func( $callback, $value );
						update_post_meta( $post_id, $key, $value );
				}

                                $price_raw   = isset( $_POST['pf2_price'] ) ? wp_unslash( $_POST['pf2_price'] ) : '';
                                $price_value = pf2_meta_sanitize_number( $price_raw );

                                if ( '' === trim( (string) $price_raw ) ) {
                                                delete_post_meta( $post_id, 'pf2_price' );
                                } else {
                                                update_post_meta( $post_id, 'pf2_price', $price_value );
                                }

				$sku = get_post_meta( $post_id, 'pf2_sku', true );

				if ( '' === $sku ) {
						$generated = pf2_admin_generate_product_sku( $post_id );
						update_post_meta( $post_id, 'pf2_sku', $generated );
				}
		}
}
add_action( 'save_post_pf2_product', 'pf2_admin_save_product_meta', 10, 2 );

if ( ! function_exists( 'pf2_admin_save_portfolio_meta' ) ) {
		/**
		 * Persist portfolio metadata from classic editor submissions.
		 *
		 * @param int     $post_id Post identifier.
		 * @param \WP_Post $post    Post object.
		 * @return void
		 */
		function pf2_admin_save_portfolio_meta( $post_id, $post ) {
				unset( $post );

				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
						return;
				}

				if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
						return;
				}

				if ( ! isset( $_POST['pf2_portfolio_meta_nonce'] ) ) {
						return;
				}

				$nonce = sanitize_text_field( wp_unslash( $_POST['pf2_portfolio_meta_nonce'] ) );

				if ( ! wp_verify_nonce( $nonce, 'pf2_portfolio_meta_nonce' ) ) {
						return;
				}

				if ( ! current_user_can( 'edit_post', $post_id ) ) {
						return;
				}

				$fields = array(
						'pf2_client'       => 'pf2_meta_sanitize_text',
						'pf2_location'     => 'pf2_meta_sanitize_text',
						'pf2_product_name' => 'pf2_meta_sanitize_text',
						'pf2_gallery_ids'  => 'pf2_meta_sanitize_gallery_csv',
				);

				foreach ( $fields as $key => $callback ) {
						$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
						$value = call_user_func( $callback, $value );
						update_post_meta( $post_id, $key, $value );
				}
		}
}
add_action( 'save_post_pf2_portfolio', 'pf2_admin_save_portfolio_meta', 10, 2 );
