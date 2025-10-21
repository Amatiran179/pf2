<?php
/**
 * Modal CTA template.
 *
 * @package PF2\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta = isset( $cta ) ? $cta : array();

$type         = isset( $cta['type'] ) ? $cta['type'] : 'modal';
$text         = isset( $cta['text'] ) ? $cta['text'] : '';
$phone        = isset( $cta['phone'] ) ? $cta['phone'] : '';
$class        = isset( $cta['class'] ) ? $cta['class'] : 'pf2-cta pf2-cta--modal';
$post_id      = isset( $cta['post_id'] ) ? (int) $cta['post_id'] : 0;
$trigger_text = isset( $cta['trigger_text'] ) ? $cta['trigger_text'] : __( 'Buka Konsultasi', 'pf2' );

$context      = isset( $cta['context'] ) ? $cta['context'] : array();
$context_data = '';

if ( $context ) {
	$encoded       = wp_json_encode( $context );
	$context_data = $encoded ? $encoded : '';
}

$raw_classes = preg_split( '/\s+/', $class );
$raw_classes = is_array( $raw_classes ) ? $raw_classes : array();
$classes    = implode( ' ', array_filter( array_map( 'sanitize_html_class', $raw_classes ) ) );

$modal_key = 'modal';
if ( $post_id ) {
	$modal_key .= '-' . $post_id;
}

$modal_container_id   = 'pf2-cta-modal-container-' . $modal_key;
$heading_id           = 'pf2-cta-modal-title-' . $modal_key;
$description_id       = 'pf2-cta-modal-description-' . $modal_key;
$dialog_label_id      = 'pf2-cta-modal-label-' . $modal_key;
$dialog_describedby   = trim( $description_id . ' ' . $heading_id );

$wa_link = sprintf(
	'https://wa.me/%1$s?text=%2$s',
	rawurlencode( $phone ),
	rawurlencode( $text )
);
?>
<button
        type="button"
        class="pf2-cta-modal__trigger"
        data-pf2-cta-open="<?php echo esc_attr( $modal_key ); ?>"
        aria-haspopup="dialog"
        aria-expanded="false"
        aria-controls="<?php echo esc_attr( $modal_container_id ); ?>"
>
        <?php echo esc_html( $trigger_text ); ?>
</button>
<div
        class="pf2-cta-modal"
        data-pf2-cta-modal="<?php echo esc_attr( $modal_key ); ?>"
        aria-hidden="true"
        hidden
        id="<?php echo esc_attr( $modal_container_id ); ?>"
>
        <div class="pf2-cta-modal__overlay" data-pf2-cta-modal-close aria-hidden="true"></div>
        <div
                class="pf2-cta-modal__dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="<?php echo esc_attr( $dialog_label_id ); ?>"
                aria-describedby="<?php echo esc_attr( $dialog_describedby ); ?>"
                tabindex="-1"
                data-pf2-cta-modal-dialog
        >
                <button
                        type="button"
                        class="pf2-cta-modal__close"
                        aria-label="<?php esc_attr_e( 'Tutup dialog CTA', 'pf2' ); ?>"
                        data-pf2-cta-modal-close
                >
                        <span aria-hidden="true">&times;</span>
                </button>
                <h2 id="<?php echo esc_attr( $dialog_label_id ); ?>" class="sr-only">
                        <?php esc_html_e( 'Quick Contact', 'pf2' ); ?>
                </h2>
                <p id="<?php echo esc_attr( $description_id ); ?>" class="sr-only">
                        <?php esc_html_e( 'Use this dialog to contact us via WhatsApp.', 'pf2' ); ?>
                </p>
                <div class="pf2-cta-modal__content" tabindex="-1" data-pf2-cta-modal-initial>
                        <h3 class="pf2-cta-modal__title" id="<?php echo esc_attr( $heading_id ); ?>">
                                <?php echo esc_html( $text ); ?>
                        </h3>
                        <a
                                class="<?php echo esc_attr( $classes ); ?>"
                                href="<?php echo esc_url( $wa_link ); ?>"
                                target="_blank"
                                rel="nofollow noopener"
	                        data-pf2-cta="<?php echo esc_attr( $type ); ?>"
	                        data-pf2-cta-phone="<?php echo esc_attr( $phone ); ?>"
	                        data-pf2-cta-text="<?php echo esc_attr( $text ); ?>"
	                        data-pf2-cta-post="<?php echo esc_attr( $post_id ); ?>"
	<?php if ( $context_data ) : ?>
	                        data-pf2-cta-context="<?php echo esc_attr( $context_data ); ?>"
	<?php endif; ?>
	                >
	                        <?php echo esc_html( $text ); ?>
	                </a>
	        </div>
	</div>
</div>
