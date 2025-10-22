<?php
/**
 * Schema meta registration utilities.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_meta_can_edit' ) ) {
    /**
     * Determine whether the current user can edit schema meta values.
     *
     * @param bool   $allowed  Whether editing is currently allowed.
     * @param string $meta_key Meta key name.
     * @param int    $post_id  Post identifier.
     * @return bool
     */
    function pf2_schema_meta_can_edit( $allowed, $meta_key, $post_id ) {
        unset( $allowed, $meta_key );

        return current_user_can( 'edit_post', (int) $post_id );
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_boolean' ) ) {
    /**
     * Sanitize boolean values.
     *
     * @param mixed $value Raw value.
     * @return bool
     */
    function pf2_schema_meta_sanitize_boolean( $value ) {
        return (bool) rest_sanitize_boolean( $value );
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_string' ) ) {
    /**
     * Sanitize string values.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf2_schema_meta_sanitize_string( $value ) {
        if ( is_scalar( $value ) ) {
            return sanitize_text_field( (string) $value );
        }

        return '';
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_textarea' ) ) {
    /**
     * Sanitize textarea values.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf2_schema_meta_sanitize_textarea( $value ) {
        if ( is_string( $value ) ) {
            return sanitize_textarea_field( $value );
        }

        if ( is_scalar( $value ) ) {
            return sanitize_textarea_field( (string) $value );
        }

        return '';
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_url' ) ) {
    /**
     * Sanitize URL values.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf2_schema_meta_sanitize_url( $value ) {
        if ( ! is_scalar( $value ) ) {
            return '';
        }

        $value = trim( (string) $value );
        if ( '' === $value ) {
            return '';
        }

        return esc_url_raw( $value );
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_int' ) ) {
    /**
     * Sanitize integer values.
     *
     * @param mixed $value Raw value.
     * @return int
     */
    function pf2_schema_meta_sanitize_int( $value ) {
        return absint( $value );
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_string_array' ) ) {
    /**
     * Sanitize an array of string values.
     *
     * @param mixed $value Raw value.
     * @return array<int, string>
     */
    function pf2_schema_meta_sanitize_string_array( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $sanitized = array();

        foreach ( $value as $entry ) {
            if ( ! is_scalar( $entry ) ) {
                continue;
            }

            $text = sanitize_text_field( (string) $entry );
            if ( '' === $text ) {
                continue;
            }

            if ( ! in_array( $text, $sanitized, true ) ) {
                $sanitized[] = $text;
            }
        }

        return $sanitized;
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_faq_items' ) ) {
    /**
     * Sanitize FAQ repeater entries.
     *
     * @param mixed $value Raw value.
     * @return array<int, array<string, string>>
     */
    function pf2_schema_meta_sanitize_faq_items( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $items = array();

        foreach ( $value as $entry ) {
            if ( ! is_array( $entry ) ) {
                continue;
            }

            $question = isset( $entry['question'] ) ? pf2_schema_meta_sanitize_string( $entry['question'] ) : '';
            $answer   = isset( $entry['answer'] ) ? pf2_schema_meta_sanitize_textarea( $entry['answer'] ) : '';

            if ( '' === $question || '' === $answer ) {
                continue;
            }

            $items[] = array(
                'question' => $question,
                'answer'   => $answer,
            );
        }

        return $items;
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_howto_steps' ) ) {
    /**
     * Sanitize HowTo repeater entries.
     *
     * @param mixed $value Raw value.
     * @return array<int, array<string, mixed>>
     */
    function pf2_schema_meta_sanitize_howto_steps( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $steps = array();

        foreach ( $value as $entry ) {
            if ( ! is_array( $entry ) ) {
                continue;
            }

            $name  = isset( $entry['name'] ) ? pf2_schema_meta_sanitize_string( $entry['name'] ) : '';
            $text  = isset( $entry['text'] ) ? pf2_schema_meta_sanitize_textarea( $entry['text'] ) : '';
            $image = isset( $entry['image_id'] ) ? pf2_schema_meta_sanitize_int( $entry['image_id'] ) : 0;

            if ( '' === $name && '' === $text && ! $image ) {
                continue;
            }

            $step = array(
                'name' => $name,
                'text' => $text,
            );

            if ( $image ) {
                $step['image_id'] = $image;
            }

            $steps[] = $step;
        }

        return $steps;
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_servicearea_type' ) ) {
    /**
     * Sanitize service area type.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf2_schema_meta_sanitize_servicearea_type( $value ) {
        $value   = pf2_schema_meta_sanitize_string( $value );
        $allowed = array( '', 'City', 'Country', 'Region', 'PostalAddress', 'GeoShape' );

        if ( ! in_array( $value, $allowed, true ) ) {
            return '';
        }

        return $value;
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_servicearea_postal' ) ) {
    /**
     * Sanitize PostalAddress payload.
     *
     * @param mixed $value Raw value.
     * @return array<string, string>
     */
    function pf2_schema_meta_sanitize_servicearea_postal( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $fields = array(
            'streetAddress',
            'addressLocality',
            'addressRegion',
            'postalCode',
            'addressCountry',
        );

        $sanitized = array();

        foreach ( $fields as $field ) {
            if ( isset( $value[ $field ] ) ) {
                $sanitized[ $field ] = pf2_schema_meta_sanitize_string( $value[ $field ] );
            }
        }

        return array_filter( $sanitized );
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_servicearea_geo' ) ) {
    /**
     * Sanitize GeoShape payload.
     *
     * @param mixed $value Raw value.
     * @return array<string, string>
     */
    function pf2_schema_meta_sanitize_servicearea_geo( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $sanitized = array();

        if ( isset( $value['circle'] ) ) {
            $circle = pf2_schema_meta_sanitize_string( $value['circle'] );
            if ( '' !== $circle ) {
                $sanitized['circle'] = $circle;
            }
        }

        if ( isset( $value['polygon'] ) ) {
            $polygon = pf2_schema_meta_sanitize_textarea( $value['polygon'] );
            if ( '' !== $polygon ) {
                $sanitized['polygon'] = $polygon;
            }
        }

        return $sanitized;
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_tourist_geo' ) ) {
    /**
     * Sanitize TouristAttraction geo payload.
     *
     * @param mixed $value Raw value.
     * @return array<string, string>
     */
    function pf2_schema_meta_sanitize_tourist_geo( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $sanitized = array();

        if ( isset( $value['latitude'] ) ) {
            $latitude = pf2_schema_meta_sanitize_string( $value['latitude'] );
            if ( '' !== $latitude ) {
                $sanitized['latitude'] = $latitude;
            }
        }

        if ( isset( $value['longitude'] ) ) {
            $longitude = pf2_schema_meta_sanitize_string( $value['longitude'] );
            if ( '' !== $longitude ) {
                $sanitized['longitude'] = $longitude;
            }
        }

        return $sanitized;
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_csv_ids' ) ) {
    /**
     * Sanitize a CSV string of attachment identifiers.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf2_schema_meta_sanitize_csv_ids( $value ) {
        $ids = array();

        if ( is_string( $value ) ) {
            $parts = preg_split( '/[\s,]+/', $value );
        } elseif ( is_array( $value ) ) {
            $parts = $value;
        } else {
            $parts = array();
        }

        foreach ( $parts as $part ) {
            $id = absint( $part );
            if ( $id && ! in_array( $id, $ids, true ) ) {
                $ids[] = $id;
            }
        }

        if ( empty( $ids ) ) {
            return '';
        }

        return implode( ',', $ids );
    }
}

if ( ! function_exists( 'pf2_schema_meta_register' ) ) {
    /**
     * Register schema-related post meta.
     *
     * @return void
     */
    function pf2_schema_meta_register() {
        $post_types = array( 'post', 'page', 'pf2_product', 'pf2_portfolio' );

        $boolean_args = array(
            'type'              => 'boolean',
            'single'            => true,
            'default'           => false,
            'show_in_rest'      => array(
                'schema' => array(
                    'type' => 'boolean',
                ),
            ),
            'auth_callback'     => 'pf2_schema_meta_can_edit',
            'sanitize_callback' => 'pf2_schema_meta_sanitize_boolean',
        );

        foreach ( $post_types as $post_type ) {
            register_post_meta( $post_type, 'pf2_schema_faq_enabled', $boolean_args );
            register_post_meta(
                $post_type,
                'pf2_schema_faq_items',
                array(
                    'type'              => 'array',
                    'single'            => true,
                    'default'           => array(),
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_faq_items',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'  => 'array',
                            'items' => array(
                                'type'       => 'object',
                                'properties' => array(
                                    'question' => array( 'type' => 'string' ),
                                    'answer'   => array( 'type' => 'string' ),
                                ),
                                'additionalProperties' => false,
                            ),
                        ),
                    ),
                )
            );

            register_post_meta( $post_type, 'pf2_schema_howto_enabled', $boolean_args );
            register_post_meta(
                $post_type,
                'pf2_schema_howto_name',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_howto_steps',
                array(
                    'type'              => 'array',
                    'single'            => true,
                    'default'           => array(),
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_howto_steps',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'  => 'array',
                            'items' => array(
                                'type'       => 'object',
                                'properties' => array(
                                    'name'     => array( 'type' => 'string' ),
                                    'text'     => array( 'type' => 'string' ),
                                    'image_id' => array( 'type' => 'integer' ),
                                ),
                                'additionalProperties' => false,
                            ),
                        ),
                    ),
                )
            );

            register_post_meta( $post_type, 'pf2_schema_video_enabled', $boolean_args );
            register_post_meta(
                $post_type,
                'pf2_schema_video_url',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_url',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'   => 'string',
                            'format' => 'uri',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_video_name',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_video_description',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_textarea',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_video_thumbnail_id',
                array(
                    'type'              => 'integer',
                    'single'            => true,
                    'default'           => 0,
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_int',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'integer',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_video_upload_date',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'   => 'string',
                            'format' => 'date-time',
                        ),
                    ),
                )
            );

            register_post_meta( $post_type, 'pf2_schema_servicearea_enabled', $boolean_args );
            register_post_meta(
                $post_type,
                'pf2_schema_servicearea_type',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_servicearea_type',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_servicearea_values',
                array(
                    'type'              => 'array',
                    'single'            => true,
                    'default'           => array(),
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string_array',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'  => 'array',
                            'items' => array(
                                'type' => 'string',
                            ),
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_servicearea_postal',
                array(
                    'type'              => 'object',
                    'single'            => true,
                    'default'           => array(),
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_servicearea_postal',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'       => 'object',
                            'properties' => array(
                                'streetAddress'   => array( 'type' => 'string' ),
                                'addressLocality' => array( 'type' => 'string' ),
                                'addressRegion'   => array( 'type' => 'string' ),
                                'postalCode'      => array( 'type' => 'string' ),
                                'addressCountry'  => array( 'type' => 'string' ),
                            ),
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_servicearea_geo',
                array(
                    'type'              => 'object',
                    'single'            => true,
                    'default'           => array(),
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_servicearea_geo',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'       => 'object',
                            'properties' => array(
                                'circle'  => array( 'type' => 'string' ),
                                'polygon' => array( 'type' => 'string' ),
                            ),
                        ),
                    ),
                )
            );

            register_post_meta( $post_type, 'pf2_schema_touristattraction_enabled', $boolean_args );
            register_post_meta(
                $post_type,
                'pf2_schema_touristattraction_name',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_touristattraction_description',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_textarea',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_touristattraction_image_ids',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_csv_ids',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_touristattraction_geo',
                array(
                    'type'              => 'object',
                    'single'            => true,
                    'default'           => array(),
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_tourist_geo',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'       => 'object',
                            'properties' => array(
                                'latitude'  => array( 'type' => 'string' ),
                                'longitude' => array( 'type' => 'string' ),
                            ),
                        ),
                    ),
                )
            );
        }
    }
}
add_action( 'init', 'pf2_schema_meta_register' );
