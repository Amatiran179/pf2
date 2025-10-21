<?php
/**
 * Inline CTA template.
 *
 * @package PF2\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta = isset( $cta ) ? $cta : array();

$type    = isset( $cta['type'] ) ? $cta['type'] : 'inline';
$text    = isset( $cta['text'] ) ? $cta['text'] : '';
$phone   = isset( $cta['phone'] ) ? $cta['phone'] : '';
$class   = isset( $cta['class'] ) ? $cta['class'] : 'pf2-cta pf2-cta--inline';
$post_id = isset( $cta['post_id'] ) ? (int) $cta['post_id'] : 0;
$context      = isset( $cta['context'] ) ? $cta['context'] : array();
$context_data = '';

if ( $context ) {
	$encoded       = wp_json_encode( $context );
	$context_data = $encoded ? $encoded : '';
}

$raw_classes = preg_split( '/\s+/', $class );
$raw_classes = is_array( $raw_classes ) ? $raw_classes : array();
$classes    = implode( ' ', array_filter( array_map( 'sanitize_html_class', $raw_classes ) ) );

$wa_link = sprintf(
	'https://wa.me/%1$s?text=%2$s',
	rawurlencode( $phone ),
	rawurlencode( $text )
);
?>
<div class="pf2-cta-inline">
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
