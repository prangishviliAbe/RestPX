<?php
/**
 * Plugin Name: RestPX
 * Description: Seamlessly fetch and display posts from external WordPress sites via REST API. Features smart URL parsing for automatic Category and Language (Polylang) detection, fully responsive cards, and a dedicated Elementor widget.
 * Version: 1.5
 * Author: Abe Prangishvili
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define constants
define('REST_API_POSTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('REST_API_POSTS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the shortcode functionality
require_once REST_API_POSTS_PLUGIN_PATH . 'includes/rest-api-posts-shortcode.php';

// Include the Elementor widget functionality
require_once REST_API_POSTS_PLUGIN_PATH . 'includes/rest-api-posts-widget.php';

// Enqueue styles
function rest_api_posts_enqueue_styles() {
    wp_enqueue_style(
        'rest-api-posts-style', 
        REST_API_POSTS_PLUGIN_URL . 'assets/css/rest-api-posts.css', 
        [], 
        '1.5.0'
    );
}
add_action('wp_enqueue_scripts', 'rest_api_posts_enqueue_styles');