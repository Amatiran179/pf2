<?php if (!defined('ABSPATH')) exit; get_header(); ?>
<main id="content" class="site-main" role="main">
<div class="content-area with-sidebar"><div class="site-main">
<header class="page-header"><h1 class="page-title">
<?php printf(esc_html__('Search results for: %s','pf2'), '<span>'.esc_html(get_search_query()).'</span>'); ?>
</h1></header>
<?php if (have_posts()): while (have_posts()): the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
  <div class="entry-summary"><?php the_excerpt(); ?></div>
</article>
<?php endwhile; the_posts_pagination(['screen_reader_text'=>esc_html__('Posts navigation','pf2')]); else: ?>
<p><?php echo esc_html__('No results found. Please try another search.','pf2'); ?></p><?php get_search_form(); ?>
<?php endif; ?></div><?php get_sidebar(); ?></div>
</main>
<?php get_footer(); ?>
