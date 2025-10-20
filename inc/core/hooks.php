<?php
if (!defined('ABSPATH')) { exit; }

/** Example hooks place */
add_action('init', function(){
    // Placeholder for CPT registrations in later batches
});

/** Basic filters */
add_filter('the_content', function($content){
    return $content;
});
