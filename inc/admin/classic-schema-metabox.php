<?php
/**
 * Classic editor schema meta box.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pf2_schema_classic_supported_post_types' ) ) {
    /**
     * Retrieve the list of post types that support schema extras.
     *
     * @return array<int, string>
     */
    function pf2_schema_classic_supported_post_types() {
        return array( 'post', 'page', 'pf2_product', 'pf2_portfolio' );
    }
}

if ( ! function_exists( 'pf2_schema_classic_enqueue_assets' ) ) {
    /**
     * Enqueue assets required for the classic meta box UI.
     *
     * @param string $hook Current admin hook.
     * @return void
     */
    function pf2_schema_classic_enqueue_assets( $hook ) {
        if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->post_type, pf2_schema_classic_supported_post_types(), true ) ) {
            return;
        }

        $style_path = get_template_directory() . '/assets/css/admin/pf2-schema-tabs.css';
        if ( file_exists( $style_path ) ) {
            wp_enqueue_style(
                'pf2-schema-tabs',
                get_template_directory_uri() . '/assets/css/admin/pf2-schema-tabs.css',
                array(),
                filemtime( $style_path )
            );
        }

        $script = <<<'SCRIPT'
(function () {
    function activateTab(container, target) {
        const tabs = container.querySelectorAll('[data-tab-target]');
        const buttons = container.querySelectorAll('[data-tab]');
        buttons.forEach(function (button) {
            if (button.dataset.tab === target) {
                button.classList.add('is-active');
                button.setAttribute('aria-selected', 'true');
                button.setAttribute('tabindex', '0');
            } else {
                button.classList.remove('is-active');
                button.setAttribute('aria-selected', 'false');
                button.setAttribute('tabindex', '-1');
            }
        });
        tabs.forEach(function (panel) {
            if (panel.dataset.tabTarget === target) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', 'hidden');
            }
        });
    }

    window.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.pf2-schema-tabs').forEach(function (container) {
            const buttons = container.querySelectorAll('[data-tab]');
            if (!buttons.length) {
                return;
            }

            const initial = container.querySelector('[data-tab].is-active') || buttons[0];
            activateTab(container, initial.dataset.tab);

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activateTab(container, button.dataset.tab);
                });
            });
        });
    });
})();
SCRIPT;

        wp_register_script( 'pf2-schema-tabs', '', array(), '1.0.0', true );
        wp_add_inline_script( 'pf2-schema-tabs', $script );
        wp_enqueue_script( 'pf2-schema-tabs' );
    }
}
add_action( 'admin_enqueue_scripts', 'pf2_schema_classic_enqueue_assets' );

if ( ! function_exists( 'pf2_schema_classic_add_metabox' ) ) {
    /**
     * Register the classic meta box for supported post types.
     *
     * @return void
     */
    function pf2_schema_classic_add_metabox() {
        foreach ( pf2_schema_classic_supported_post_types() as $post_type ) {
            add_meta_box(
                'pf2_schema_meta',
                esc_html__( 'PF2 Schema', 'pf2' ),
                'pf2_schema_classic_render_metabox',
                $post_type,
                'normal',
                'default'
            );
        }
    }
}
add_action( 'add_meta_boxes', 'pf2_schema_classic_add_metabox' );

