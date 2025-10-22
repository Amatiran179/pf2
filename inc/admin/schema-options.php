<?php
/**
 * Schema options admin integration.
 *
 * Registers LocalBusiness configuration controls within the theme options
 * interface and provides repeatable field render helpers.
 *
 * @package PF2\Admin\Schema
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_admin_register_tab' ) ) {
    /**
     * Append the Schema tab definition to the settings schema.
     *
     * @param array<string, array<string, mixed>> $tabs Existing tabs.
     * @return array<string, array<string, mixed>>
     */
    function pf2_schema_admin_register_tab( $tabs ) {
        $types = array(
            'LocalBusiness'        => __( 'Local Business', 'pf2' ),
            'AutomotiveBusiness'   => __( 'Automotive Business', 'pf2' ),
            'MedicalBusiness'      => __( 'Medical Business', 'pf2' ),
            'ProfessionalService'  => __( 'Professional Service', 'pf2' ),
            'FoodEstablishment'    => __( 'Food Establishment', 'pf2' ),
            'Restaurant'           => __( 'Restaurant', 'pf2' ),
            'Store'                => __( 'Store / Retail', 'pf2' ),
            'TravelAgency'         => __( 'Travel Agency', 'pf2' ),
            'LodgingBusiness'      => __( 'Lodging Business', 'pf2' ),
            'RealEstateAgent'      => __( 'Real Estate Agent', 'pf2' ),
            'HealthAndBeautyBusiness' => __( 'Health & Beauty Business', 'pf2' ),
        );

        $tabs['schema'] = array(
            'title'       => __( 'Schema', 'pf2' ),
            'description' => __( 'Configure structured data output such as LocalBusiness schema.', 'pf2' ),
            'fields'      => array(
                'localbusiness_enabled' => array(
                    'label'       => __( 'Enable Local Business schema', 'pf2' ),
                    'control'     => 'checkbox',
                    'sanitize'    => 'bool',
                    'description' => __( 'Toggle LocalBusiness JSON-LD output on selected pages.', 'pf2' ),
                ),
                'localbusiness_type' => array(
                    'label'       => __( 'Business Type', 'pf2' ),
                    'control'     => 'select',
                    'choices'     => $types,
                    'sanitize'    => 'schema_localbusiness_type',
                    'description' => __( 'Select the schema.org subtype that best represents the business.', 'pf2' ),
                ),
                'localbusiness_name' => array(
                    'label'    => __( 'Business Name', 'pf2' ),
                    'control'  => 'text',
                    'sanitize' => 'string',
                ),
                'localbusiness_description' => array(
                    'label'       => __( 'Business Description', 'pf2' ),
                    'control'     => 'textarea',
                    'sanitize'    => 'textarea',
                    'attributes'  => array(
                        'rows' => 3,
                    ),
                    'description' => __( 'Short summary of the business, products, or services.', 'pf2' ),
                ),
                'localbusiness_street' => array(
                    'label'    => __( 'Street Address', 'pf2' ),
                    'control'  => 'text',
                    'sanitize' => 'string',
                ),
                'localbusiness_locality' => array(
                    'label'    => __( 'City / Locality', 'pf2' ),
                    'control'  => 'text',
                    'sanitize' => 'string',
                ),
                'localbusiness_region' => array(
                    'label'    => __( 'State / Region', 'pf2' ),
                    'control'  => 'text',
                    'sanitize' => 'string',
                ),
                'localbusiness_postal' => array(
                    'label'    => __( 'Postal Code', 'pf2' ),
                    'control'  => 'text',
                    'sanitize' => 'string',
                ),
                'localbusiness_country' => array(
                    'label'       => __( 'Country', 'pf2' ),
                    'control'     => 'text',
                    'sanitize'    => 'string',
                    'attributes'  => array(
                        'maxlength'   => 2,
                        'style'       => 'width:80px;',
                        'placeholder' => 'ID',
                    ),
                    'description' => __( 'Use the two-letter country code (ISO 3166-1 alpha-2).', 'pf2' ),
                ),
                'localbusiness_latitude' => array(
                    'label'       => __( 'Latitude', 'pf2' ),
                    'control'     => 'text',
                    'sanitize'    => 'float',
                    'attributes'  => array(
                        'pattern' => '[-+]?[0-9]*\.?[0-9]+',
                    ),
                ),
                'localbusiness_longitude' => array(
                    'label'       => __( 'Longitude', 'pf2' ),
                    'control'     => 'text',
                    'sanitize'    => 'float',
                    'attributes'  => array(
                        'pattern' => '[-+]?[0-9]*\.?[0-9]+',
                    ),
                ),
                'localbusiness_telephone' => array(
                    'label'       => __( 'Telephone', 'pf2' ),
                    'control'     => 'text',
                    'sanitize'    => 'phone',
                    'description' => __( 'Primary phone number with country code.', 'pf2' ),
                ),
                'localbusiness_url' => array(
                    'label'       => __( 'Website URL', 'pf2' ),
                    'control'     => 'text',
                    'input_type'  => 'url',
                    'sanitize'    => 'url',
                ),
                'localbusiness_price_range' => array(
                    'label'       => __( 'Price Range', 'pf2' ),
                    'control'     => 'text',
                    'sanitize'    => 'string',
                    'description' => __( 'Typical price range, e.g. $$ or Rp100k - Rp500k.', 'pf2' ),
                ),
                'localbusiness_area_served' => array(
                    'label'       => __( 'Area Served', 'pf2' ),
                    'control'     => 'text',
                    'sanitize'    => 'string',
                    'description' => __( 'Comma-separated cities or regions served by the business.', 'pf2' ),
                ),
                'localbusiness_same_as' => array(
                    'label'       => __( 'Same As Profiles', 'pf2' ),
                    'control'     => 'callback',
                    'render_callback' => 'pf2_schema_options_render_same_as',
                    'sanitize'    => 'url_array',
                    'description' => __( 'List official social profiles or directories. Leave blank to skip.', 'pf2' ),
                ),
                'localbusiness_opening_hours' => array(
                    'label'       => __( 'Opening Hours', 'pf2' ),
                    'control'     => 'callback',
                    'render_callback' => 'pf2_schema_options_render_opening_hours',
                    'sanitize'    => 'opening_hours',
                    'description' => __( 'Add opening hours entries with day, open, and close times (24h).', 'pf2' ),
                ),
                'localbusiness_target_pages' => array(
                    'label'       => __( 'Target Pages', 'pf2' ),
                    'control'     => 'callback',
                    'render_callback' => 'pf2_schema_options_render_target_pages',
                    'sanitize'    => 'int_array',
                    'description' => __( 'Select the pages where LocalBusiness schema should appear.', 'pf2' ),
                ),
            ),
        );

        return $tabs;
    }
}
add_filter( 'pf2/admin/settings-schema', 'pf2_schema_admin_register_tab' );

