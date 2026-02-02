<?php
if (!defined('ABSPATH')) {
    exit;
}

class RestPX_API {
    
    /**
     * Parse the user-provided URL to find Language and Category logic.
     * 
     * @param string $url The external site URL.
     * @param string $default_lang The default language code.
     * @return array Contains 'base_url', 'lang', 'category_slug'
     */
    public function parse_url_for_metadata($url, $default_lang = 'en') {
        $parsed_url = parse_url($url);
        
        if (!isset($parsed_url['host'])) {
            return new WP_Error('invalid_url', 'Invalid URL provided.');
        }

        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'https';
        $base_url = $scheme . '://' . $parsed_url['host'];
        
        $path = isset($parsed_url['path']) ? trim($parsed_url['path'], '/') : '';
        $path_segments = explode('/', $path);
        
        $lang = $default_lang;
        $category_slug = null;

        // Detect Language
        if (isset($path_segments[0]) && preg_match('/^[a-z]{2}$/', $path_segments[0])) {
            $lang = $path_segments[0];
            // If language is detected in path, append it to base_url to support key/wp-json style
            $base_url .= '/' . $lang;
        }

        // Detect Category
        $cat_index = array_search('category', $path_segments);
        if ($cat_index !== false && isset($path_segments[$cat_index + 1])) {
            $category_slug = $path_segments[$cat_index + 1];
        }

        return [
            'base_url' => $base_url,
            'lang' => $lang,
            'category_slug' => $category_slug
        ];
    }

    /**
     * Get Category ID by slug from external site.
     */
    public function get_category_id($base_url, $category_slug, $lang) {
        $cat_cache_key = 'rest_api_cat_id_' . md5($base_url . $category_slug . $lang);
        $cached_cat_id = get_transient($cat_cache_key);

        if ($cached_cat_id) {
            return $cached_cat_id;
        }

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
                set_transient($cat_cache_key, $category_id, 24 * HOUR_IN_SECONDS);
                return $category_id;
            }
        }

        return null; // Not found or error
    }

    /**
     * Fetch Posts from external API.
     */
    public function fetch_posts($base_url, $count, $lang, $category_id = null) {
        $query_params = [
            'per_page' => intval($count),
            'lang' => sanitize_text_field($lang),
            '_embed' => true,
        ];

        if ($category_id) {
            $query_params['categories'] = $category_id;
        }

        $api_url = $base_url . '/wp-json/wp/v2/posts';
        $request_url = add_query_arg($query_params, $api_url);

        $cache_key = 'rest_api_posts_' . md5($request_url);
        $cached_posts = get_transient($cache_key);

        if ($cached_posts) {
            return $cached_posts;
        }

        $response = wp_remote_get($request_url);

        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Unable to retrieve posts.');
        }

        $body = wp_remote_retrieve_body($response);
        $posts = json_decode($body, true);

        if (empty($posts)) {
            return []; // Return empty array if no posts
        }

        set_transient($cache_key, $posts, 5 * MINUTE_IN_SECONDS);
        return $posts;
    }
}
