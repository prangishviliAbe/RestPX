<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Shortcode to display posts from external site
add_shortcode('rest_api_posts', 'get_posts_from_external_site');

function get_posts_from_external_site($atts) {
    // Set default attributes
    $atts = shortcode_atts([
        'url' => '',    // External site URL for REST API
        'count' => 12,  // Total number of posts to display
        'lang' => 'en', // Language filter (default: English)
        'grid' => 3,    // Number of columns (1-4)
        'style' => 'default', // Card style: default, minimal, overlay
    ], $atts, 'rest_api_posts');

    if (empty($atts['url'])) {
        return '<p>Please provide the URL of the external site.</p>';
    }

    // Instantiate API Handler
    $api_handler = new RestPX_API();
    
    // Parse URL for metadata
    $parsed_data = $api_handler->parse_url_for_metadata($atts['url'], $atts['lang']);
    
    if (is_wp_error($parsed_data)) {
        return '<p>' . $parsed_data->get_error_message() . '</p>';
    }

    $base_url = $parsed_data['base_url'];
    $lang = $parsed_data['lang']; // Extracted or default
    $category_slug = $parsed_data['category_slug'];
    $category_id = null;

    // Resolve Category ID if slug is present
    if ($category_slug) {
        $category_id = $api_handler->get_category_id($base_url, $category_slug, $lang);
    }

    // Fetch Posts
    $posts = $api_handler->fetch_posts($base_url, $atts['count'], $lang, $category_id);

    if (is_wp_error($posts)) {
        return '<p>' . $posts->get_error_message() . '</p>';
    }

    // Instantiate Renderer
    $renderer = new RestPX_Renderer();
    
    // Render Output
    return $renderer->render($posts, $atts);
}