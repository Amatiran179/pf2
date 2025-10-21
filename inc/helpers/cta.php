<?php
/**
 * Call-to-action helper functions.
 *
 * Provides rendering utilities, shortcode integration, and placement logic
 * for inline, floating, and modal CTAs.
 *
 * @package PF2\Helpers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default CTA attributes.
 *
 * @return array<string, mixed>
 */
function pf2_cta_get_defaults() {
	$text_default  = __( 'Konsultasi via WhatsApp', 'pf2' );
	$phone_default = '628111111111';

	if ( function_exists( 'pf2_options_get' ) ) {
		$option_text = (string) pf2_options_get( 'cta_text', $text_default );
		if ( '' !== $option_text ) {
			$text_default = $option_text;
		}

		$option_phone = (string) pf2_options_get( 'phone_wa', '' );
		if ( '' !== $option_phone ) {
			$phone_default = $option_phone;
		}
	}

	$phone_default = preg_replace( '/[^0-9+]/', '', $phone_default );

	$defaults = array(
		'type'        => 'inline',
		'text'        => $text_default,
		'phone'       => $phone_default,
		'icon'        => 'whatsapp',
		'class'       => 'pf2-cta pf2-cta--primary',
		'context'     => array(),
		'post_id'     => 0,
		'trigger_text' => __( 'Buka Konsultasi', 'pf2' ),
	);

	/**
	 * Filter the default CTA configuration.
	 *
	 * @param array $defaults Default CTA values.
	 */
	return apply_filters( 'pf2_cta_defaults', $defaults );
}


/**
 * Determine whether a CTA should render for the provided context.
 *
 * @param array<string, mixed> $context Context describing the CTA request.
 * @return bool
 */
function pf2_cta_should_render( $context = array() ) {
	$should_render = true;

	if ( function_exists( 'pf2_options_get' ) ) {
		$global_enabled = (int) pf2_options_get( 'cta_enabled', 1 );
		if ( ! $global_enabled ) {
			$should_render = false;
		} elseif ( isset( $context['type'] ) && 'floating' === $context['type'] ) {
			$floating_enabled = (int) pf2_options_get( 'cta_floating_enabled', 1 );
			if ( ! $floating_enabled ) {
				$should_render = false;
			}
		}
	}

	if ( isset( $context['post_id'] ) && $context['post_id'] ) {
	        $post_status = get_post_status( (int) $context['post_id'] );
	        if ( 'publish' !== $post_status ) {
	                $should_render = false;
	        }
	}

	if ( isset( $context['post_id'] ) && $context['post_id'] ) {
	        if ( post_password_required( (int) $context['post_id'] ) ) {
	                $should_render = false;
	        }
	} elseif ( post_password_required() ) {
	        $should_render = false;
	}

	/**
	 * Filter CTA rendering eligibility.
	 *
	 * @param bool  $should_render Whether the CTA should render.
	 * @param array $context       Context data.
	 */
	return (bool) apply_filters( 'pf2_cta_should_render', $should_render, $context );
}

/**
 * Render a CTA component.
 *
 * @param array<string, mixed> $args CTA arguments.
 * @return string
 */
