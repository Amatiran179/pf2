<?php
/**
 * Schema meta registration and classic editor fallback UI.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_meta_can_edit' ) ) {
    /**
     * Determine whether the current user can edit schema meta.
     *
     * @param bool   $allowed Whether editing is allowed.
     * @param string $meta_key Meta key.
     * @param int    $post_id Post identifier.
     * @return bool
     */
    function pf2_schema_meta_can_edit( $allowed, $meta_key, $post_id ) {
        unset( $allowed, $meta_key );

        return current_user_can( 'edit_post', (int) $post_id );
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_boolean' ) ) {
    /**
     * Sanitize boolean meta values.
     *
     * @param mixed $value Raw value.
     * @return bool
     */
    function pf2_schema_meta_sanitize_boolean( $value ) {
        return rest_sanitize_boolean( $value );
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_string' ) ) {
    /**
     * Sanitize string meta values.
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
     * Sanitize multiline string meta values.
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
     * Sanitize URL meta values.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf2_schema_meta_sanitize_url( $value ) {
        if ( is_string( $value ) || is_scalar( $value ) ) {
            $value = trim( (string) $value );

            if ( '' === $value ) {
                return '';
            }

            return esc_url_raw( $value );
        }

        return '';
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_string_array' ) ) {
    /**
     * Sanitize an array of strings.
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

if ( ! function_exists( 'pf2_schema_meta_sanitize_int_array' ) ) {
    /**
     * Sanitize an array of integers.
     *
     * @param mixed $value Raw value.
     * @return array<int, int>
     */
    function pf2_schema_meta_sanitize_int_array( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $sanitized = array();

        foreach ( $value as $entry ) {
            if ( is_scalar( $entry ) ) {
                $number = absint( $entry );
                if ( $number && ! in_array( $number, $sanitized, true ) ) {
                    $sanitized[] = $number;
                }
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

            $question = isset( $entry['question'] ) ? sanitize_text_field( (string) $entry['question'] ) : '';
            $answer   = isset( $entry['answer'] ) ? sanitize_textarea_field( (string) $entry['answer'] ) : '';

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
     * Sanitize HowTo steps.
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

            $name  = isset( $entry['name'] ) ? sanitize_text_field( (string) $entry['name'] ) : '';
            $text  = isset( $entry['text'] ) ? sanitize_textarea_field( (string) $entry['text'] ) : '';
            $image = isset( $entry['image'] ) ? absint( $entry['image'] ) : 0;

            if ( '' === $name && '' === $text ) {
                continue;
            }

            $step = array(
                'name' => $name,
                'text' => $text,
            );

            if ( $image ) {
                $step['image'] = $image;
            }

            $steps[] = $step;
        }

        return $steps;
    }
}

if ( ! function_exists( 'pf2_schema_meta_sanitize_servicearea_type' ) ) {
    /**
     * Sanitize service area type selections.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf2_schema_meta_sanitize_servicearea_type( $value ) {
        $allowed = array( 'City', 'Country', 'Region', 'PostalAddress', 'GeoShape' );
        $value   = pf2_schema_meta_sanitize_string( $value );

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
            'streetAddress'   => 'pf2_schema_meta_sanitize_string',
            'addressLocality' => 'pf2_schema_meta_sanitize_string',
            'addressRegion'   => 'pf2_schema_meta_sanitize_string',
            'postalCode'      => 'pf2_schema_meta_sanitize_string',
            'addressCountry'  => 'pf2_schema_meta_sanitize_string',
        );

        $sanitized = array();

        foreach ( $fields as $key => $callback ) {
            if ( isset( $value[ $key ] ) ) {
                $sanitized[ $key ] = call_user_func( $callback, $value[ $key ] );
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

        if ( isset( $value['latitude'] ) && is_scalar( $value['latitude'] ) ) {
            $lat = (string) $value['latitude'];
            $lat = trim( $lat );
            if ( '' !== $lat ) {
                $sanitized['latitude'] = sanitize_text_field( $lat );
            }
        }

        if ( isset( $value['longitude'] ) && is_scalar( $value['longitude'] ) ) {
            $lng = (string) $value['longitude'];
            $lng = trim( $lng );
            if ( '' !== $lng ) {
                $sanitized['longitude'] = sanitize_text_field( $lng );
            }
        }

        return $sanitized;
    }
}

if ( ! function_exists( 'pf2_schema_meta_register' ) ) {
    /**
     * Register schema-related post meta fields.
     *
     * @return void
     */
    function pf2_schema_meta_register() {
        $post_types = array( 'post', 'page', 'pf2_product', 'pf2_portfolio' );

        $boolean_args = array(
            'type'              => 'boolean',
            'single'            => true,
            'show_in_rest'      => array(
                'schema' => array(
                    'type' => 'boolean',
                ),
            ),
            'auth_callback'     => 'pf2_schema_meta_can_edit',
            'sanitize_callback' => 'pf2_schema_meta_sanitize_boolean',
            'default'           => false,
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_faq_items',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'  => 'array',
                            'items' => array(
                                'type'       => 'object',
                                'properties' => array(
                                    'question' => array( 'type' => 'string' ),
                                    'answer'   => array( 'type' => 'string' ),
                                ),
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_howto_steps',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'  => 'array',
                            'items' => array(
                                'type'       => 'object',
                                'properties' => array(
                                    'name'  => array( 'type' => 'string' ),
                                    'text'  => array( 'type' => 'string' ),
                                    'image' => array( 'type' => 'integer' ),
                                ),
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_url',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_textarea',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_video_thumbnail',
                array(
                    'type'              => 'string',
                    'single'            => true,
                    'default'           => '',
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_url',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                            'format' => 'uri',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_servicearea_type',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                            'enum' => array( 'City', 'Country', 'Region', 'PostalAddress', 'GeoShape' ),
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string_array',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_servicearea_postal',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_servicearea_geo',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_string',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_textarea',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type' => 'string',
                        ),
                    ),
                )
            );
            register_post_meta(
                $post_type,
                'pf2_schema_touristattraction_images',
                array(
                    'type'              => 'array',
                    'single'            => true,
                    'default'           => array(),
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_int_array',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
                    'show_in_rest'      => array(
                        'schema' => array(
                            'type'  => 'array',
                            'items' => array(
                                'type' => 'integer',
                            ),
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
                    'sanitize_callback' => 'pf2_schema_meta_sanitize_tourist_geo',
                    'auth_callback'     => 'pf2_schema_meta_can_edit',
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

if ( ! function_exists( 'pf2_schema_meta_metabox_render' ) ) {
    /**
     * Render the classic editor meta box.
     *
     * @param \WP_Post $post Current post object.
     * @return void
     */
    function pf2_schema_meta_metabox_render( $post ) {
        wp_nonce_field( 'pf2_schema_meta_nonce', 'pf2_schema_meta_nonce' );

        $enabled_faq    = (bool) get_post_meta( $post->ID, 'pf2_schema_faq_enabled', true );
        $faq_items      = get_post_meta( $post->ID, 'pf2_schema_faq_items', true );
        $faq_text_lines = array();

        if ( is_array( $faq_items ) ) {
            foreach ( $faq_items as $item ) {
                if ( ! is_array( $item ) ) {
                    continue;
                }

                $question = isset( $item['question'] ) ? $item['question'] : '';
                $answer   = isset( $item['answer'] ) ? $item['answer'] : '';

                if ( '' === $question || '' === $answer ) {
                    continue;
                }

                $faq_text_lines[] = $question . ' | ' . $answer;
            }
        }

        $enabled_howto = (bool) get_post_meta( $post->ID, 'pf2_schema_howto_enabled', true );
        $howto_name    = get_post_meta( $post->ID, 'pf2_schema_howto_name', true );
        $howto_steps   = get_post_meta( $post->ID, 'pf2_schema_howto_steps', true );
        $howto_lines   = array();

        if ( is_array( $howto_steps ) ) {
            foreach ( $howto_steps as $step ) {
                if ( ! is_array( $step ) ) {
                    continue;
                }

                $name  = isset( $step['name'] ) ? $step['name'] : '';
                $text  = isset( $step['text'] ) ? $step['text'] : '';
                $image = isset( $step['image'] ) ? (int) $step['image'] : 0;

                if ( '' === $name && '' === $text && ! $image ) {
                    continue;
                }

                $howto_lines[] = implode( ' | ', array_filter( array( $name, $text, $image ? (string) $image : '' ) ) );
            }
        }

        $enabled_video        = (bool) get_post_meta( $post->ID, 'pf2_schema_video_enabled', true );
        $video_url            = get_post_meta( $post->ID, 'pf2_schema_video_url', true );
        $video_name           = get_post_meta( $post->ID, 'pf2_schema_video_name', true );
        $video_description    = get_post_meta( $post->ID, 'pf2_schema_video_description', true );
        $video_thumbnail      = get_post_meta( $post->ID, 'pf2_schema_video_thumbnail', true );
        $video_upload_date    = get_post_meta( $post->ID, 'pf2_schema_video_upload_date', true );

        $enabled_servicearea  = (bool) get_post_meta( $post->ID, 'pf2_schema_servicearea_enabled', true );
        $service_type         = get_post_meta( $post->ID, 'pf2_schema_servicearea_type', true );
        $service_values       = get_post_meta( $post->ID, 'pf2_schema_servicearea_values', true );
        $service_postal       = get_post_meta( $post->ID, 'pf2_schema_servicearea_postal', true );
        $service_geo          = get_post_meta( $post->ID, 'pf2_schema_servicearea_geo', true );

        $enabled_tourist      = (bool) get_post_meta( $post->ID, 'pf2_schema_touristattraction_enabled', true );
        $tourist_name         = get_post_meta( $post->ID, 'pf2_schema_touristattraction_name', true );
        $tourist_description  = get_post_meta( $post->ID, 'pf2_schema_touristattraction_description', true );
        $tourist_images       = get_post_meta( $post->ID, 'pf2_schema_touristattraction_images', true );
        $tourist_geo          = get_post_meta( $post->ID, 'pf2_schema_touristattraction_geo', true );

        $faq_block    = implode( "\n", $faq_text_lines );
        $howto_block  = implode( "\n", $howto_lines );
        $values_block = '';
        if ( is_array( $service_values ) && ! empty( $service_values ) ) {
            $values_block = implode( "\n", $service_values );
        }

        $tourist_images_value = '';
        if ( is_array( $tourist_images ) && ! empty( $tourist_images ) ) {
            $tourist_images_value = implode( ',', array_map( 'intval', $tourist_images ) );
        }

        ?>
        <div class="pf2-schema-metabox">
            <p><?php esc_html_e( 'Gunakan blok editor untuk pengalaman terbaik. Form di bawah menyediakan fallback untuk classic editor.', 'pf2' ); ?></p>

            <fieldset>
                <legend><?php esc_html_e( 'FAQ', 'pf2' ); ?></legend>
                <label>
                    <input type="checkbox" name="pf2_schema_faq_enabled" value="1" <?php checked( $enabled_faq ); ?> />
                    <?php esc_html_e( 'Aktifkan FAQPage schema', 'pf2' ); ?>
                </label>
                <p class="description"><?php esc_html_e( 'Format: Pertanyaan | Jawaban per baris.', 'pf2' ); ?></p>
                <textarea name="pf2_schema_faq_items_raw" rows="4" class="large-text"><?php echo esc_textarea( $faq_block ); ?></textarea>
            </fieldset>

            <fieldset>
                <legend><?php esc_html_e( 'HowTo', 'pf2' ); ?></legend>
                <label>
                    <input type="checkbox" name="pf2_schema_howto_enabled" value="1" <?php checked( $enabled_howto ); ?> />
                    <?php esc_html_e( 'Aktifkan HowTo schema', 'pf2' ); ?>
                </label>
                <p>
                    <label for="pf2_schema_howto_name" class="screen-reader-text"><?php esc_html_e( 'Judul HowTo', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_howto_name" name="pf2_schema_howto_name" class="regular-text" value="<?php echo esc_attr( $howto_name ); ?>" placeholder="<?php esc_attr_e( 'Judul HowTo (opsional)', 'pf2' ); ?>" />
                </p>
                <p class="description"><?php esc_html_e( 'Format: Nama Langkah | Deskripsi | ID Gambar (opsional) per baris.', 'pf2' ); ?></p>
                <textarea name="pf2_schema_howto_steps_raw" rows="4" class="large-text"><?php echo esc_textarea( $howto_block ); ?></textarea>
            </fieldset>

            <fieldset>
                <legend><?php esc_html_e( 'Video', 'pf2' ); ?></legend>
                <label>
                    <input type="checkbox" name="pf2_schema_video_enabled" value="1" <?php checked( $enabled_video ); ?> />
                    <?php esc_html_e( 'Aktifkan VideoObject schema', 'pf2' ); ?>
                </label>
                <p><label for="pf2_schema_video_url"><?php esc_html_e( 'URL Video', 'pf2' ); ?></label>
                <input type="url" id="pf2_schema_video_url" name="pf2_schema_video_url" class="regular-text" value="<?php echo esc_attr( $video_url ); ?>" /></p>
                <p><label for="pf2_schema_video_name"><?php esc_html_e( 'Judul Video', 'pf2' ); ?></label>
                <input type="text" id="pf2_schema_video_name" name="pf2_schema_video_name" class="regular-text" value="<?php echo esc_attr( $video_name ); ?>" /></p>
                <p><label for="pf2_schema_video_description"><?php esc_html_e( 'Deskripsi Video', 'pf2' ); ?></label>
                <textarea id="pf2_schema_video_description" name="pf2_schema_video_description" rows="3" class="large-text"><?php echo esc_textarea( $video_description ); ?></textarea></p>
                <p><label for="pf2_schema_video_thumbnail"><?php esc_html_e( 'Thumbnail URL', 'pf2' ); ?></label>
                <input type="url" id="pf2_schema_video_thumbnail" name="pf2_schema_video_thumbnail" class="regular-text" value="<?php echo esc_attr( $video_thumbnail ); ?>" /></p>
                <p><label for="pf2_schema_video_upload_date"><?php esc_html_e( 'Tanggal Upload (ISO 8601)', 'pf2' ); ?></label>
                <input type="text" id="pf2_schema_video_upload_date" name="pf2_schema_video_upload_date" class="regular-text" value="<?php echo esc_attr( $video_upload_date ); ?>" /></p>
            </fieldset>

            <fieldset>
                <legend><?php esc_html_e( 'Service Area', 'pf2' ); ?></legend>
                <label>
                    <input type="checkbox" name="pf2_schema_servicearea_enabled" value="1" <?php checked( $enabled_servicearea ); ?> />
                    <?php esc_html_e( 'Aktifkan Service area schema', 'pf2' ); ?>
                </label>
                <p>
                    <label for="pf2_schema_servicearea_type"><?php esc_html_e( 'Tipe Area', 'pf2' ); ?></label>
                    <select id="pf2_schema_servicearea_type" name="pf2_schema_servicearea_type">
                        <option value="" <?php selected( '', $service_type ); ?>><?php esc_html_e( 'Pilih tipe', 'pf2' ); ?></option>
                        <?php
                        $types = array( 'City' => __( 'Kota', 'pf2' ), 'Country' => __( 'Negara', 'pf2' ), 'Region' => __( 'Provinsi/Region', 'pf2' ), 'PostalAddress' => __( 'Alamat', 'pf2' ), 'GeoShape' => __( 'GeoShape', 'pf2' ) );
                        foreach ( $types as $value => $label ) {
                            printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $service_type, $value, false ), esc_html( $label ) );
                        }
                        ?>
                    </select>
                </p>
                <p class="description"><?php esc_html_e( 'Masukkan daftar area (satu per baris) untuk City/Country/Region.', 'pf2' ); ?></p>
                <textarea name="pf2_schema_servicearea_values_raw" rows="3" class="large-text"><?php echo esc_textarea( $values_block ); ?></textarea>
                <details>
                    <summary><?php esc_html_e( 'Detail PostalAddress', 'pf2' ); ?></summary>
                    <p><label for="pf2_schema_servicearea_postal_street"><?php esc_html_e( 'Alamat Jalan', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_servicearea_postal_street" name="pf2_schema_servicearea_postal[streetAddress]" class="regular-text" value="<?php echo isset( $service_postal['streetAddress'] ) ? esc_attr( $service_postal['streetAddress'] ) : ''; ?>" /></p>
                    <p><label for="pf2_schema_servicearea_postal_locality"><?php esc_html_e( 'Kota', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_servicearea_postal_locality" name="pf2_schema_servicearea_postal[addressLocality]" class="regular-text" value="<?php echo isset( $service_postal['addressLocality'] ) ? esc_attr( $service_postal['addressLocality'] ) : ''; ?>" /></p>
                    <p><label for="pf2_schema_servicearea_postal_region"><?php esc_html_e( 'Provinsi/Region', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_servicearea_postal_region" name="pf2_schema_servicearea_postal[addressRegion]" class="regular-text" value="<?php echo isset( $service_postal['addressRegion'] ) ? esc_attr( $service_postal['addressRegion'] ) : ''; ?>" /></p>
                    <p><label for="pf2_schema_servicearea_postal_postalcode"><?php esc_html_e( 'Kode Pos', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_servicearea_postal_postalcode" name="pf2_schema_servicearea_postal[postalCode]" class="regular-text" value="<?php echo isset( $service_postal['postalCode'] ) ? esc_attr( $service_postal['postalCode'] ) : ''; ?>" /></p>
                    <p><label for="pf2_schema_servicearea_postal_country"><?php esc_html_e( 'Negara', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_servicearea_postal_country" name="pf2_schema_servicearea_postal[addressCountry]" class="regular-text" value="<?php echo isset( $service_postal['addressCountry'] ) ? esc_attr( $service_postal['addressCountry'] ) : ''; ?>" /></p>
                </details>
                <details>
                    <summary><?php esc_html_e( 'GeoShape', 'pf2' ); ?></summary>
                    <p><label for="pf2_schema_servicearea_geo_circle"><?php esc_html_e( 'Lingkaran (lat,lng radius)', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_servicearea_geo_circle" name="pf2_schema_servicearea_geo[circle]" class="regular-text" value="<?php echo isset( $service_geo['circle'] ) ? esc_attr( $service_geo['circle'] ) : ''; ?>" /></p>
                    <p><label for="pf2_schema_servicearea_geo_polygon"><?php esc_html_e( 'Polygon (koordinat dipisah spasi)', 'pf2' ); ?></label>
                    <textarea id="pf2_schema_servicearea_geo_polygon" name="pf2_schema_servicearea_geo[polygon]" rows="2" class="large-text"><?php echo isset( $service_geo['polygon'] ) ? esc_textarea( $service_geo['polygon'] ) : ''; ?></textarea></p>
                </details>
            </fieldset>

            <fieldset>
                <legend><?php esc_html_e( 'Tourist Attraction', 'pf2' ); ?></legend>
                <label>
                    <input type="checkbox" name="pf2_schema_touristattraction_enabled" value="1" <?php checked( $enabled_tourist ); ?> />
                    <?php esc_html_e( 'Aktifkan TouristAttraction schema', 'pf2' ); ?>
                </label>
                <p><label for="pf2_schema_touristattraction_name"><?php esc_html_e( 'Nama', 'pf2' ); ?></label>
                <input type="text" id="pf2_schema_touristattraction_name" name="pf2_schema_touristattraction_name" class="regular-text" value="<?php echo esc_attr( $tourist_name ); ?>" /></p>
                <p><label for="pf2_schema_touristattraction_description"><?php esc_html_e( 'Deskripsi', 'pf2' ); ?></label>
                <textarea id="pf2_schema_touristattraction_description" name="pf2_schema_touristattraction_description" rows="3" class="large-text"><?php echo esc_textarea( $tourist_description ); ?></textarea></p>
                <p class="description"><?php esc_html_e( 'ID gambar dipisahkan koma.', 'pf2' ); ?></p>
                <input type="text" name="pf2_schema_touristattraction_images_raw" class="regular-text" value="<?php echo esc_attr( $tourist_images_value ); ?>" />
                <details>
                    <summary><?php esc_html_e( 'Koordinat', 'pf2' ); ?></summary>
                    <p><label for="pf2_schema_touristattraction_geo_lat"><?php esc_html_e( 'Latitude', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_touristattraction_geo_lat" name="pf2_schema_touristattraction_geo[latitude]" class="regular-text" value="<?php echo isset( $tourist_geo['latitude'] ) ? esc_attr( $tourist_geo['latitude'] ) : ''; ?>" /></p>
                    <p><label for="pf2_schema_touristattraction_geo_lng"><?php esc_html_e( 'Longitude', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_touristattraction_geo_lng" name="pf2_schema_touristattraction_geo[longitude]" class="regular-text" value="<?php echo isset( $tourist_geo['longitude'] ) ? esc_attr( $tourist_geo['longitude'] ) : ''; ?>" /></p>
                </details>
            </fieldset>
        </div>
        <?php
    }
}

if ( ! function_exists( 'pf2_schema_meta_add_metabox' ) ) {
    /**
     * Register the classic editor metabox.
     *
     * @return void
     */
    function pf2_schema_meta_add_metabox() {
        $post_types = array( 'post', 'page', 'pf2_product', 'pf2_portfolio' );

        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'pf2_schema_meta',
                esc_html__( 'PF2 Schema Extras', 'pf2' ),
                'pf2_schema_meta_metabox_render',
                $post_type,
                'normal',
                'default'
            );
        }
    }
}
add_action( 'add_meta_boxes', 'pf2_schema_meta_add_metabox' );

if ( ! function_exists( 'pf2_schema_meta_save' ) ) {
    /**
     * Persist meta values from the classic editor form.
     *
     * @param int $post_id Post identifier.
     * @return void
     */
    function pf2_schema_meta_save( $post_id ) {
        if ( ! isset( $_POST['pf2_schema_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['pf2_schema_meta_nonce'] ), 'pf2_schema_meta_nonce' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $post_type = get_post_type( $post_id );
        $allowed   = array( 'post', 'page', 'pf2_product', 'pf2_portfolio' );

        if ( ! in_array( $post_type, $allowed, true ) ) {
            return;
        }

        $checkboxes = array(
            'pf2_schema_faq_enabled',
            'pf2_schema_howto_enabled',
            'pf2_schema_video_enabled',
            'pf2_schema_servicearea_enabled',
            'pf2_schema_touristattraction_enabled',
        );

        foreach ( $checkboxes as $checkbox ) {
            $value = isset( $_POST[ $checkbox ] ) ? rest_sanitize_boolean( wp_unslash( $_POST[ $checkbox ] ) ) : false;
            update_post_meta( $post_id, $checkbox, $value );
        }

        $faq_items = array();
        if ( isset( $_POST['pf2_schema_faq_items_raw'] ) ) {
            $lines = explode( "\n", (string) wp_unslash( $_POST['pf2_schema_faq_items_raw'] ) );
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( '' === $line ) {
                    continue;
                }

                $parts = array_map( 'trim', explode( '|', $line ) );
                if ( count( $parts ) < 2 ) {
                    continue;
                }

                $faq_items[] = array(
                    'question' => sanitize_text_field( $parts[0] ),
                    'answer'   => sanitize_textarea_field( $parts[1] ),
                );
            }
        }
        update_post_meta( $post_id, 'pf2_schema_faq_items', pf2_schema_meta_sanitize_faq_items( $faq_items ) );

        $howto_name = isset( $_POST['pf2_schema_howto_name'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['pf2_schema_howto_name'] ) ) : '';
        update_post_meta( $post_id, 'pf2_schema_howto_name', $howto_name );

        $steps = array();
        if ( isset( $_POST['pf2_schema_howto_steps_raw'] ) ) {
            $lines = explode( "\n", (string) wp_unslash( $_POST['pf2_schema_howto_steps_raw'] ) );
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( '' === $line ) {
                    continue;
                }

                $parts = array_map( 'trim', explode( '|', $line ) );
                if ( empty( $parts ) ) {
                    continue;
                }

                $entry = array(
                    'name' => isset( $parts[0] ) ? sanitize_text_field( $parts[0] ) : '',
                    'text' => isset( $parts[1] ) ? sanitize_textarea_field( $parts[1] ) : '',
                );

                if ( isset( $parts[2] ) ) {
                    $entry['image'] = absint( $parts[2] );
                }

                $steps[] = $entry;
            }
        }
        update_post_meta( $post_id, 'pf2_schema_howto_steps', pf2_schema_meta_sanitize_howto_steps( $steps ) );

        $video_map = array(
            'pf2_schema_video_url'         => 'pf2_schema_meta_sanitize_url',
            'pf2_schema_video_name'        => 'pf2_schema_meta_sanitize_string',
            'pf2_schema_video_description' => 'pf2_schema_meta_sanitize_textarea',
            'pf2_schema_video_thumbnail'   => 'pf2_schema_meta_sanitize_url',
            'pf2_schema_video_upload_date' => 'pf2_schema_meta_sanitize_string',
        );

        foreach ( $video_map as $key => $callback ) {
            $value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
            update_post_meta( $post_id, $key, call_user_func( $callback, $value ) );
        }

        $service_type = isset( $_POST['pf2_schema_servicearea_type'] ) ? wp_unslash( $_POST['pf2_schema_servicearea_type'] ) : '';
        update_post_meta( $post_id, 'pf2_schema_servicearea_type', pf2_schema_meta_sanitize_servicearea_type( $service_type ) );

        $service_values = array();
        if ( isset( $_POST['pf2_schema_servicearea_values_raw'] ) ) {
            $lines = explode( "\n", (string) wp_unslash( $_POST['pf2_schema_servicearea_values_raw'] ) );
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( '' === $line ) {
                    continue;
                }

                $service_values[] = sanitize_text_field( $line );
            }
        }
        update_post_meta( $post_id, 'pf2_schema_servicearea_values', pf2_schema_meta_sanitize_string_array( $service_values ) );

        $postal_raw = isset( $_POST['pf2_schema_servicearea_postal'] ) ? wp_unslash( $_POST['pf2_schema_servicearea_postal'] ) : array();
        if ( is_array( $postal_raw ) ) {
            update_post_meta( $post_id, 'pf2_schema_servicearea_postal', pf2_schema_meta_sanitize_servicearea_postal( $postal_raw ) );
        }

        $geo_raw = isset( $_POST['pf2_schema_servicearea_geo'] ) ? wp_unslash( $_POST['pf2_schema_servicearea_geo'] ) : array();
        if ( is_array( $geo_raw ) ) {
            update_post_meta( $post_id, 'pf2_schema_servicearea_geo', pf2_schema_meta_sanitize_servicearea_geo( $geo_raw ) );
        }

        $tourist_map = array(
            'pf2_schema_touristattraction_name'        => 'pf2_schema_meta_sanitize_string',
            'pf2_schema_touristattraction_description' => 'pf2_schema_meta_sanitize_textarea',
        );

        foreach ( $tourist_map as $key => $callback ) {
            $value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
            update_post_meta( $post_id, $key, call_user_func( $callback, $value ) );
        }

        $tourist_images = array();
        if ( isset( $_POST['pf2_schema_touristattraction_images_raw'] ) ) {
            $parts = explode( ',', (string) wp_unslash( $_POST['pf2_schema_touristattraction_images_raw'] ) );
            foreach ( $parts as $part ) {
                $part = trim( $part );
                if ( '' === $part ) {
                    continue;
                }

                $tourist_images[] = absint( $part );
            }
        }
        update_post_meta( $post_id, 'pf2_schema_touristattraction_images', pf2_schema_meta_sanitize_int_array( $tourist_images ) );

        $tourist_geo = isset( $_POST['pf2_schema_touristattraction_geo'] ) ? wp_unslash( $_POST['pf2_schema_touristattraction_geo'] ) : array();
        if ( is_array( $tourist_geo ) ) {
            update_post_meta( $post_id, 'pf2_schema_touristattraction_geo', pf2_schema_meta_sanitize_tourist_geo( $tourist_geo ) );
        }
    }
}
add_action( 'save_post', 'pf2_schema_meta_save' );
