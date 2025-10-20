<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Vite-aware enqueue: dev via HMR, prod via built assets
 */
add_action('wp_enqueue_scripts', function(){
    $theme = wp_get_theme();
    $ver = $theme->get('Version');

    $vite_dev = defined('PF2_VITE_DEV') ? PF2_VITE_DEV : false;
    $dev_host = defined('PF2_VITE_DEV_HOST') ? PF2_VITE_DEV_HOST : 'http://localhost:5173';

    if ($vite_dev) {
        // Dev (HMR)
        wp_enqueue_script('pf2-front', $dev_host . '/assets/js/front.js', [], null, true);
        wp_enqueue_style('pf2-front', $dev_host . '/assets/css/front.css', [], null);
    } else {
        // Prod (built) – adjust paths if using manifest
        $uri = get_template_directory_uri();
        wp_enqueue_style('pf2-front', $uri . '/assets/css/front.css', [], $ver);
        wp_enqueue_script('pf2-front', $uri . '/assets/js/front.js', [], $ver, true);
    }
}, 20);

add_action('admin_enqueue_scripts', function($hook){
    $theme = wp_get_theme();
    $ver = $theme->get('Version');
    $uri = get_template_directory_uri();
    wp_enqueue_style('pf2-admin', $uri . '/assets/css/admin.css', [], $ver);
    wp_enqueue_script('pf2-admin', $uri . '/assets/js/admin.js', ['jquery'], $ver, true);
});
