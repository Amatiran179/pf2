<?php
/**
 * Tourist attraction schema builder.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

if ( ! function_exists( 'pf2_schema_build_tourist_attraction' ) ) {
		/**
		 * Build a TouristAttraction schema payload.
		 *
		 * @param \WP_Post $post Post context.
		 * @return array
		 */
		function pf2_schema_build_tourist_attraction( $post ) {
				if ( ! $post instanceof \WP_Post ) {
						return array();
				}

				$post_id = (int) $post->ID;

				$product_name = pf2_schema_get_meta_text( $post_id, 'pf2_product_name' );
				$client       = pf2_schema_get_meta_text( $post_id, 'pf2_client' );
				$location_raw = pf2_schema_get_meta_text( $post_id, 'pf2_location' );

				$location = array();
				if ( $location_raw ) {
						$location = array(
								'@type' => 'Place',
								'name'  => $location_raw,
						);

						if ( filter_var( $location_raw, FILTER_VALIDATE_URL ) ) {
								$location['url'] = esc_url_raw( $location_raw );
						} else {
								$location['url'] = 'https://maps.google.com/?q=' . rawurlencode( $location_raw );
						}
				}

				$about = array();
				if ( $product_name ) {
						$about = array(
								'@type' => 'Product',
								'name'  => $product_name,
						);
				}

				$description = wp_strip_all_tags( get_the_excerpt( $post ) );
				if ( '' === $description ) {
						$description = wp_strip_all_tags( get_the_content( null, false, $post ) );
				}

				$data = array(
						'@context'      => 'https://schema.org',
						'@type'         => 'CreativeWork',
						'name'          => wp_strip_all_tags( get_the_title( $post ) ),
						'description'   => $description,
						'image'         => pf2_schema_images_from_post( $post_id ),
						'url'           => esc_url_raw( get_permalink( $post ) ),
						'about'         => $about,
						'location'      => $location,
						'datePublished' => get_post_time( 'c', true, $post ),
						'creator'       => $client ? array(
								'@type' => 'Organization',
								'name'  => $client,
						) : array(),
				);

				return pf2_schema_array_filter_recursive( $data );
		}
}
