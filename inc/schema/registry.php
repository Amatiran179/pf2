<?php
/**
 * Schema registry helpers.
 *
 * Provides sanitation, merge utilities, and context helpers shared across
 * schema builders.
 *
 * @package PF2\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_clean' ) ) {
    /**
     * Recursively remove empty values from a schema array.
     *
     * @param mixed $data Raw schema data.
     * @return array<mixed>
     */
    function pf2_schema_clean( $data ) {
        if ( ! is_array( $data ) ) {
            return array();
        }

        $cleaned = array();

        foreach ( $data as $key => $value ) {
            if ( is_array( $value ) ) {
                $value = pf2_schema_clean( $value );
                if ( empty( $value ) ) {
                    continue;
                }
            } elseif ( is_string( $value ) ) {
                $value = trim( $value );
                if ( '' === $value ) {
                    continue;
                }
            } elseif ( null === $value ) {
                continue;
            }

            $cleaned[ $key ] = $value;
        }

        if ( empty( $cleaned ) ) {
            return array();
        }

        $is_sequential = array_keys( $cleaned ) === range( 0, count( $cleaned ) - 1 );
        if ( $is_sequential ) {
            $cleaned = array_values( $cleaned );
        }

        return $cleaned;
    }
}

if ( ! function_exists( 'pf2_schema_get_meta_text' ) ) {
    /**
     * Retrieve a sanitized string meta value for schema usage.
     *
     * @param int    $post_id Post identifier.
     * @param string $meta_key Meta key.
     * @return string
     */
    function pf2_schema_get_meta_text( $post_id, $meta_key ) {
        $value = get_post_meta( (int) $post_id, $meta_key, true );

        if ( is_string( $value ) ) {
            return sanitize_text_field( $value );
        }

        if ( is_scalar( $value ) ) {
            return sanitize_text_field( (string) $value );
        }

        return '';
    }
}

if ( ! function_exists( 'pf2_schema_array_filter_recursive' ) ) {
    /**
     * Recursively strip empty values from a schema fragment.
     *
     * Acts as a backwards compatible wrapper around {@see pf2_schema_clean()}.
     *
     * @param mixed $data Raw schema data.
     * @return array<mixed>
     */
    function pf2_schema_array_filter_recursive( $data ) {
        return pf2_schema_clean( $data );
    }
}

if ( ! function_exists( 'pf2_schema_images_from_post' ) ) {
    /**
     * Collect image URLs associated with a post.
     *
     * @param int $post_id Post identifier.
     * @return array<int, string>
     */
    function pf2_schema_images_from_post( $post_id ) {
        $post_id = absint( $post_id );

        if ( ! $post_id ) {
            return array();
        }

        $images = array();

        $featured_id = get_post_thumbnail_id( $post_id );
        if ( $featured_id ) {
            $featured_url = wp_get_attachment_image_url( $featured_id, 'full' );
            if ( $featured_url ) {
                $images[] = esc_url_raw( $featured_url );
            }
        }

        $gallery_meta = get_post_meta( $post_id, 'pf2_gallery_ids', true );
        if ( is_string( $gallery_meta ) && '' !== trim( $gallery_meta ) ) {
            $gallery_ids = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', $gallery_meta ) ) ) );
        } elseif ( is_array( $gallery_meta ) ) {
            $gallery_ids = array();
            foreach ( $gallery_meta as $item ) {
                if ( is_scalar( $item ) ) {
                    $gallery_ids[] = absint( $item );
                }
            }
        } else {
            $gallery_ids = array();
        }

        foreach ( $gallery_ids as $gallery_id ) {
            if ( ! $gallery_id ) {
                continue;
            }

            $image_url = wp_get_attachment_image_url( $gallery_id, 'full' );
            if ( $image_url ) {
                $images[] = esc_url_raw( $image_url );
            }
        }

        $images = array_values( array_unique( array_filter( $images ) ) );

        return $images;
    }
}

