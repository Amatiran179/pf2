<?php
/**
 * PF2 Gallery meta registration and classic metabox implementation.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_gallery_sanitize_csv' ) ) {
        /**
         * Normalise gallery IDs into a CSV string.
         *
         * @param mixed $value Raw value.
         * @return string
         */
        function pf2_gallery_sanitize_csv( $value ) {
                $items = array();

                if ( is_string( $value ) ) {
                        $items = preg_split( '/,/', $value );
                } elseif ( is_array( $value ) ) {
                        $items = $value;
                }

                if ( ! is_array( $items ) ) {
                        return '';
                }

                $ids   = array();
                $guard = array();

                foreach ( $items as $item ) {
                        $item = is_scalar( $item ) ? (string) $item : '';
                        $id   = absint( trim( $item ) );

                        if ( $id && ! isset( $guard[ $id ] ) ) {
                                $ids[]          = (string) $id;
                                $guard[ $id ] = true;
                        }
                }

                return $ids ? implode( ',', $ids ) : '';
        }
}

if ( ! function_exists( 'pf2_gallery_register_meta' ) ) {
        /**
         * Register gallery post meta for PF2 CPTs.
         *
         * @return void
         */
        function pf2_gallery_register_meta() {
                $post_types = array( 'pf2_product', 'pf2_portfolio' );

                foreach ( $post_types as $post_type ) {
                        register_post_meta(
                                $post_type,
                                'pf2_gallery_ids',
                                array(
                                        'type'              => 'string',
                                        'single'            => true,
                                        'show_in_rest'      => true,
                                        'default'           => '',
                                        'sanitize_callback' => 'pf2_gallery_sanitize_csv',
                                        'auth_callback'     => 'pf2_meta_auth_callback',
                                )
                        );
                }
        }
}
add_action( 'init', 'pf2_gallery_register_meta' );

if ( ! function_exists( 'pf2_gallery_parse_ids' ) ) {
        /**
         * Convert a CSV string into an array of IDs.
         *
         * @param string $csv Csv string.
         * @return array<int>
         */
        function pf2_gallery_parse_ids( $csv ) {
                if ( '' === $csv ) {
                        return array();
                }

                $parts = preg_split( '/,/', $csv );

                if ( ! is_array( $parts ) ) {
                        return array();
                }

                $ids   = array();
                $guard = array();

                foreach ( $parts as $part ) {
                        $id = absint( trim( (string) $part ) );

                        if ( $id && ! isset( $guard[ $id ] ) ) {
                                $ids[]          = $id;
                                $guard[ $id ] = true;
                        }
                }

                return $ids;
        }
}

if ( ! function_exists( 'pf2_gallery_prepare_attachment' ) ) {
        /**
         * Prepare attachment data for classic metabox rendering.
         *
         * @param int $attachment_id Attachment ID.
         * @return array<string, string>
         */
        function pf2_gallery_prepare_attachment( $attachment_id ) {
                $attachment_id = absint( $attachment_id );

                if ( ! $attachment_id ) {
                        return array();
                }

                $thumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
                $alt   = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                $title = get_the_title( $attachment_id );

                return array(
                        'id'    => (string) $attachment_id,
                        'thumb' => $thumb ? $thumb[0] : '',
                        'alt'   => $alt ? wp_strip_all_tags( (string) $alt ) : wp_strip_all_tags( (string) $title ),
                );
        }
}

