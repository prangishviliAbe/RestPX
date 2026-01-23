<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register the Elementor widget
add_action('elementor/widgets/widgets_registered', 'register_rest_api_posts_widget');

/**
 * Registers the custom widget for Elementor.
 */
function register_rest_api_posts_widget($widgets_manager) {
    if (!class_exists('Elementor\Widget_Base')) {
        return;
    }

    class REST_API_Posts_Widget extends \Elementor\Widget_Base {
        /**
         * Get the widget name.
         *
         * @return string Widget name.
         */
        public function get_name() {
            return 'rest_api_posts_widget';
        }

        /**
         * Get the widget title.
         *
         * @return string Widget title.
         */
        public function get_title() {
            return __('REST API Posts', 'rest-api-posts');
        }

        /**
         * Get the widget icon.
         *
         * @return string Widget icon.
         */
        public function get_icon() {
            return 'eicon-posts-grid';
        }

        /**
         * Get the widget categories.
         *
         * @return array Widget categories.
         */
        public function get_categories() {
            return ['general'];
        }

        /**
         * Register the widget controls.
         */
        protected function _register_controls() {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Content', 'rest-api-posts'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'url',
                [
                    'label' => __('API URL', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'input_type' => 'url',
                    'placeholder' => __('https://example.com', 'rest-api-posts'),
                    'label_block' => true,
                ]
            );

            $this->add_control(
                'count',
                [
                    'label' => __('Number of Posts', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 5,
                    'min' => 1,
                    'max' => 20,
                ]
            );

            $this->add_control(
                'lang',
                [
                    'label' => __('Language', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'en',
                    'options' => [
                        'en' => __('English', 'rest-api-posts'),
                        'ka-ge' => __('Georgian', 'rest-api-posts'),
                    ],
                ]
            );

            $this->end_controls_section();
        }

        /**
         * Render the widget output on the frontend.
         */
        protected function render() {
            $settings = $this->get_settings_for_display();

            // Retrieve widget settings
            $url = $settings['url'];
            $count = $settings['count'];
            $lang = $settings['lang'];

            // Validate the URL
            if (empty($url)) {
                echo '<p>' . __('Please provide a valid API URL.', 'rest-api-posts') . '</p>';
                return;
            }

            // Use the shortcode function to fetch posts and output them as cards
            echo do_shortcode('[rest_api_posts url="' . esc_url($url) . '" count="' . intval($count) . '" lang="' . esc_attr($lang) . '"]');
        }
    }

    // Register the widget
    $widgets_manager->register(new REST_API_Posts_Widget());
}