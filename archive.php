<?php if (!defined('ABSPATH')) exit; get_header(); ?>
<main id="content" class="site-main" role="main">
<div class="content-area with-sidebar"><div class="site-main">
<header class="page-header">
  <h1 class="page-title"><?php the_archive_title(); ?></h1>
  <div class="archive-description"><?php the_archive_description(); ?></div>
</header>
<?php if (have_posts()): ?><div class="pf2-grid">
<?php while (have_posts()): the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('pf2-card'); ?>>
  <a href="<?php the_permalink(); ?>" class="pf2-card__link" aria-label="<?php echo esc_attr(get_the_title()); ?>">
    <?php if (has_post_thumbnail()): ?><div class="pf2-card__thumb"><?php the_post_thumbnail('medium_large',['loading'=>'lazy','decoding'=>'async']); ?></div><?php endif; ?>
    <h2 class="pf2-card__title"><?php the_title(); ?></h2>
    <div class="pf2-card__excerpt"><?php the_excerpt(); ?></div>
  </a>
</article>
<?php endwhile; ?></div>
<?php the_posts_pagination(['screen_reader_text'=>esc_html__('Posts navigation','pf2')]); ?>
<?php else: ?><p><?php echo esc_html__('No content found.','pf2'); ?></p><?php endif; ?>
</div><?php get_sidebar(); ?></div>
</main>
<?php get_footer(); ?>
