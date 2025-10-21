<?php
/**
 * Default hero template.
 *
 * @package PF2\TemplateParts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$blog_name        = get_bloginfo( 'name' );
$blog_description = get_bloginfo( 'description', 'display' );
$hero_title       = $blog_name;
$hero_subtitle    = $blog_description;

if ( function_exists( 'pf2_options_get' ) ) {
	$option_title    = (string) pf2_options_get( 'hero_title', $hero_title );
	$option_subtitle = (string) pf2_options_get( 'hero_subtitle', $hero_subtitle );

	$hero_title    = '' !== $option_title ? $option_title : $blog_name;
	$hero_subtitle = '' !== $option_subtitle ? $option_subtitle : $blog_description;
}
?>
<section class="pf2-hero pf2-hero--default" aria-labelledby="pf2-hero-title" style="background-image: linear-gradient(135deg, var(--pf2-hero-gradient-start, #0ea5e9), var(--pf2-hero-gradient-end, #1e3a8a));">
	<div class="pf2-container">
		<div class="pf2-hero__content">
			<h1 id="pf2-hero-title" class="pf2-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
			<?php if ( $hero_subtitle ) : ?>
				<p class="pf2-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