if ( ! function_exists( 'pf2_schema_merge' ) ) {
    /**
     * Flatten a list of schema fragments into a single dimensional array.
     *
     * @param array<mixed> ...$items Schema fragments.
     * @return array<int, array<string, mixed>>
     */
    function pf2_schema_merge( ...$items ) {
        $merged = array();

        foreach ( $items as $item ) {
            if ( empty( $item ) ) {
                continue;
            }

            if ( is_array( $item ) && isset( $item['@type'] ) ) {
                $merged[] = $item;
                continue;
            }

            if ( is_array( $item ) ) {
                foreach ( $item as $sub_item ) {
                    $merged = array_merge( $merged, pf2_schema_merge( $sub_item ) );
                }
            }
        }

        return $merged;
    }
}

if ( ! function_exists( 'pf2_schema_is_enabled' ) ) {
    /**
     * Determine whether a schema fragment should be generated.
     *
     * @param string               $type    Schema type key.
     * @param array<string, mixed> $context Optional context information.
     * @return bool
     */
    function pf2_schema_is_enabled( $type, $context = array() ) {
        $type    = sanitize_key( $type );
        $default = true;

        if ( 'output' === $type ) {
            $default = (bool) pf2_options_get( 'schema_enabled', 1 );

            if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) {
                $default = false;
            }
        }

        /**
         * Filter schema enablement per type.
         *
         * @param bool                 $enabled Whether the schema should render.
         * @param array<string, mixed> $context Request context.
         */
        return (bool) apply_filters( "pf2_schema_enable_{$type}", $default, $context );
    }
}

if ( ! function_exists( 'pf2_schema_context' ) ) {
    /**
     * Build a contextual data snapshot for schema generation.
     *
     * @return array<string, mixed>
     */
    function pf2_schema_context() {
        $post_id   = get_queried_object_id();
        $post_type = $post_id ? get_post_type( $post_id ) : '';
        $language  = get_bloginfo( 'language' );
        $site_name = get_bloginfo( 'name' );
        $site_desc = get_bloginfo( 'description', 'display' );
        $site_url  = home_url( '/' );

        $canonical = '';
        if ( function_exists( 'wp_get_canonical_url' ) ) {
            $canonical = wp_get_canonical_url();
        }

        if ( ! $canonical ) {
            if ( is_singular() && $post_id ) {
                $canonical = get_permalink( $post_id );
            } elseif ( is_front_page() ) {
                $canonical = home_url( '/' );
            } elseif ( is_home() && ( $posts_page = (int) get_option( 'page_for_posts' ) ) ) {
                $canonical = get_permalink( $posts_page );
            } else {
                global $wp;
                $request_path = '';
                if ( isset( $wp ) && is_object( $wp ) && isset( $wp->request ) ) {
                    $request_path = trim( (string) $wp->request );
                }

                $canonical = $request_path ? home_url( trailingslashit( $request_path ) ) : home_url( '/' );
            }
        }

        $canonical = $canonical ? esc_url_raw( $canonical ) : '';

        $post_title   = $post_id ? get_the_title( $post_id ) : '';
        $post_excerpt = $post_id ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : '';

        $logo_id    = (int) get_theme_mod( 'custom_logo' );
        $logo_url   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
        $icon_url   = get_site_icon_url( 512 );
        $option_logo = (string) pf2_options_get( 'logo_url', '' );

        return array(
            'post_id'        => $post_id,
            'post_type'      => $post_type ? (string) $post_type : '',
            'is_home'        => is_home(),
            'is_front'       => is_front_page(),
            'is_archive'     => is_archive(),
            'is_singular'    => is_singular(),
            'lang'           => $language ? (string) $language : '',
            'site_name'      => $site_name ? (string) $site_name : '',
            'site_description' => $site_desc ? (string) $site_desc : '',
            'site_url'       => $site_url ? esc_url_raw( $site_url ) : '',
            'url'            => $canonical,
            'post_title'     => $post_title ? (string) $post_title : '',
            'post_excerpt'   => $post_excerpt,
            'logo_id'        => $logo_id,
            'logo_url'       => $logo_url ? esc_url_raw( $logo_url ) : '',
            'option_logo'    => $option_logo ? esc_url_raw( $option_logo ) : '',
            'site_icon'      => $icon_url ? esc_url_raw( $icon_url ) : '',
        );
    }
}
