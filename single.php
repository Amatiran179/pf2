<?php if (!defined('ABSPATH')) exit; get_header(); ?>
<main id="content" class="site-main" role="main">
<div class="content-area with-sidebar"><div class="site-main">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header"><h1 class="entry-title"><?php the_title(); ?></h1></header>
  <?php if (has_post_thumbnail()): ?><div class="entry-thumb"><?php the_post_thumbnail('large',['loading'=>'lazy','decoding'=>'async']); ?></div><?php endif; ?>
  <div class="entry-content"><?php the_content(); ?></div>
  <footer class="entry-footer"><?php the_post_navigation(); ?></footer>
</article>
<?php comments_template(); endwhile; else: ?>
<p><?php echo esc_html__('No content found.', 'pf2'); ?></p>
<?php endif; ?></div><?php get_sidebar(); ?></div>
</main>
<?php get_footer(); ?>
