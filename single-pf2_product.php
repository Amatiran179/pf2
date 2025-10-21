<?php
/**
 * Single template for PF2 Products.
 *
 * @package PF2
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

get_header();
?>
<main id="content" class="pf2-single" role="main">
		<?php get_template_part( 'inc/templates/parts/breadcrumbs' ); ?>

		<?php
		while ( have_posts() ) :
				the_post();

				$post_id  = get_the_ID();
				$defaults = array(
						'pf2_sku'      => '',
						'pf2_material' => 'FIBERGLASS Premium Grade A',
						'pf2_model'    => 'Bisa Custom Sesuai Permintaan',
						'pf2_color'    => 'Bisa Custom Sesuai Permintaan',
						'pf2_size'     => 'Bisa Custom Sesuai Permintaan',
						'pf2_currency' => 'IDR',
						'pf2_wa'       => '',
						'pf2_features' => '',
				);

				$meta = array();
				foreach ( $defaults as $key => $default ) {
						$value        = get_post_meta( $post_id, $key, true );
						$meta[ $key ] = '' !== $value && null !== $value ? $value : $default;
				}

				$price_meta = get_post_meta( $post_id, 'pf2_price', true );
				$has_price  = '' !== $price_meta && null !== $price_meta && is_numeric( $price_meta );
				$price_text = '';

				if ( $has_price ) {
						$price_value = (float) $price_meta;
						$decimals    = ( abs( $price_value - round( $price_value ) ) < 0.01 ) ? 0 : 2;
						$price_text  = number_format_i18n( $price_value, $decimals );
				}

				$sku = $meta['pf2_sku'] ? $meta['pf2_sku'] : 'PF' . $post_id;

				$wa_number = $meta['pf2_wa'];
				if ( '' === $wa_number && function_exists( 'pf2_options_get' ) ) {
						$wa_number = (string) pf2_options_get( 'phone_wa', '' );
				}

				$wa_number_clean = preg_replace( '/[^0-9]/', '', (string) $wa_number );
				$permalink       = get_permalink( $post_id );
				$wa_link         = '';

				if ( $wa_number_clean ) {
						$message = sprintf(
								/* translators: 1: product title, 2: product SKU, 3: URL */
								esc_html__( 'Halo, saya tertarik: %1$s (SKU: %2$s) - %3$s', 'pf2' ),
								wp_strip_all_tags( get_the_title() ),
								$sku,
								$permalink
						);

						$wa_link = sprintf(
								'https://wa.me/%1$s?text=%2$s',
								rawurlencode( $wa_number_clean ),
								rawurlencode( $message )
						);
				}

				$secondary_phone = '';
				if ( function_exists( 'pf2_options_get' ) ) {
						$secondary_phone = (string) pf2_options_get( 'phone_tel', '' );
				}

				if ( '' === $secondary_phone ) {
						$secondary_phone = $wa_number;
				}

				$secondary_phone_clean = preg_replace( '/[^0-9+]/', '', (string) $secondary_phone );
				$catalog_url           = '';
				if ( function_exists( 'pf2_options_get' ) ) {
						$catalog_url = (string) pf2_options_get( 'catalog_url', '' );
				}

				$features_raw  = $meta['pf2_features'];
				$features_list = array();
				if ( is_string( $features_raw ) && '' !== trim( $features_raw ) ) {
						$features_list = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $features_raw ) ) );
				}
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'pf2-single__article pf2-single__article--product' ); ?>>
						<header class="pf2-single__header">
								<h1 class="pf2-title pf2-title--center"><?php the_title(); ?></h1>
						</header>

						<section class="pf2-single__top pf2-grid-2">
								<div class="pf2-single__media">
										<?php get_template_part( 'template-parts/product/single', 'gallery' ); ?>
								</div>
								<aside class="pf2-single__info" aria-labelledby="pf2-info-title">
										<h2 id="pf2-info-title" class="sr-only"><?php echo esc_html__( 'Informasi', 'pf2' ); ?></h2>
										<table class="pf2-table" aria-describedby="pf2-info-title">
												<tbody>
														<tr>
																<th scope="row"><?php echo esc_html__( 'SKU', 'pf2' ); ?></th>
																<td><?php echo esc_html( $sku ); ?></td>
														</tr>
														<tr>
																<th scope="row"><?php echo esc_html__( 'Material', 'pf2' ); ?></th>
																<td><?php echo esc_html( $meta['pf2_material'] ); ?></td>
														</tr>
														<tr>
																<th scope="row"><?php echo esc_html__( 'Model', 'pf2' ); ?></th>
																<td><?php echo esc_html( $meta['pf2_model'] ); ?></td>
														</tr>
														<tr>
																<th scope="row"><?php echo esc_html__( 'Warna', 'pf2' ); ?></th>
																<td><?php echo esc_html( $meta['pf2_color'] ); ?></td>
														</tr>
														<tr>
																<th scope="row"><?php echo esc_html__( 'Ukuran', 'pf2' ); ?></th>
																<td><?php echo esc_html( $meta['pf2_size'] ); ?></td>
														</tr>
														<?php if ( $has_price && $price_text ) : ?>
																<tr>
																		<th scope="row"><?php echo esc_html__( 'Harga', 'pf2' ); ?></th>
																		<td><?php echo esc_html( $meta['pf2_currency'] . ' ' . $price_text ); ?></td>
																</tr>
														<?php endif; ?>
												</tbody>
										</table>

										<?php if ( ! empty( $features_list ) ) : ?>
												<div class="pf2-single__features">
														<h3 class="pf2-single__features-title"><?php echo esc_html__( 'Fitur Unggulan', 'pf2' ); ?></h3>
														<ul>
																<?php foreach ( $features_list as $feature ) : ?>
																		<li><?php echo esc_html( $feature ); ?></li>
																<?php endforeach; ?>
														</ul>
												</div>
										<?php endif; ?>

										<div class="pf2-cta-wrap">
												<?php if ( $wa_link ) : ?>
														<a class="pf2-btn pf2-btn--wa" href="<?php echo esc_url( $wa_link ); ?>" target="_blank" rel="noopener"
																aria-label="<?php echo esc_attr( sprintf( esc_html__( 'Hubungi WhatsApp untuk %s', 'pf2' ), wp_strip_all_tags( get_the_title() ) ) ); ?>">
																<?php echo esc_html__( 'Chat via WhatsApp', 'pf2' ); ?>
														</a>
												<?php endif; ?>

												<?php if ( $secondary_phone_clean ) : ?>
														<a class="pf2-btn pf2-btn--secondary" href="<?php echo esc_url( 'tel:' . $secondary_phone_clean ); ?>"
																aria-label="<?php echo esc_attr( sprintf( esc_html__( 'Telepon PutraFiber untuk %s', 'pf2' ), wp_strip_all_tags( get_the_title() ) ) ); ?>">
																<?php echo esc_html__( 'Telepon Sekarang', 'pf2' ); ?>
														</a>
												<?php endif; ?>

												<?php if ( $catalog_url ) : ?>
														<a class="pf2-btn pf2-btn--secondary" href="<?php echo esc_url( $catalog_url ); ?>" target="_blank" rel="noopener"
																aria-label="<?php echo esc_attr( esc_html__( 'Unduh katalog produk', 'pf2' ) ); ?>">
																<?php echo esc_html__( 'Unduh Katalog', 'pf2' ); ?>
														</a>
												<?php endif; ?>
										</div>
								</aside>
						</section>

						<section class="pf2-single__content">
								<?php the_content(); ?>
						</section>

						<?php
						$related_args = array(
								'post_type'           => 'pf2_product',
								'posts_per_page'      => 6,
								'post__not_in'        => array( $post_id ),
								'ignore_sticky_posts' => true,
						);

						$terms = get_the_terms( $post_id, 'pf2_product_cat' );
						if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
								$term_ids = wp_list_pluck( $terms, 'term_id' );
								if ( ! empty( $term_ids ) ) {
										$related_args['tax_query'] = array(
												array(
														'taxonomy' => 'pf2_product_cat',
														'field'    => 'term_id',
														'terms'    => $term_ids,
												),
										);
								}
						}

						$related_query = new WP_Query( $related_args );

						if ( $related_query->have_posts() ) :
								?>
								<section class="pf2-single__related" aria-labelledby="pf2-related-products">
										<h2 id="pf2-related-products" class="pf2-h2"><?php echo esc_html__( 'Produk lainnya', 'pf2' ); ?></h2>
										<div class="pf2-related-grid">
												<?php
												while ( $related_query->have_posts() ) :
														$related_query->the_post();
														get_template_part( 'inc/templates/parts/card', 'product' );
												endwhile;
												?>
										</div>
								</section>
								<?php
						endif;

						wp_reset_postdata();
						?>
				</article>
		<?php endwhile; ?>
</main>
<?php get_footer(); ?>
