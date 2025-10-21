<?php
/**
 * Schema engine dispatcher and helpers.
 *
 * Provides detection, builder dispatching, and rendering for JSON-LD output.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_schema_array_filter_recursive' ) ) {
        /**
         * Recursively filter out empty values from an array.
         *
         * @param mixed $data Array of schema data.
         * @return array
         */
        function pf2_schema_array_filter_recursive( $data ) {
                if ( ! is_array( $data ) ) {
                        return array();
                }

                foreach ( $data as $key => $value ) {
                        if ( is_array( $value ) ) {
                                $data[ $key ] = pf2_schema_array_filter_recursive( $value );

                                if ( empty( $data[ $key ] ) ) {
                                        unset( $data[ $key ] );
                                }
                        } elseif ( null === $value || '' === $value ) {
                                unset( $data[ $key ] );
                        }
                }

                return $data;
        }
}

if ( ! function_exists( 'pf2_schema_normalize_type' ) ) {
        /**
         * Normalize schema slug identifiers to a predictable form.
         *
         * @param string $type Requested schema type.
         * @return string
         */
        function pf2_schema_normalize_type( $type ) {
                if ( ! is_string( $type ) || '' === $type ) {
                        return '';
                }

                $normalized = strtolower( $type );
                $normalized = str_replace( '-', '_', $normalized );

                return preg_replace( '/[^a-z0-9_]/', '', $normalized );
        }
}

if ( ! function_exists( 'pf2_schema_get_meta_text' ) ) {
        /**
         * Safely fetch sanitized text metadata for a post.
         *
         * @param int    $post_id Post identifier.
         * @param string $key     Meta key to fetch.
         * @return string
         */
        function pf2_schema_get_meta_text( $post_id, $key ) {
                $value = get_post_meta( $post_id, $key, true );

                if ( is_string( $value ) ) {
                        return sanitize_text_field( $value );
                }

                return '';
        }
}

if ( ! function_exists( 'pf2_schema_build_postal_address' ) ) {
        /**
         * Construct a PostalAddress schema fragment from known metadata.
         *
         * @param int $post_id Post identifier.
         * @return array
         */
        function pf2_schema_build_postal_address( $post_id ) {
                $address = array(
                        '@type'           => 'PostalAddress',
                        'streetAddress'   => pf2_schema_get_meta_text( $post_id, 'pf2_address_street' ),
                        'addressLocality' => pf2_schema_get_meta_text( $post_id, 'pf2_address_city' ),
                        'addressRegion'   => pf2_schema_get_meta_text( $post_id, 'pf2_address_region' ),
                        'postalCode'      => pf2_schema_get_meta_text( $post_id, 'pf2_address_postal_code' ),
                        'addressCountry'  => pf2_schema_get_meta_text( $post_id, 'pf2_address_country' ),
                );

                $address = pf2_schema_array_filter_recursive( $address );

                if ( empty( $address ) ) {
                        return array();
                }

                return $address;
        }
}

if ( ! function_exists( 'pf2_schema_images_from_post' ) ) {
        /**
         * Gather image URLs suitable for schema image arrays.
         *
         * @param int $post_id Post identifier.
         * @return array
         */
        function pf2_schema_images_from_post( $post_id ) {
                $images = array();

                $featured = get_post_thumbnail_id( $post_id );
                if ( $featured ) {
                        $featured_url = wp_get_attachment_image_url( $featured, 'full' );
                        if ( $featured_url ) {
                                $images[] = esc_url_raw( $featured_url );
                        }
                }

                $attachments = get_attached_media( 'image', $post_id );
                if ( ! empty( $attachments ) ) {
                        foreach ( $attachments as $attachment ) {
                                if ( ! isset( $attachment->ID ) ) {
                                        continue;
                                }

                                $url = wp_get_attachment_image_url( (int) $attachment->ID, 'full' );
                                if ( $url ) {
                                        $images[] = esc_url_raw( $url );
                                }
                        }
                }

                $images = array_values( array_unique( array_filter( $images ) ) );

                return $images;
        }
}

require_once get_template_directory() . '/inc/schema/product.php';
require_once get_template_directory() . '/inc/schema/article.php';
require_once get_template_directory() . '/inc/schema/faq.php';
require_once get_template_directory() . '/inc/schema/howto.php';
require_once get_template_directory() . '/inc/schema/service-area.php';
require_once get_template_directory() . '/inc/schema/tourist-attraction.php';
require_once get_template_directory() . '/inc/schema/organization.php';
require_once get_template_directory() . '/inc/schema/local-business.php';

add_filter(
	'pf2_schema_enabled',
	function ( $enabled ) {
		if ( function_exists( 'pf2_options_get' ) ) {
			$option = (int) pf2_options_get( 'schema_enabled', 1 );
			if ( ! $option ) {
				return false;
			}
		}

		return (bool) $enabled;
	}
);

if ( ! function_exists( 'pf2_schema_is_enabled' ) ) {
        /**
         * Determine whether the schema engine should run.
         *
         * Defaults to disabled when popular SEO suites are active to avoid
         * duplicate output, but can be forced on via the `pf2_schema_enabled`
         * filter.
         *
         * @return bool
         */
        function pf2_schema_is_enabled() {
                $default = true;

                if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) {
                        $default = false;
                }

                /**
                 * Filter the schema enablement flag.
                 *
                 * @param bool $enabled Whether schema output should run.
                 */
                return (bool) apply_filters( 'pf2_schema_enabled', $default );
        }
}