if ( ! function_exists( 'pf2_schema_classic_render_metabox' ) ) {
    /**
     * Render the classic schema meta box.
     *
     * @param \WP_Post $post Current post object.
     * @return void
     */
    function pf2_schema_classic_render_metabox( $post ) {
        wp_nonce_field( 'pf2_schema_meta_nonce', 'pf2_schema_meta_nonce' );

        $faq_enabled   = (bool) get_post_meta( $post->ID, 'pf2_schema_faq_enabled', true );
        $faq_items     = get_post_meta( $post->ID, 'pf2_schema_faq_items', true );
        $howto_enabled = (bool) get_post_meta( $post->ID, 'pf2_schema_howto_enabled', true );
        $howto_name    = get_post_meta( $post->ID, 'pf2_schema_howto_name', true );
        $howto_steps   = get_post_meta( $post->ID, 'pf2_schema_howto_steps', true );
        $video_enabled = (bool) get_post_meta( $post->ID, 'pf2_schema_video_enabled', true );
        $video_meta    = array(
            'url'          => get_post_meta( $post->ID, 'pf2_schema_video_url', true ),
            'name'         => get_post_meta( $post->ID, 'pf2_schema_video_name', true ),
            'description'  => get_post_meta( $post->ID, 'pf2_schema_video_description', true ),
            'thumbnail_id' => (int) get_post_meta( $post->ID, 'pf2_schema_video_thumbnail_id', true ),
            'upload_date'  => get_post_meta( $post->ID, 'pf2_schema_video_upload_date', true ),
        );
        $service_enabled = (bool) get_post_meta( $post->ID, 'pf2_schema_servicearea_enabled', true );
        $service_type    = get_post_meta( $post->ID, 'pf2_schema_servicearea_type', true );
        $service_values  = get_post_meta( $post->ID, 'pf2_schema_servicearea_values', true );
        $service_postal  = get_post_meta( $post->ID, 'pf2_schema_servicearea_postal', true );
        $service_geo     = get_post_meta( $post->ID, 'pf2_schema_servicearea_geo', true );
        $tourist_enabled = (bool) get_post_meta( $post->ID, 'pf2_schema_touristattraction_enabled', true );
        $tourist_meta    = array(
            'name'        => get_post_meta( $post->ID, 'pf2_schema_touristattraction_name', true ),
            'description' => get_post_meta( $post->ID, 'pf2_schema_touristattraction_description', true ),
            'image_ids'   => get_post_meta( $post->ID, 'pf2_schema_touristattraction_image_ids', true ),
            'geo'         => get_post_meta( $post->ID, 'pf2_schema_touristattraction_geo', true ),
        );

        $faq_lines = array();
        if ( is_array( $faq_items ) ) {
            foreach ( $faq_items as $item ) {
                if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
                    continue;
                }
                $faq_lines[] = $item['question'] . ' | ' . $item['answer'];
            }
        }

        $howto_lines = array();
        if ( is_array( $howto_steps ) ) {
            foreach ( $howto_steps as $step ) {
                $name  = isset( $step['name'] ) ? $step['name'] : '';
                $text  = isset( $step['text'] ) ? $step['text'] : '';
                $image = isset( $step['image_id'] ) ? (int) $step['image_id'] : 0;

                if ( '' === $name && '' === $text && ! $image ) {
                    continue;
                }

                $row = array( $name, $text );
                if ( $image ) {
                    $row[] = (string) $image;
                }
                $howto_lines[] = implode( ' | ', array_filter( $row, 'strlen' ) );
            }
        }

        if ( ! is_array( $service_values ) ) {
            $service_values = array();
        }

        if ( ! is_array( $service_postal ) ) {
            $service_postal = array();
        }

        if ( ! is_array( $service_geo ) ) {
            $service_geo = array();
        }

        if ( ! is_array( $tourist_meta['geo'] ) ) {
            $tourist_meta['geo'] = array();
        }

        ?>
        <div class="pf2-schema-tabs">
            <p class="description"><?php esc_html_e( 'Gunakan blok editor untuk pengalaman terbaik. Form ini menyediakan fallback untuk Classic Editor.', 'pf2' ); ?></p>
            <nav class="pf2-schema-tabs__nav" role="tablist">
                <button type="button" class="pf2-schema-tabs__tab is-active" data-tab="faq" role="tab"><?php esc_html_e( 'FAQ', 'pf2' ); ?></button>
                <button type="button" class="pf2-schema-tabs__tab" data-tab="howto" role="tab"><?php esc_html_e( 'HowTo', 'pf2' ); ?></button>
                <button type="button" class="pf2-schema-tabs__tab" data-tab="video" role="tab"><?php esc_html_e( 'Video', 'pf2' ); ?></button>
                <button type="button" class="pf2-schema-tabs__tab" data-tab="service" role="tab"><?php esc_html_e( 'Service Area', 'pf2' ); ?></button>
                <button type="button" class="pf2-schema-tabs__tab" data-tab="tourist" role="tab"><?php esc_html_e( 'Tourist Attraction', 'pf2' ); ?></button>
            </nav>

            <section class="pf2-schema-tabs__panel" data-tab-target="faq" role="tabpanel">
                <label>
                    <input type="checkbox" name="pf2_schema_faq_enabled" value="1" <?php checked( $faq_enabled ); ?> />
                    <?php esc_html_e( 'Aktifkan FAQPage schema', 'pf2' ); ?>
                </label>
                <p class="description"><?php esc_html_e( 'Gunakan format: Pertanyaan | Jawaban per baris.', 'pf2' ); ?></p>
                <textarea name="pf2_schema_faq_items_raw" rows="6" class="large-text"><?php echo esc_textarea( implode( "\n", $faq_lines ) ); ?></textarea>
            </section>

            <section class="pf2-schema-tabs__panel" data-tab-target="howto" role="tabpanel" hidden="hidden">
                <label>
                    <input type="checkbox" name="pf2_schema_howto_enabled" value="1" <?php checked( $howto_enabled ); ?> />
                    <?php esc_html_e( 'Aktifkan HowTo schema', 'pf2' ); ?>
                </label>
                <p>
                    <label for="pf2_schema_howto_name" class="screen-reader-text"><?php esc_html_e( 'Judul HowTo', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_howto_name" name="pf2_schema_howto_name" class="regular-text" value="<?php echo esc_attr( $howto_name ); ?>" placeholder="<?php esc_attr_e( 'Judul HowTo (opsional)', 'pf2' ); ?>" />
                </p>
                <p class="description"><?php esc_html_e( 'Format: Nama Langkah | Deskripsi | ID Gambar (opsional) per baris.', 'pf2' ); ?></p>
                <textarea name="pf2_schema_howto_steps_raw" rows="6" class="large-text"><?php echo esc_textarea( implode( "\n", $howto_lines ) ); ?></textarea>
            </section>

            <section class="pf2-schema-tabs__panel" data-tab-target="video" role="tabpanel" hidden="hidden">
                <label>
                    <input type="checkbox" name="pf2_schema_video_enabled" value="1" <?php checked( $video_enabled ); ?> />
                    <?php esc_html_e( 'Aktifkan VideoObject schema', 'pf2' ); ?>
                </label>
                <p>
                    <label for="pf2_schema_video_url" class="screen-reader-text"><?php esc_html_e( 'URL Video', 'pf2' ); ?></label>
                    <input type="url" id="pf2_schema_video_url" name="pf2_schema_video_url" class="regular-text" value="<?php echo esc_attr( $video_meta['url'] ); ?>" placeholder="https://" />
                </p>
                <p>
                    <label for="pf2_schema_video_name" class="screen-reader-text"><?php esc_html_e( 'Judul Video', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_video_name" name="pf2_schema_video_name" class="regular-text" value="<?php echo esc_attr( $video_meta['name'] ); ?>" />
                </p>
                <p>
                    <label for="pf2_schema_video_description" class="screen-reader-text"><?php esc_html_e( 'Deskripsi Video', 'pf2' ); ?></label>
                    <textarea id="pf2_schema_video_description" name="pf2_schema_video_description" rows="4" class="large-text"><?php echo esc_textarea( $video_meta['description'] ); ?></textarea>
                </p>
                <p>
                    <label for="pf2_schema_video_thumbnail_id" class="screen-reader-text"><?php esc_html_e( 'ID Thumbnail', 'pf2' ); ?></label>
                    <input type="number" id="pf2_schema_video_thumbnail_id" name="pf2_schema_video_thumbnail_id" class="small-text" value="<?php echo esc_attr( $video_meta['thumbnail_id'] ); ?>" min="0" />
                    <span class="description"><?php esc_html_e( 'Masukkan ID lampiran untuk thumbnail.', 'pf2' ); ?></span>
                </p>
                <p>
                    <label for="pf2_schema_video_upload_date" class="screen-reader-text"><?php esc_html_e( 'Tanggal Upload', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_video_upload_date" name="pf2_schema_video_upload_date" class="regular-text" value="<?php echo esc_attr( $video_meta['upload_date'] ); ?>" placeholder="2024-01-31T00:00:00+07:00" />
                </p>
            </section>

            <section class="pf2-schema-tabs__panel" data-tab-target="service" role="tabpanel" hidden="hidden">
                <label>
                    <input type="checkbox" name="pf2_schema_servicearea_enabled" value="1" <?php checked( $service_enabled ); ?> />
                    <?php esc_html_e( 'Aktifkan Service area schema', 'pf2' ); ?>
                </label>
                <p>
                    <label for="pf2_schema_servicearea_type" class="screen-reader-text"><?php esc_html_e( 'Tipe Area', 'pf2' ); ?></label>
                    <select id="pf2_schema_servicearea_type" name="pf2_schema_servicearea_type">
                        <option value="" <?php selected( $service_type, '' ); ?>><?php esc_html_e( 'Pilih tipe area', 'pf2' ); ?></option>
                        <option value="City" <?php selected( $service_type, 'City' ); ?>><?php esc_html_e( 'City', 'pf2' ); ?></option>
                        <option value="Country" <?php selected( $service_type, 'Country' ); ?>><?php esc_html_e( 'Country', 'pf2' ); ?></option>
                        <option value="Region" <?php selected( $service_type, 'Region' ); ?>><?php esc_html_e( 'Region', 'pf2' ); ?></option>
                        <option value="PostalAddress" <?php selected( $service_type, 'PostalAddress' ); ?>><?php esc_html_e( 'PostalAddress', 'pf2' ); ?></option>
                        <option value="GeoShape" <?php selected( $service_type, 'GeoShape' ); ?>><?php esc_html_e( 'GeoShape', 'pf2' ); ?></option>
                    </select>
                </p>
                <p class="description"><?php esc_html_e( 'Daftar area (City/Country/Region) pisahkan per baris.', 'pf2' ); ?></p>
                <textarea name="pf2_schema_servicearea_values_raw" rows="6" class="large-text"><?php echo esc_textarea( implode( "\n", $service_values ) ); ?></textarea>

                <fieldset>
                    <legend><?php esc_html_e( 'PostalAddress', 'pf2' ); ?></legend>
                    <p><input type="text" name="pf2_schema_servicearea_postal[streetAddress]" class="regular-text" placeholder="<?php esc_attr_e( 'Alamat jalan', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $service_postal['streetAddress'] ) ? $service_postal['streetAddress'] : '' ); ?>" /></p>
                    <p><input type="text" name="pf2_schema_servicearea_postal[addressLocality]" class="regular-text" placeholder="<?php esc_attr_e( 'Kota', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $service_postal['addressLocality'] ) ? $service_postal['addressLocality'] : '' ); ?>" /></p>
                    <p><input type="text" name="pf2_schema_servicearea_postal[addressRegion]" class="regular-text" placeholder="<?php esc_attr_e( 'Provinsi/Region', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $service_postal['addressRegion'] ) ? $service_postal['addressRegion'] : '' ); ?>" /></p>
                    <p><input type="text" name="pf2_schema_servicearea_postal[postalCode]" class="regular-text" placeholder="<?php esc_attr_e( 'Kode pos', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $service_postal['postalCode'] ) ? $service_postal['postalCode'] : '' ); ?>" /></p>
                    <p><input type="text" name="pf2_schema_servicearea_postal[addressCountry]" class="regular-text" placeholder="<?php esc_attr_e( 'Negara', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $service_postal['addressCountry'] ) ? $service_postal['addressCountry'] : '' ); ?>" /></p>
                </fieldset>

                <fieldset>
                    <legend><?php esc_html_e( 'GeoShape', 'pf2' ); ?></legend>
                    <p><input type="text" name="pf2_schema_servicearea_geo[circle]" class="regular-text" placeholder="<?php esc_attr_e( 'latitude,longitude radius', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $service_geo['circle'] ) ? $service_geo['circle'] : '' ); ?>" /></p>
                    <p><textarea name="pf2_schema_servicearea_geo[polygon]" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Daftar koordinat dipisahkan spasi', 'pf2' ); ?>"><?php echo esc_textarea( isset( $service_geo['polygon'] ) ? $service_geo['polygon'] : '' ); ?></textarea></p>
                </fieldset>
            </section>

            <section class="pf2-schema-tabs__panel" data-tab-target="tourist" role="tabpanel" hidden="hidden">
                <label>
                    <input type="checkbox" name="pf2_schema_touristattraction_enabled" value="1" <?php checked( $tourist_enabled ); ?> />
                    <?php esc_html_e( 'Aktifkan TouristAttraction schema', 'pf2' ); ?>
                </label>
                <p>
                    <label for="pf2_schema_touristattraction_name" class="screen-reader-text"><?php esc_html_e( 'Nama', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_touristattraction_name" name="pf2_schema_touristattraction_name" class="regular-text" value="<?php echo esc_attr( $tourist_meta['name'] ); ?>" />
                </p>
                <p>
                    <label for="pf2_schema_touristattraction_description" class="screen-reader-text"><?php esc_html_e( 'Deskripsi', 'pf2' ); ?></label>
                    <textarea id="pf2_schema_touristattraction_description" name="pf2_schema_touristattraction_description" rows="4" class="large-text"><?php echo esc_textarea( $tourist_meta['description'] ); ?></textarea>
                </p>
                <p>
                    <label for="pf2_schema_touristattraction_image_ids" class="screen-reader-text"><?php esc_html_e( 'ID Gambar (CSV)', 'pf2' ); ?></label>
                    <input type="text" id="pf2_schema_touristattraction_image_ids" name="pf2_schema_touristattraction_image_ids" class="regular-text" value="<?php echo esc_attr( $tourist_meta['image_ids'] ); ?>" placeholder="1,2,3" />
                </p>
                <fieldset>
                    <legend><?php esc_html_e( 'Koordinat', 'pf2' ); ?></legend>
                    <p><input type="text" name="pf2_schema_touristattraction_geo[latitude]" class="regular-text" placeholder="<?php esc_attr_e( 'Latitude', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $tourist_meta['geo']['latitude'] ) ? $tourist_meta['geo']['latitude'] : '' ); ?>" /></p>
                    <p><input type="text" name="pf2_schema_touristattraction_geo[longitude]" class="regular-text" placeholder="<?php esc_attr_e( 'Longitude', 'pf2' ); ?>" value="<?php echo esc_attr( isset( $tourist_meta['geo']['longitude'] ) ? $tourist_meta['geo']['longitude'] : '' ); ?>" /></p>
                </fieldset>
            </section>
        </div>
        <?php
    }
}

if ( ! function_exists( 'pf2_schema_classic_save' ) ) {
    /**
     * Persist classic meta box values.
     *
     * @param int $post_id Post identifier.
     * @return void
     */
    function pf2_schema_classic_save( $post_id ) {
        if ( ! isset( $_POST['pf2_schema_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['pf2_schema_meta_nonce'] ), 'pf2_schema_meta_nonce' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
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
            $value = isset( $_POST[ $checkbox ] ) ? (bool) wp_unslash( $_POST[ $checkbox ] ) : false;
            update_post_meta( $post_id, $checkbox, pf2_schema_meta_sanitize_boolean( $value ) );
        }

        $faq_lines = array();
        if ( isset( $_POST['pf2_schema_faq_items_raw'] ) ) {
            $raw_lines = explode( "\n", (string) wp_unslash( $_POST['pf2_schema_faq_items_raw'] ) );
            foreach ( $raw_lines as $line ) {
                $parts = array_map( 'trim', explode( '|', $line ) );
                if ( count( $parts ) < 2 ) {
                    continue;
                }
                $faq_lines[] = array(
                    'question' => $parts[0],
                    'answer'   => $parts[1],
                );
            }
        }
        update_post_meta( $post_id, 'pf2_schema_faq_items', pf2_schema_meta_sanitize_faq_items( $faq_lines ) );

        $howto_steps = array();
        if ( isset( $_POST['pf2_schema_howto_steps_raw'] ) ) {
            $raw_lines = explode( "\n", (string) wp_unslash( $_POST['pf2_schema_howto_steps_raw'] ) );
            foreach ( $raw_lines as $line ) {
                $parts = array_map( 'trim', explode( '|', $line ) );
                if ( empty( $parts ) ) {
                    continue;
                }

                $entry = array(
                    'name' => isset( $parts[0] ) ? $parts[0] : '',
                    'text' => isset( $parts[1] ) ? $parts[1] : '',
                );

                if ( isset( $parts[2] ) ) {
                    $entry['image_id'] = absint( $parts[2] );
                }

                $howto_steps[] = $entry;
            }
        }
        update_post_meta( $post_id, 'pf2_schema_howto_steps', pf2_schema_meta_sanitize_howto_steps( $howto_steps ) );
        $howto_name = isset( $_POST['pf2_schema_howto_name'] ) ? wp_unslash( $_POST['pf2_schema_howto_name'] ) : '';
        update_post_meta( $post_id, 'pf2_schema_howto_name', pf2_schema_meta_sanitize_string( $howto_name ) );

        $video_keys = array(
            'pf2_schema_video_url'         => 'pf2_schema_meta_sanitize_url',
            'pf2_schema_video_name'        => 'pf2_schema_meta_sanitize_string',
            'pf2_schema_video_description' => 'pf2_schema_meta_sanitize_textarea',
            'pf2_schema_video_upload_date' => 'pf2_schema_meta_sanitize_string',
        );

        foreach ( $video_keys as $meta_key => $callback ) {
            $value = isset( $_POST[ $meta_key ] ) ? wp_unslash( $_POST[ $meta_key ] ) : '';
            update_post_meta( $post_id, $meta_key, call_user_func( $callback, $value ) );
        }

        $thumbnail_id = isset( $_POST['pf2_schema_video_thumbnail_id'] ) ? wp_unslash( $_POST['pf2_schema_video_thumbnail_id'] ) : 0;
        update_post_meta( $post_id, 'pf2_schema_video_thumbnail_id', pf2_schema_meta_sanitize_int( $thumbnail_id ) );

        $service_type = isset( $_POST['pf2_schema_servicearea_type'] ) ? wp_unslash( $_POST['pf2_schema_servicearea_type'] ) : '';
        update_post_meta( $post_id, 'pf2_schema_servicearea_type', pf2_schema_meta_sanitize_servicearea_type( $service_type ) );

        $service_values = array();
        if ( isset( $_POST['pf2_schema_servicearea_values_raw'] ) ) {
            $lines = explode( "\n", (string) wp_unslash( $_POST['pf2_schema_servicearea_values_raw'] ) );
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( '' !== $line ) {
                    $service_values[] = $line;
                }
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

        $tourist_fields = array(
            'pf2_schema_touristattraction_name'        => 'pf2_schema_meta_sanitize_string',
            'pf2_schema_touristattraction_description' => 'pf2_schema_meta_sanitize_textarea',
        );

        foreach ( $tourist_fields as $meta_key => $callback ) {
            $value = isset( $_POST[ $meta_key ] ) ? wp_unslash( $_POST[ $meta_key ] ) : '';
            update_post_meta( $post_id, $meta_key, call_user_func( $callback, $value ) );
        }

        $tourist_images = isset( $_POST['pf2_schema_touristattraction_image_ids'] ) ? wp_unslash( $_POST['pf2_schema_touristattraction_image_ids'] ) : '';
        update_post_meta( $post_id, 'pf2_schema_touristattraction_image_ids', pf2_schema_meta_sanitize_csv_ids( $tourist_images ) );

        $tourist_geo = isset( $_POST['pf2_schema_touristattraction_geo'] ) ? wp_unslash( $_POST['pf2_schema_touristattraction_geo'] ) : array();
        if ( is_array( $tourist_geo ) ) {
            update_post_meta( $post_id, 'pf2_schema_touristattraction_geo', pf2_schema_meta_sanitize_tourist_geo( $tourist_geo ) );
        }
    }
}
add_action( 'save_post', 'pf2_schema_classic_save' );