if ( ! function_exists( 'pf2_gallery_render_metabox' ) ) {
        /**
         * Render the PF2 Gallery metabox markup.
         *
         * @param \WP_Post $post Post object.
         * @return void
         */
        function pf2_gallery_render_metabox( $post ) {
                wp_nonce_field( 'pf2_gallery_save', 'pf2_gallery_nonce' );

                $raw_value = get_post_meta( $post->ID, 'pf2_gallery_ids', true );
                $value     = is_string( $raw_value ) ? $raw_value : '';
                $ids       = pf2_gallery_parse_ids( $value );

                $attachments = array();

                foreach ( $ids as $attachment_id ) {
                        $attachments[] = pf2_gallery_prepare_attachment( $attachment_id );
                }

                $csv_value = $ids ? implode( ',', array_map( 'strval', $ids ) ) : '';
                ?>
                <div class="pf2-gallery-metabox" data-pf2-gallery-metabox data-gallery-ids="<?php echo esc_attr( $csv_value ); ?>">
                        <p class="pf2-gallery-metabox__actions">
                                <button type="button" class="button pf2-gallery-metabox__add">
                                        <?php esc_html_e( 'Upload/Select Images', 'pf2' ); ?>
                                </button>
                                <button type="button" class="button-link pf2-gallery-metabox__clear" <?php echo empty( $ids ) ? 'disabled' : ''; ?>>
                                        <?php esc_html_e( 'Kosongkan galeri', 'pf2' ); ?>
                                </button>
                        </p>
                        <p class="pf2-gallery-metabox__empty"<?php echo empty( $ids ) ? '' : ' hidden'; ?>><?php esc_html_e( 'Belum ada gambar yang dipilih.', 'pf2' ); ?></p>
                        <ul class="pf2-gallery-preview pf2-gallery-metabox__list"<?php echo empty( $ids ) ? ' hidden' : ''; ?>>
                                <?php foreach ( $attachments as $attachment ) : ?>
                                        <li class="pf2-gallery-preview__item" draggable="true" data-id="<?php echo esc_attr( $attachment['id'] ); ?>" data-thumb="<?php echo esc_url( $attachment['thumb'] ); ?>" data-alt="<?php echo esc_attr( $attachment['alt'] ); ?>">
                                                <span class="pf2-gallery-preview__handle" aria-hidden="true">⇅</span>
                                                <?php if ( $attachment['thumb'] ) : ?>
                                                        <img src="<?php echo esc_url( $attachment['thumb'] ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>" class="pf2-gallery-preview__thumbnail" />
                                                <?php else : ?>
                                                        <span class="pf2-gallery-preview__placeholder"><?php esc_html_e( 'Tidak ada pratinjau', 'pf2' ); ?></span>
                                                <?php endif; ?>
                                                <button type="button" class="button-link pf2-gallery-preview__remove">
                                                        <?php esc_html_e( 'Hapus', 'pf2' ); ?>
                                                </button>
                                        </li>
                                <?php endforeach; ?>
                        </ul>
                        <p class="pf2-gallery-metabox__hint"><?php esc_html_e( 'Tarik untuk mengurutkan ulang gambar.', 'pf2' ); ?></p>
                        <input type="hidden" class="pf2-gallery-metabox__field" name="pf2_gallery_ids" value="<?php echo esc_attr( $csv_value ); ?>" />
                </div>
                <?php
        }
}

if ( ! function_exists( 'pf2_gallery_add_metabox' ) ) {
        /**
         * Register the PF2 Gallery metabox for supported post types.
         *
         * @return void
         */
        function pf2_gallery_add_metabox() {
                $post_types = array( 'pf2_product', 'pf2_portfolio' );

                foreach ( $post_types as $post_type ) {
                        add_meta_box(
                                'pf2-gallery-metabox',
                                esc_html__( 'PF2 Gallery', 'pf2' ),
                                'pf2_gallery_render_metabox',
                                $post_type,
                                'side',
                                'default'
                        );
                }
        }
}
add_action( 'add_meta_boxes', 'pf2_gallery_add_metabox' );

if ( ! function_exists( 'pf2_gallery_save_meta' ) ) {
        /**
         * Persist PF2 gallery IDs when saving a post.
         *
         * @param int     $post_id Post identifier.
         * @param \WP_Post $post    Post object.
         * @return void
         */
        function pf2_gallery_save_meta( $post_id, $post ) {
                unset( $post );

                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                        return;
                }

                if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
                        return;
                }

                if ( ! isset( $_POST['pf2_gallery_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
                        return;
                }

                $nonce = sanitize_text_field( wp_unslash( $_POST['pf2_gallery_nonce'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

                if ( ! wp_verify_nonce( $nonce, 'pf2_gallery_save' ) ) {
                        return;
                }

                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                        return;
                }

                $raw_value = isset( $_POST['pf2_gallery_ids'] ) ? wp_unslash( $_POST['pf2_gallery_ids'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
                $sanitised = pf2_gallery_sanitize_csv( $raw_value );

                update_post_meta( $post_id, 'pf2_gallery_ids', $sanitised );
        }
}
add_action( 'save_post_pf2_product', 'pf2_gallery_save_meta', 10, 2 );
add_action( 'save_post_pf2_portfolio', 'pf2_gallery_save_meta', 10, 2 );
