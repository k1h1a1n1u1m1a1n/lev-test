<?php
/*
Plugin Name: Products Selection
*/

add_action('init', function () {
    add_shortcode('products-selection', function () {
        ob_start();
        require_once "shortcode-content.php";
        return ob_get_clean();
    });
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('products-selection-style', plugin_dir_url(__FILE__) . "/style.css");
});

