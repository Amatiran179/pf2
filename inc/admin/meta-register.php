<?php
/**
 * Post meta registration for PF2 custom post types.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

if ( ! function_exists( 'pf2_meta_auth_callback' ) ) {
		/**
		 * Gate post meta editing to users with edit permissions.
		 *
		 * @param bool       $allowed  Whether the user is allowed to edit.
		 * @param string     $meta_key Meta key.
		 * @param int        $post_id  Post identifier.
		 * @param int        $user_id  User identifier.
		 * @param string|int $cap      Capability name.
		 * @param array      $caps     Primitive caps.
		 * @return bool
		 */
		function pf2_meta_auth_callback( $allowed, $meta_key, $post_id, $user_id = 0, $cap = '', $caps = array() ) {
				unset( $allowed, $meta_key, $user_id, $cap, $caps );

				return current_user_can( 'edit_post', (int) $post_id );
		}
}

if ( ! function_exists( 'pf2_meta_sanitize_text' ) ) {
		/**
		 * Sanitize a text meta value.
		 *
		 * @param mixed $value Raw value.
		 * @return string
		 */
		function pf2_meta_sanitize_text( $value ) {
				if ( is_scalar( $value ) ) {
						return sanitize_text_field( (string) $value );
				}

				return '';
		}
}

if ( ! function_exists( 'pf2_meta_sanitize_textarea' ) ) {
		/**
		 * Sanitize textarea content while preserving new lines.
		 *
		 * @param mixed $value Raw value.
		 * @return string
		 */
		function pf2_meta_sanitize_textarea( $value ) {
				if ( is_string( $value ) ) {
						return sanitize_textarea_field( $value );
				}

				if ( is_scalar( $value ) ) {
						return sanitize_textarea_field( (string) $value );
				}

				return '';
		}
}

if ( ! function_exists( 'pf2_meta_sanitize_number' ) ) {
                /**
                 * Sanitize a numeric meta value.
                 *
                 * Ensures the stored value respects the registered number type by
                 * returning a float. Empty or invalid input is normalised to null so
                 * consumers can distinguish between an author-provided value and an
                 * unset price.
                 *
                 * @param mixed $value Raw value.
                 * @return float|null
                 */
                function pf2_meta_sanitize_number( $value ) {
                                if ( is_string( $value ) ) {
                                                $value = trim( $value );

                                                if ( '' === $value ) {
                                                                return null;
                                                }

                                                $value = str_replace( ',', '.', $value );
                                }

                                if ( is_numeric( $value ) ) {
                                                return (float) $value;
                                }

                                return null;
                }
}

if ( ! function_exists( 'pf2_meta_sanitize_gallery_csv' ) ) {
		/**
		 * Sanitize gallery attachment IDs stored as CSV.
		 *
		 * @param mixed $value Raw value.
		 * @return string
		 */
		function pf2_meta_sanitize_gallery_csv( $value ) {
				$items = array();

				if ( is_string( $value ) ) {
						$items = preg_split( '/,/', $value );
				} elseif ( is_array( $value ) ) {
						$items = $value;
				}

				if ( ! is_array( $items ) ) {
						return '';
				}

				$ids      = array();
				$registry = array();

				foreach ( $items as $item ) {
						$item = is_scalar( $item ) ? (string) $item : '';
						$id   = absint( trim( $item ) );

						if ( $id && ! isset( $registry[ $id ] ) ) {
								$ids[]              = (string) $id;
								$registry[ $id ] = true;
						}
				}

				return implode( ',', $ids );
		}
}

if ( ! function_exists( 'pf2_meta_register_post_meta' ) ) {
		/**
		 * Register post meta for PF2 custom post types.
		 *
		 * @return void
		 */
		function pf2_meta_register_post_meta() {
				$product_defaults = array(
						'pf2_material' => 'FIBERGLASS Premium Grade A',
						'pf2_model'    => 'Bisa Custom Sesuai Permintaan',
						'pf2_color'    => 'Bisa Custom Sesuai Permintaan',
						'pf2_size'     => 'Bisa Custom Sesuai Permintaan',
						'pf2_currency' => 'IDR',
				);

				$product_meta = array(
						'pf2_sku'          => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => '',
						),
						'pf2_material'     => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => $product_defaults['pf2_material'],
						),
						'pf2_model'        => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => $product_defaults['pf2_model'],
						),
						'pf2_color'        => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => $product_defaults['pf2_color'],
						),
						'pf2_size'         => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => $product_defaults['pf2_size'],
						),
                                                'pf2_price'        => array(
                                                                'type'              => 'number',
                                                                'sanitize_callback' => 'pf2_meta_sanitize_number',
                                                                'default'           => null,
                                                ),
						'pf2_currency'     => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => $product_defaults['pf2_currency'],
						),
						'pf2_wa'           => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => '',
						),
						'pf2_features'     => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_textarea',
								'default'           => '',
						),
						'pf2_gallery_ids'  => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_gallery_csv',
								'default'           => '',
						),
				);

				foreach ( $product_meta as $key => $args ) {
						register_post_meta(
								'pf2_product',
								$key,
								array_merge(
										array(
												'single'        => true,
												'show_in_rest'  => true,
												'auth_callback' => 'pf2_meta_auth_callback',
										),
										$args
								)
						);
				}

				$portfolio_meta = array(
						'pf2_client'       => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => '',
						),
						'pf2_location'     => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => '',
						),
						'pf2_product_name' => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_text',
								'default'           => '',
						),
						'pf2_gallery_ids'  => array(
								'type'              => 'string',
								'sanitize_callback' => 'pf2_meta_sanitize_gallery_csv',
								'default'           => '',
						),
				);

				foreach ( $portfolio_meta as $key => $args ) {
						register_post_meta(
								'pf2_portfolio',
								$key,
								array_merge(
										array(
												'single'        => true,
												'show_in_rest'  => true,
												'auth_callback' => 'pf2_meta_auth_callback',
										),
										$args
								)
						);
				}
		}
}
add_action( 'init', 'pf2_meta_register_post_meta' );
