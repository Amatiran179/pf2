<?php
/**
 * Front Page
 * Minimal hero + sections placeholder
 */
if (!defined('ABSPATH')) { exit; }
get_header(); ?>

<main id="primary" class="site-main">
  <?php get_template_part('template-parts/hero/hero', 'default'); ?>

  <section class="container" style="padding:48px 16px;max-width:1100px;margin:0 auto;">
    <h2><?php esc_html_e('Latest Portfolio', 'pf2'); ?></h2>
    <div class="pf2-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
      <?php
      $q = new WP_Query(['post_type'=>'portfolio','posts_per_page'=>6]);
      if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
        <?php get_template_part('template-parts/portfolio/loop', 'item'); ?>
      <?php endwhile; wp_reset_postdata(); else: ?>
        <p><?php esc_html_e('No portfolio yet.', 'pf2'); ?></p>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