function pf2_cta_render( $args = array() ) {
	$defaults = pf2_cta_get_defaults();
	$args     = wp_parse_args( $args, $defaults );

	$type = isset( $args['type'] ) ? sanitize_key( $args['type'] ) : 'inline';
	if ( ! in_array( $type, array( 'inline', 'floating', 'modal' ), true ) ) {
	        $type = 'inline';
	}

	$post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();

	$context = array_merge(
	        array(
	                'type'     => $type,
	                'location' => $type,
	                'post_id'  => $post_id,
	        ),
	        is_array( $args['context'] ) ? $args['context'] : array()
	);

	if ( ! pf2_cta_should_render( $context ) ) {
	        return '';
	}

	$text       = isset( $args['text'] ) ? sanitize_text_field( $args['text'] ) : $defaults['text'];
	$phone      = isset( $args['phone'] ) ? preg_replace( '/[^0-9]/', '', (string) $args['phone'] ) : $defaults['phone'];
	$icon       = isset( $args['icon'] ) ? sanitize_key( $args['icon'] ) : $defaults['icon'];
	$class_list  = isset( $args['class'] ) && $args['class'] ? $args['class'] : $defaults['class'];
	$class_tokens = preg_split( '/\s+/', (string) $class_list );
	$class_tokens = is_array( $class_tokens ) ? $class_tokens : array();
	$class_list   = array_filter( array_map( 'sanitize_html_class', $class_tokens ) );
	$class        = implode( ' ', $class_list );

	if ( empty( $class ) ) {
	        $class = 'pf2-cta pf2-cta--primary';
	}

	$class = apply_filters( 'pf2_cta_class', $class, $type, $context );

	if ( empty( $text ) ) {
	        $text = $defaults['text'];
	}

	if ( empty( $phone ) ) {
	        $phone = $defaults['phone'];
	}

	$trigger_text = isset( $args['trigger_text'] ) ? sanitize_text_field( $args['trigger_text'] ) : $defaults['trigger_text'];
	$trigger_text = apply_filters( 'pf2_cta_trigger_text', $trigger_text, $type, $context );

	$text  = apply_filters( 'pf2_cta_text', $text, $type, $context );
	$phone = apply_filters( 'pf2_cta_phone', $phone, $type, $context );

	$templates = array(
	        'inline'   => 'inc/templates/cta/inline.php',
	        'floating' => 'inc/templates/cta/floating.php',
	        'modal'    => 'inc/templates/cta/modal.php',
	);

	$template_path = locate_template( $templates[ $type ], false, false );

	if ( ! $template_path ) {
	        return '';
	}

	$cta = array(
	        'type'         => $type,
	        'text'         => $text,
	        'phone'        => $phone,
	        'icon'         => $icon,
	        'class'        => $class,
	        'post_id'      => $post_id,
	        'trigger_text' => $trigger_text,
	        'context'      => $context,
	);

	ob_start();
	include $template_path;
	$html = ob_get_clean();

	/**
	 * Fires after CTA markup has been generated.
	 *
	 * @param array $cta CTA metadata.
	 */
	do_action( 'pf2_cta_rendered', $cta );

	/**
	 * Filter CTA markup before returning.
	 *
	 * @param string $html CTA markup.
	 * @param array  $cta  CTA arguments.
	 */
	return apply_filters( 'pf2_cta_render', $html, $cta );
}

/**
 * Shortcode handler for `[pf2_cta]`.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function pf2_cta_shortcode( $atts ) {
	$atts = shortcode_atts(
	        array(
	                'type'   => 'inline',
	                'text'   => '',
	                'phone'  => '',
	                'icon'   => 'whatsapp',
	                'class'  => '',
	        ),
	        $atts,
	        'pf2_cta'
	);

	return pf2_cta_render(
	        array(
	                'type'    => $atts['type'],
	                'text'    => $atts['text'],
	                'phone'   => $atts['phone'],
	                'icon'    => $atts['icon'],
	                'class'   => $atts['class'],
	                'context' => array(
	                        'source'    => 'shortcode',
	                        'shortcode' => array(
	                                'type'  => sanitize_key( $atts['type'] ),
	                                'phone' => preg_replace( '/[^0-9]/', '', $atts['phone'] ),
	                        ),
	                ),
	        )
	);
}
add_shortcode( 'pf2_cta', 'pf2_cta_shortcode' );

/**
 * Append inline CTA to the_content for supported post types.
 *
 * @param string $content The post content.
 * @return string
 */
function pf2_cta_append_inline_to_content( $content ) {
	if ( function_exists( 'pf2_options_get' ) && ! (int) pf2_options_get( 'cta_enabled', 1 ) ) {
		return $content;
	}

	if ( ! is_singular( array( 'pf2_product', 'pf2_portfolio' ) ) ) {
		return $content;
	}

	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$post_type = get_post_type();

	if ( ! apply_filters( 'pf2_cta_inline_enabled', true, $post_type ) ) {
		return $content;
	}

	$cta_markup = pf2_cta_render(
		array(
			'type'    => 'inline',
			'post_id' => get_the_ID(),
			'context' => array(
				'placement' => 'the_content',
				'post_type' => $post_type,
			),
		)
	);

	if ( $cta_markup ) {
		$content .= $cta_markup;
	}

	return $content;
}

add_filter( 'the_content', 'pf2_cta_append_inline_to_content', 15 );
