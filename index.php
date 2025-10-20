<?php
if (!defined('ABSPATH')) { exit; }
get_header(); ?>
<main id="primary" class="site-main">
  <div class="container" style="padding:32px 16px;max-width:1100px;margin:0 auto;">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <article <?php post_class('pf2-card'); ?>>
        <h1><?php the_title(); ?></h1>
        <div class="entry"><?php the_content(); ?></div>
      </article>
    <?php endwhile; else: ?>
      <p><?php esc_html_e('No content found.', 'pf2'); ?></p>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