if ( ! function_exists( 'pf2_schema_options_render_repeater_scripts' ) ) {
    /**
     * Output the JavaScript helpers for repeater style controls.
     *
     * Ensures the script is only printed once per request.
     *
     * @return void
     */
    function pf2_schema_options_render_repeater_scripts() {
        static $printed = false;

        if ( $printed ) {
            return;
        }

        $printed = true;
        ?>
        <script>
        ( function () {
                function initRepeater( container ) {
                        if ( ! container || container.dataset.pf2SchemaReady ) {
                                return;
                        }

                        container.dataset.pf2SchemaReady = '1';
                        var base = container.getAttribute( 'data-pf2-schema-base' );
                        var itemsWrap = container.querySelector( '[data-pf2-schema-repeater-items]' );

                        if ( ! base || ! itemsWrap ) {
                                return;
                        }

                        function updateNames() {
                                var items = itemsWrap.querySelectorAll( '[data-pf2-schema-repeater-item]' );
                                items.forEach( function ( item, index ) {
                                        item.querySelectorAll( '[data-pf2-schema-field]' ).forEach( function ( field ) {
                                                var suffix = field.getAttribute( 'data-pf2-schema-field' );
                                                var name = base + '[' + index + ']';
                                                if ( suffix ) {
                                                        name += '[' + suffix + ']';
                                                }
                                                field.setAttribute( 'name', name );
                                        } );
                                } );
                        }

                        container.addEventListener( 'click', function ( event ) {
                                if ( event.target.closest( '[data-pf2-schema-repeater-remove]' ) ) {
                                        event.preventDefault();
                                        var item = event.target.closest( '[data-pf2-schema-repeater-item]' );
                                        if ( ! item ) {
                                                return;
                                        }

                                        var currentItems = itemsWrap.querySelectorAll( '[data-pf2-schema-repeater-item]' );
                                        if ( currentItems.length <= 1 ) {
                                                item.querySelectorAll( '[data-pf2-schema-field]' ).forEach( function ( field ) {
                                                        if ( 'select-one' === field.type ) {
                                                                field.selectedIndex = 0;
                                                        } else {
                                                                field.value = '';
                                                        }
                                                } );
                                                return;
                                        }

                                        item.remove();
                                        updateNames();
                                }

                                if ( event.target.closest( '[data-pf2-schema-repeater-add]' ) ) {
                                        event.preventDefault();
                                        var template = container.querySelector( 'template[data-pf2-schema-repeater-template]' );
                                        if ( ! template ) {
                                                return;
                                        }

                                        var clone = document.importNode( template.content, true );
                                        itemsWrap.appendChild( clone );
                                        updateNames();
                                }
                        } );

                        updateNames();
                }

                document.addEventListener( 'DOMContentLoaded', function () {
                        document.querySelectorAll( '[data-pf2-schema-repeater]' ).forEach( initRepeater );
                } );
        } )();
        </script>
        <?php
    }
}

