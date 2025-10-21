<?php if (!defined('ABSPATH')) exit; get_header(); ?>
<main id="content" class="site-main" role="main">
<div class="content-area"><div class="site-main">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header"><h1 class="entry-title"><?php the_title(); ?></h1></header>
  <?php if (has_post_thumbnail()): ?><div class="entry-thumb"><?php the_post_thumbnail('large',['loading'=>'lazy','decoding'=>'async']); ?></div><?php endif; ?>
  <div class="entry-content"><?php the_content(); ?></div>
</article>
<?php comments_template(); endwhile; endif; ?></div></div>
</main>
<?php get_footer(); ?>
