<?php if (!defined('ABSPATH')) exit;
add_action('after_setup_theme', function(){
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
  add_theme_support('responsive-embeds');
  register_nav_menus([
    'primary' => __('Primary Menu','pf2'),
    'footer'  => __('Footer Menu','pf2'),
  ]);
  load_theme_textdomain('pf2', get_template_directory().'/languages');
  if (!isset($GLOBALS['content_width'])) $GLOBALS['content_width'] = 1200;
});
add_action('widgets_init', function(){
  register_sidebar([
    'name'          => __('Sidebar','pf2'),
    'id'            => 'sidebar-1',
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ]);
});