if ( ! function_exists( 'pf2_schema_options_render_same_as' ) ) {
    /**
     * Render the repeatable SameAs URL control.
     *
     * @param array<string, mixed> $args Field arguments.
     * @param mixed                $value Stored value.
     * @param string               $field_id Field identifier.
     * @param string               $aria_attribute Optional aria attributes.
     * @return void
     */
    function pf2_schema_options_render_same_as( $args, $value, $field_id, $aria_attribute ) {
        pf2_schema_options_render_repeater_scripts();

        if ( ! is_array( $value ) ) {
            $value = array();
        }

        $value = array_values( array_filter( array_map( 'trim', $value ) ) );
        if ( empty( $value ) ) {
            $value = array( '' );
        }

        $base_name = 'pf2_options[' . $args['key'] . ']';

        echo '<div class="pf2-schema-repeater" data-pf2-schema-repeater data-pf2-schema-base="' . esc_attr( $base_name ) . '">';
        echo '<div class="pf2-schema-repeater__items" data-pf2-schema-repeater-items>';

        foreach ( $value as $url ) {
            echo '<div class="pf2-schema-repeater__item" data-pf2-schema-repeater-item>';
            printf(
                '<input type="url" class="regular-text" value="%1$s"%2$s data-pf2-schema-field="" />',
                esc_attr( $url ),
                $aria_attribute
            );
            echo ' <button type="button" class="button button-link-delete" data-pf2-schema-repeater-remove>' . esc_html__( 'Remove', 'pf2' ) . '</button>';
            echo '</div>';
        }

        echo '</div>';
        echo '<template data-pf2-schema-repeater-template>';
        echo '<div class="pf2-schema-repeater__item" data-pf2-schema-repeater-item>';
        echo '<input type="url" class="regular-text" data-pf2-schema-field="" ' . $aria_attribute . ' />';
        echo ' <button type="button" class="button button-link-delete" data-pf2-schema-repeater-remove>' . esc_html__( 'Remove', 'pf2' ) . '</button>';
        echo '</div>';
        echo '</template>';
        echo '<button type="button" class="button" data-pf2-schema-repeater-add>' . esc_html__( 'Add URL', 'pf2' ) . '</button>';
        echo '</div>';
    }
}