if ( ! function_exists( 'pf2_schema_detect_type' ) ) {
        /**
         * Determine the most appropriate schema type for a post object.
         *
         * @param \WP_Post $post Post object to inspect.
         * @return string|null
         */
        function pf2_schema_detect_type( $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return null;
                }

                $post_id = (int) $post->ID;

                $disable = get_post_meta( $post_id, 'pf2_schema_disable', true );
                if ( $disable ) {
                        return null;
                }

                $manual_type = get_post_meta( $post_id, 'pf2_schema_type', true );
                if ( $manual_type ) {
                        $manual_type = pf2_schema_normalize_type( $manual_type );
                        if ( $manual_type ) {
                                /**
                                 * Filter the detected schema type.
                                 *
                                 * @param string|null $type Detected type string.
                                 * @param \WP_Post    $post Post being inspected.
                                 */
                                return apply_filters( 'pf2_schema_type', $manual_type, $post );
                        }
                }

                $detected = '';

                switch ( $post->post_type ) {
                        case 'pf2_product':
                                $detected = 'product';
                                break;
                        case 'pf2_portfolio':
                                $detected = 'tourist_attraction';
                                break;
                        case 'post':
                                $detected = 'article';
                                break;
                        case 'pf2_service':
                                $detected = 'service_area';
                                break;
                        case 'pf2_business':
                                $detected = 'local_business';
                                break;
                        default:
                                $detected = '';
                                break;
                }

                if ( ! $detected ) {
                        $has_faq_block   = function_exists( 'has_block' ) && has_block( 'pf2/faq', $post );
                        $has_howto_block = function_exists( 'has_block' ) && has_block( 'pf2/how-to', $post );

                        $has_faq_shortcode   = function_exists( 'has_shortcode' ) && has_shortcode( $post->post_content, 'pf2_faq' );
                        $has_howto_shortcode = function_exists( 'has_shortcode' ) && has_shortcode( $post->post_content, 'pf2_howto' );

                        if ( $has_faq_block || $has_faq_shortcode ) {
                                $detected = 'faq';
                        } elseif ( $has_howto_block || $has_howto_shortcode ) {
                                $detected = 'howto';
                        }
                }

                $detected = pf2_schema_normalize_type( $detected );

                if ( ! $detected ) {
                        return null;
                }

                /**
                 * Filter the detected schema type.
                 *
                 * @param string|null $type Detected type string.
                 * @param \WP_Post    $post Post being inspected.
                 */
                return apply_filters( 'pf2_schema_type', $detected, $post );
        }
}

if ( ! function_exists( 'pf2_schema_build' ) ) {
        /**
         * Build JSON-LD data array for the provided schema type.
         *
         * @param string   $type Schema type slug.
         * @param \WP_Post $post Post context.
         * @return array
         */
        function pf2_schema_build( $type, $post ) {
                if ( ! $post instanceof \WP_Post ) {
                        return array();
                }

                $type   = pf2_schema_normalize_type( $type );
                $schema = array();

                switch ( $type ) {
                        case 'product':
                                $schema = pf2_schema_build_product( $post );
                                break;
                        case 'article':
                                $schema = pf2_schema_build_article( $post );
                                break;
                        case 'faq':
                                $schema = pf2_schema_build_faq( $post );
                                break;
                        case 'howto':
                                $schema = pf2_schema_build_howto( $post );
                                break;
                        case 'service_area':
                                $schema = pf2_schema_build_service_area( $post );
                                break;
                        case 'tourist_attraction':
                                $schema = pf2_schema_build_tourist_attraction( $post );
                                break;
                        case 'organization':
                                $schema = pf2_schema_build_organization( $post );
                                break;
                        case 'local_business':
                                $schema = pf2_schema_build_local_business( $post );
                                break;
                        default:
                                $schema = array();
                                break;
                }

                if ( ! is_array( $schema ) ) {
                        $schema = array();
                }

                $schema = pf2_schema_array_filter_recursive( $schema );

                /**
                 * Filter the schema payload before rendering.
                 *
                 * @param array    $schema Schema payload.
                 * @param string   $type   Schema type slug.
                 * @param \WP_Post $post   Post context.
                 */
                $schema = apply_filters( 'pf2_schema_data', $schema, $type, $post );

                if ( ! is_array( $schema ) ) {
                        return array();
                }

                return pf2_schema_array_filter_recursive( $schema );
        }
}

if ( ! function_exists( 'pf2_schema_render_jsonld' ) ) {
        /**
         * Convert schema data to JSON-LD string.
         *
         * @param array $data Schema payload.
         * @return string
         */
        function pf2_schema_render_jsonld( $data ) {
                if ( empty( $data ) || ! is_array( $data ) ) {
                        return '';
                }

                return wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        }
}

if ( ! function_exists( 'pf2_schema_output' ) ) {
        /**
         * Hooked output handler for rendering schema JSON-LD in the document head.
         *
         * @return void
         */
        function pf2_schema_output() {
                static $rendered = false;

                if ( $rendered ) {
                        return;
                }

                if ( is_admin() || ! is_singular() ) {
                        return;
                }

                if ( ! pf2_schema_is_enabled() ) {
                        return;
                }

                $post = get_post();
                if ( ! $post instanceof \WP_Post ) {
                        return;
                }

                $type = pf2_schema_detect_type( $post );
                if ( ! $type ) {
                        return;
                }

                $schema = pf2_schema_build( $type, $post );
                if ( empty( $schema ) ) {
                        return;
                }

                $json = pf2_schema_render_jsonld( $schema );
                if ( ! $json ) {
                        return;
                }

                $rendered = true;

                echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                /**
                 * Fires after schema JSON-LD has been rendered.
                 *
                 * @param string   $type Schema type slug.
                 * @param \WP_Post $post Post context.
                 */
                do_action( 'pf2_schema_rendered', $type, $post );
        }
}

add_action( 'wp_head', 'pf2_schema_output', 90 );
