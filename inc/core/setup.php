<?php
if (!defined('ABSPATH')) { exit; }

add_action('after_setup_theme', function(){
    load_theme_textdomain('pf2', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','gallery','caption','style','script']);
    add_theme_support('responsive-embeds');
    register_nav_menus([
        'primary' => __('Primary Menu','pf2'),
        'footer'  => __('Footer Menu','pf2'),
    ]);
});
