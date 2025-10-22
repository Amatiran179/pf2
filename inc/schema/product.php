<?php
/**
 * Product schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

if ( ! function_exists( 'pf2_schema_build_product' ) ) {
		/**
		 * Build a Product schema payload.
		 *
		 * @param \WP_Post $post Post context.
		 * @return array
		 */
		function pf2_schema_build_product( $post ) {
				if ( ! $post instanceof \WP_Post ) {
						return array();
				}

				$post_id = (int) $post->ID;

				$sku = pf2_schema_get_meta_text( $post_id, 'pf2_sku' );
				if ( '' === $sku ) {
						$sku = 'PF' . $post_id;
				}

				$material = pf2_schema_get_meta_text( $post_id, 'pf2_material' );
				$model    = pf2_schema_get_meta_text( $post_id, 'pf2_model' );
				$color    = pf2_schema_get_meta_text( $post_id, 'pf2_color' );
				$size     = pf2_schema_get_meta_text( $post_id, 'pf2_size' );

				$price_raw = get_post_meta( $post_id, 'pf2_price', true );
				$price     = ( '' !== $price_raw && null !== $price_raw && is_numeric( $price_raw ) ) ? (string) (float) $price_raw : null;

				$currency = pf2_schema_get_meta_text( $post_id, 'pf2_currency' );
				if ( '' === $currency ) {
						$currency = 'IDR';
				}

				$offers = array();
				if ( null !== $price ) {
						$offers = array(
								'@type'         => 'Offer',
								'priceCurrency' => $currency,
								'price'         => $price,
								'availability'  => 'https://schema.org/InStock',
								'url'           => esc_url_raw( get_permalink( $post ) ),
								'validFrom'     => get_post_time( 'c', true, $post ),
								);
				}

				$brand = 'PutraFiber';

				$features_raw = pf2_schema_get_meta_text( $post_id, 'pf2_features' );
				$description  = wp_strip_all_tags( get_the_excerpt( $post ) );
				if ( '' === $description ) {
						$description = wp_strip_all_tags( get_the_content( null, false, $post ) );
				}

				if ( $features_raw ) {
						$description .= ' ' . wp_strip_all_tags( $features_raw );
				}

				$additional_properties = array();
				$property_map          = array(
						'Material' => $material,
						'Model'    => $model,
						'Warna'    => $color,
						'Ukuran'   => $size,
				);

				foreach ( $property_map as $name => $value ) {
						if ( '' === $value ) {
								continue;
						}

						$additional_properties[] = array(
								'@type' => 'PropertyValue',
								'name'  => $name,
								'value' => $value,
						);
				}

				$data = array(
						'@context'            => 'https://schema.org',
						'@type'               => 'Product',
						'name'                => wp_strip_all_tags( get_the_title( $post ) ),
						'description'         => $description,
						'image'               => pf2_schema_images_from_post( $post_id ),
						'sku'                 => $sku,
						'brand'               => array(
								'@type' => 'Brand',
								'name'  => $brand,
						),
						'material'            => $material,
						'model'               => $model,
						'color'               => $color,
						'size'                => $size,
						'additionalProperty'  => $additional_properties,
						'offers'              => $offers,
				);

				return pf2_schema_array_filter_recursive( $data );
		}
}
