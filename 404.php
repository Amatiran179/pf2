<?php if (!defined('ABSPATH')) exit; get_header(); ?>
<main id="content" class="site-main" role="main">
<div class="content-area"><div class="site-main">
<section class="error-404 not-found">
  <h1 class="page-title"><?php echo esc_html__('Page not found','pf2'); ?></h1>
  <p><?php echo esc_html__('It looks like nothing was found at this location. Maybe try a search?','pf2'); ?></p>
  <?php get_search_form(); ?>
  <p><a href="<?php echo esc_url(home_url('/')); ?>" class="button"><?php echo esc_html__('Back to Home','pf2'); ?></a></p>
</section>
</div></div>
</main>
<?php get_footer(); ?>
