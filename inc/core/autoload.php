<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Very light PSR-4-like autoloader (theme scope)
 * Namespace: PF2\
 */
spl_autoload_register(function($class){
    if (strpos($class, 'PF2\\') !== 0) return;
    $path = get_template_directory() . '/inc/' . strtolower(str_replace(['PF2\\','\\'], ['','/'], $class)) . '.php';
    if (file_exists($path)) require_once $path;
});
