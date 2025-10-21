<?php
/**
 * Single template for PF2 Portfolio entries.
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

				$post_id      = get_the_ID();
				$client       = get_post_meta( $post_id, 'pf2_client', true );
				$location_raw = get_post_meta( $post_id, 'pf2_location', true );
				$product_name = get_post_meta( $post_id, 'pf2_product_name', true );

				$client       = $client ? $client : '';
				$product_name = $product_name ? $product_name : '';

				$location_display = $location_raw ? $location_raw : '';
				$location_url     = '';

				if ( $location_display ) {
						if ( filter_var( $location_display, FILTER_VALIDATE_URL ) ) {
								$location_url     = $location_display;
								$location_display = wp_parse_url( $location_display, PHP_URL_HOST );
								if ( ! $location_display ) {
										$location_display = $location_url;
								}
						} else {
								$location_url = 'https://maps.google.com/?q=' . rawurlencode( $location_display );
						}
				}

				$wa_number = '';
				if ( function_exists( 'pf2_options_get' ) ) {
						$wa_number = (string) pf2_options_get( 'phone_wa', '' );
				}

				$wa_number_clean = preg_replace( '/[^0-9]/', '', (string) $wa_number );
				$permalink       = get_permalink( $post_id );
				$wa_link         = '';

				if ( $wa_number_clean ) {
						$message = sprintf(
								/* translators: 1: portfolio title, 2: URL */
								esc_html__( 'Halo, saya tertarik layanan seperti: %1$s - %2$s', 'pf2' ),
								wp_strip_all_tags( get_the_title() ),
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
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'pf2-single__article pf2-single__article--portfolio' ); ?>>
						<header class="pf2-single__header">
								<h1 class="pf2-title pf2-title--center"><?php the_title(); ?></h1>
						</header>

						<section class="pf2-single__top pf2-grid-2">
								<div class="pf2-single__media">
										<?php get_template_part( 'template-parts/portfolio/single', 'gallery' ); ?>
								</div>
								<aside class="pf2-single__info" aria-labelledby="pf2-info-title">
										<h2 id="pf2-info-title" class="sr-only"><?php echo esc_html__( 'Informasi', 'pf2' ); ?></h2>
										<table class="pf2-table" aria-describedby="pf2-info-title">
												<tbody>
														<?php if ( $client ) : ?>
																<tr>
																		<th scope="row"><?php echo esc_html__( 'Klien', 'pf2' ); ?></th>
																		<td><?php echo esc_html( $client ); ?></td>
																</tr>
														<?php endif; ?>
														<?php if ( $location_display ) : ?>
																<tr>
																		<th scope="row"><?php echo esc_html__( 'Lokasi', 'pf2' ); ?></th>
																		<td>
																				<?php if ( $location_url ) : ?>
																						<a href="<?php echo esc_url( $location_url ); ?>" target="_blank" rel="noopener"
																								aria-label="<?php echo esc_attr( esc_html__( 'Lihat lokasi di tab baru', 'pf2' ) ); ?>">
																								<?php echo esc_html( $location_display ); ?>
																						</a>
																				<?php else : ?>
																						<?php echo esc_html( $location_display ); ?>
																				<?php endif; ?>
																		</td>
																</tr>
														<?php endif; ?>
														<?php if ( $product_name ) : ?>
																<tr>
																		<th scope="row"><?php echo esc_html__( 'Produk / Layanan', 'pf2' ); ?></th>
																		<td><?php echo esc_html( $product_name ); ?></td>
																</tr>
														<?php endif; ?>
														<tr>
																<th scope="row"><?php echo esc_html__( 'Tanggal Publish', 'pf2' ); ?></th>
																<td><?php echo esc_html( get_the_date() ); ?></td>
														</tr>
												</tbody>
										</table>

										<div class="pf2-cta-wrap">
												<?php if ( $wa_link ) : ?>
														<a class="pf2-btn pf2-btn--wa" href="<?php echo esc_url( $wa_link ); ?>" target="_blank" rel="noopener"
																aria-label="<?php echo esc_attr( sprintf( esc_html__( 'Hubungi WhatsApp untuk %s', 'pf2' ), wp_strip_all_tags( get_the_title() ) ) ); ?>">
																<?php echo esc_html__( 'Diskusi via WhatsApp', 'pf2' ); ?>
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
																aria-label="<?php echo esc_attr( esc_html__( 'Unduh katalog portofolio', 'pf2' ) ); ?>">
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
								'post_type'           => 'pf2_portfolio',
								'posts_per_page'      => 6,
								'post__not_in'        => array( $post_id ),
								'ignore_sticky_posts' => true,
						);

						$related_query = new WP_Query( $related_args );

						if ( $related_query->have_posts() ) :
								?>
								<section class="pf2-single__related" aria-labelledby="pf2-related-portfolio">
										<h2 id="pf2-related-portfolio" class="pf2-h2"><?php echo esc_html__( 'Portofolio lainnya', 'pf2' ); ?></h2>
										<div class="pf2-related-grid">
												<?php
												while ( $related_query->have_posts() ) :
														$related_query->the_post();
														get_template_part( 'inc/templates/parts/card', 'portfolio' );
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
