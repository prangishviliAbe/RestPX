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
    ], $atts, 'rest_api_posts');

    if (empty($atts['url'])) {
        return '<p>Please provide the URL of the external site.</p>';
    }

    // Parse the user-provided URL to intelligently detect Language and Category
    $parsed_url = parse_url($atts['url']);
    
    // Ensure we have a valid scheme and host
    if (!isset($parsed_url['host'])) {
        return '<p>Invalid URL provided.</p>';
    }
    
    // Construct the clean base URL (e.g., https://site.com)
    $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'https';
    $base_url = $scheme . '://' . $parsed_url['host'];
    
    // Process path segments to find Language and Category
    $path = isset($parsed_url['path']) ? trim($parsed_url['path'], '/') : '';
    $path_segments = explode('/', $path);
    
    $lang = $atts['lang']; // Default to widget setting
    $category_slug = null;
    $category_id = null;

    // Detect Language (2-letter code at start of path, e.g., /en/)
    if (isset($path_segments[0]) && preg_match('/^[a-z]{2}$/', $path_segments[0])) {
        $lang = $path_segments[0];
        // Remove lang from segments to avoid confusion with category parsing if needed
        // but since we search for 'category' keyword, we can leave it or just be aware.
    }

    // Detect Category Slug (look for 'category' segment)
    // Example: /category/my-cat/ or /en/category/my-cat/
    $cat_index = array_search('category', $path_segments);
    if ($cat_index !== false && isset($path_segments[$cat_index + 1])) {
        $category_slug = $path_segments[$cat_index + 1];
    }
    
    // Prepare initial query params
    $query_params = [
        'per_page' => intval($atts['count']),
        'lang' => sanitize_text_field($lang),
        '_embed' => true,
    ];

    // If a category slug was found, we need to resolve it to an ID
    if ($category_slug) {
        $cat_cache_key = 'rest_api_cat_id_' . md5($base_url . $category_slug . $lang);
        $cached_cat_id = get_transient($cat_cache_key);

        if ($cached_cat_id) {
            $category_id = $cached_cat_id;
        } else {
            // Fetch category ID from API
            // Note: Polylang often requires 'lang' param even for category lookup to get the right ID
            $cat_api_url = $base_url . '/wp-json/wp/v2/categories';
            $cat_api_url = add_query_arg([
                'slug' => $category_slug,
                'lang' => $lang
            ], $cat_api_url);

            $cat_response = wp_remote_get($cat_api_url);
            
            if (!is_wp_error($cat_response)) {
                $cat_body = wp_remote_retrieve_body($cat_response);
                $cat_data = json_decode($cat_body, true);
                
                if (!empty($cat_data) && isset($cat_data[0]['id'])) {
                    $category_id = $cat_data[0]['id'];
                    set_transient($cat_cache_key, $category_id, 24 * HOUR_IN_SECONDS); // Cache for 24h
                }
            }
        }

        if ($category_id) {
            $query_params['categories'] = $category_id;
        }
    }

    // Construct Final API Request URL
    $api_url = $base_url . '/wp-json/wp/v2/posts';
    $request_url = add_query_arg($query_params, $api_url);

    $cache_key = 'rest_api_posts_' . md5($request_url);
    $cached_posts = get_transient($cache_key);

    if ($cached_posts) {
        $posts = $cached_posts;
    } else {
        $response = wp_remote_get($request_url);

        if (is_wp_error($response)) {
            return '<p>Unable to retrieve posts. Please try again later.</p>';
        }

        $body = wp_remote_retrieve_body($response);
        $posts = json_decode($body, true);

        if (empty($posts)) {
            return '<p>No posts found.</p>';
        }

        // Cache posts for 5 minutes
        set_transient($cache_key, $posts, 5 * MINUTE_IN_SECONDS);
    }

    // Generate output
    $output = '<div class="rest-api-posts-container">';
    $posts_per_page = 6; // Number of posts per page
    $total_posts = count($posts);
    $total_pages = ceil($total_posts / $posts_per_page);

    for ($i = 0; $i < $total_pages; $i++) {
        $is_active = $i === 0 ? 'active' : 'hidden';
        $output .= '<div class="rest-api-posts-page ' . $is_active . '" data-page="' . ($i + 1) . '">';

        $start = $i * $posts_per_page;
        $page_posts = array_slice($posts, $start, $posts_per_page);

        foreach ($page_posts as $post) {
            $title = esc_html($post['title']['rendered']);
            $link = esc_url($post['link']);
            $image_url = isset($post['_embedded']['wp:featuredmedia'][0]['source_url'])
                ? esc_url($post['_embedded']['wp:featuredmedia'][0]['source_url'])
                : 'https://via.placeholder.com/300';

            $output .= '<div class="rest-api-post-card animate-on-scroll">';
            $output .= '<a href="' . $link . '" target="_blank">';
            $output .= '<div class="rest-api-post-image">';
            $output .= '<img src="' . $image_url . '" alt="' . $title . '">';
            $output .= '</div>';
            $output .= '<div class="rest-api-post-content">';
            $output .= '<p class="rest-api-post-date">' . date_i18n(get_option('date_format'), strtotime($post['date'])) . '</p>';
            $output .= '<h3>' . $title . '</h3>';
            $output .= '</div>';
            $output .= '</a></div>';
        }

        $output .= '</div>'; // .rest-api-posts-page
    }

    // Add navigation buttons
    $output .= '<div class="rest-api-posts-navigation">';
    $output .= '<button class="rest-api-prev" disabled></button>';
    $output .= '<button class="rest-api-next"></button>';
    $output .= '</div>';

    $output .= '</div>'; // .rest-api-posts-container

    return $output;
}