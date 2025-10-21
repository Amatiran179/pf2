<?php
/**
 * Default site header template.
 *
 * Renders branding and the primary navigation with sensible fallbacks so the
 * theme stays functional even before menus are configured.
 *
 * @package PF2\TemplateParts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name        = get_bloginfo( 'name' );
$site_description = get_bloginfo( 'description', 'display' );
$has_menu         = has_nav_menu( 'primary' );
?>
<header id="masthead" class="site-header" role="banner">
	<div class="pf2-container site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php endif; ?>

			<div class="site-identity">
				<?php if ( is_front_page() && is_home() ) : ?>
					<h1 class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php echo esc_html( $site_name ); ?>
						</a>
					</h1>
				<?php else : ?>
					<p class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php echo esc_html( $site_name ); ?>
						</a>
					</p>
				<?php endif; ?>

				<?php if ( $site_description ) : ?>
					<p class="site-description"><?php echo esc_html( $site_description ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<nav id="site-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'pf2' ); ?>">
			<?php
			if ( $has_menu ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'menu_class'     => 'site-navigation',
						'fallback_cb'    => '__return_empty_string',
						'depth'          => 2,
					)
				);
			} else {
				$page_menu = wp_page_menu(
					array(
						'echo'       => false,
						'menu_class' => 'site-navigation',
						'show_home'  => true,
					)
				);

				if ( is_string( $page_menu ) && '' !== $page_menu ) {
					$clean_menu = preg_replace( '#^<div[^>]*>#', '', $page_menu );
					if ( is_string( $clean_menu ) ) {
						$clean_menu = preg_replace( '#</div>$#', '', $clean_menu );
					}

					if ( is_string( $clean_menu ) && '' !== $clean_menu ) {
						echo wp_kses_post( $clean_menu );
					}
				}
			}
			?>
		</nav>
	</div>
</header>
