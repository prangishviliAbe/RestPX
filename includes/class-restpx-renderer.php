<?php
if (!defined('ABSPATH')) {
    exit;
}

class RestPX_Renderer {
    
    /**
     * Render the posts grid.
     * 
     * @param array $posts List of posts.
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render($posts, $atts) {
        if (empty($posts)) {
            return '<p>No posts found.</p>';
        }

        $grid_class = 'rap-grid-' . intval($atts['grid']);
        $style_class = 'rap-style-' . esc_attr($atts['style']);
        
        $output = '<div class="rest-api-posts-container ' . $grid_class . ' ' . $style_class . '">';
        
        $posts_per_page = 6;
        $total_posts = count($posts);
        $total_pages = ceil($total_posts / $posts_per_page);

        for ($i = 0; $i < $total_pages; $i++) {
            $is_active = $i === 0 ? 'active' : 'hidden';
            $output .= '<div class="rest-api-posts-page ' . $is_active . '" data-page="' . ($i + 1) . '">';

            $start = $i * $posts_per_page;
            $page_posts = array_slice($posts, $start, $posts_per_page);

            foreach ($page_posts as $post) {
                $output .= $this->load_template($atts['style'], $post);
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

    /**
     * Load a specific template for a post card.
     * 
     * @param string $style The style name (default, minimal, overlay).
     * @param array $post The post data.
     * @return string HTML for the single card.
     */
    private function load_template($style, $post) {
        $allowed_styles = ['default', 'minimal', 'overlay'];
        if (!in_array($style, $allowed_styles)) {
            $style = 'default';
        }

        $template_file = REST_API_POSTS_PLUGIN_PATH . 'templates/card-' . $style . '.php';

        if (!file_exists($template_file)) {
            // Fallback to default if file missing
            $template_file = REST_API_POSTS_PLUGIN_PATH . 'templates/card-default.php';
        }

        // Prepare variables for the template
        $title = esc_html($post['title']['rendered']);
        $link = esc_url($post['link']);
        $image_url = isset($post['_embedded']['wp:featuredmedia'][0]['source_url'])
            ? esc_url($post['_embedded']['wp:featuredmedia'][0]['source_url'])
            : 'https://via.placeholder.com/300';
        $date = date_i18n(get_option('date_format'), strtotime($post['date']));

        // Capture output
        ob_start();
        include $template_file;
        return ob_get_clean();
    }
}