if ( ! function_exists( 'pf2_schema_options_render_opening_hours' ) ) {
    /**
     * Render the repeatable opening hours control.
     *
     * @param array<string, mixed> $args Field arguments.
     * @param mixed                $value Stored value.
     * @param string               $field_id Field identifier.
     * @param string               $aria_attribute Optional aria attributes.
     * @return void
     */
    function pf2_schema_options_render_opening_hours( $args, $value, $field_id, $aria_attribute ) {
        pf2_schema_options_render_repeater_scripts();

        if ( ! is_array( $value ) ) {
            $value = array();
        }

        $days = array(
            'Monday'    => __( 'Monday', 'pf2' ),
            'Tuesday'   => __( 'Tuesday', 'pf2' ),
            'Wednesday' => __( 'Wednesday', 'pf2' ),
            'Thursday'  => __( 'Thursday', 'pf2' ),
            'Friday'    => __( 'Friday', 'pf2' ),
            'Saturday'  => __( 'Saturday', 'pf2' ),
            'Sunday'    => __( 'Sunday', 'pf2' ),
        );

        if ( empty( $value ) ) {
            $value = array(
                array(
                    'dayOfWeek' => 'Monday',
                    'opens'     => '',
                    'closes'    => '',
                ),
            );
        }

        $base_name = 'pf2_options[' . $args['key'] . ']';

        echo '<div class="pf2-schema-repeater" data-pf2-schema-repeater data-pf2-schema-base="' . esc_attr( $base_name ) . '">';
        echo '<div class="pf2-schema-repeater__items" data-pf2-schema-repeater-items>';

        foreach ( $value as $row ) {
            $day    = isset( $row['dayOfWeek'] ) ? $row['dayOfWeek'] : 'Monday';
            $opens  = isset( $row['opens'] ) ? $row['opens'] : '';
            $closes = isset( $row['closes'] ) ? $row['closes'] : '';

            echo '<div class="pf2-schema-repeater__item pf2-schema-repeater__item--hours" data-pf2-schema-repeater-item>';
            echo '<select data-pf2-schema-field="dayOfWeek" class="pf2-schema-repeater__select"' . $aria_attribute . '>';
            foreach ( $days as $key => $label ) {
                printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $key ), selected( $day, $key, false ), esc_html( $label ) );
            }
            echo '</select> ';
            printf( '<input type="time" value="%1$s" data-pf2-schema-field="opens"%2$s class="pf2-schema-repeater__time" /> ', esc_attr( $opens ), $aria_attribute );
            printf( '<input type="time" value="%1$s" data-pf2-schema-field="closes"%2$s class="pf2-schema-repeater__time" /> ', esc_attr( $closes ), $aria_attribute );
            echo '<button type="button" class="button button-link-delete" data-pf2-schema-repeater-remove>' . esc_html__( 'Remove', 'pf2' ) . '</button>';
            echo '</div>';
        }

        echo '</div>';
        echo '<template data-pf2-schema-repeater-template>';
        echo '<div class="pf2-schema-repeater__item pf2-schema-repeater__item--hours" data-pf2-schema-repeater-item>';
        echo '<select data-pf2-schema-field="dayOfWeek" class="pf2-schema-repeater__select"' . $aria_attribute . '>';
        foreach ( $days as $key => $label ) {
            printf( '<option value="%1$s">%2$s</option>', esc_attr( $key ), esc_html( $label ) );
        }
        echo '</select> ';
        echo '<input type="time" data-pf2-schema-field="opens" class="pf2-schema-repeater__time"' . $aria_attribute . ' /> ';
        echo '<input type="time" data-pf2-schema-field="closes" class="pf2-schema-repeater__time"' . $aria_attribute . ' /> ';
        echo '<button type="button" class="button button-link-delete" data-pf2-schema-repeater-remove>' . esc_html__( 'Remove', 'pf2' ) . '</button>';
        echo '</div>';
        echo '</template>';
        echo '<button type="button" class="button" data-pf2-schema-repeater-add>' . esc_html__( 'Add Hours', 'pf2' ) . '</button>';
        echo '</div>';
    }
}

if ( ! function_exists( 'pf2_schema_options_render_target_pages' ) ) {
    /**
     * Render the multi-select for target pages.
     *
     * @param array<string, mixed> $args Field arguments.
     * @param mixed                $value Stored value.
     * @param string               $field_id Field identifier.
     * @param string               $aria_attribute Optional aria attributes.
     * @return void
     */
    function pf2_schema_options_render_target_pages( $args, $value, $field_id, $aria_attribute ) {
        $selected = array();
        if ( is_array( $value ) ) {
            $selected = array_map( 'absint', $value );
        }

        $pages = get_pages( array(
            'sort_column' => 'post_title',
            'sort_order'  => 'ASC',
            'post_status' => array( 'publish', 'private' ),
        ) );

        $size = count( $pages ) > 10 ? 10 : max( 4, count( $pages ) );
        $name = 'pf2_options[' . $args['key'] . '][]';

        if ( ! empty( $pages ) ) {
            printf( '<select id="%1$s" name="%2$s" multiple class="widefat" size="%3$d"%4$s>', esc_attr( $field_id ), esc_attr( $name ), (int) $size, $aria_attribute );
            foreach ( $pages as $page ) {
                printf(
                    '<option value="%1$d"%3$s>%2$s</option>',
                    (int) $page->ID,
                    esc_html( get_the_title( $page ) ),
                    selected( in_array( (int) $page->ID, $selected, true ), true, false )
                );
            }
            echo '</select>';
        } else {
            echo '<p class="description">' . esc_html__( 'No pages available. Create a page to target LocalBusiness schema.', 'pf2' ) . '</p>';
        }
    }
}
