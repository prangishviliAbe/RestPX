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
            return __('RestPX', 'rest-api-posts');
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
                    'max' => 400,
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
                        'ka' => __('Georgian', 'rest-api-posts'),
                    ],
                ]
            );

            $this->add_control(
                'grid',
                [
                    'label' => __('Grid Columns', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 3,
                    'min' => 1,
                    'max' => 4,
                ]
            );

            $this->add_control(
                'style',
                [
                    'label' => __('Card Style', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'default',
                    'options' => [
                        'default' => __('Default', 'rest-api-posts'),
                        'minimal' => __('Minimal', 'rest-api-posts'),
                        'overlay' => __('Overlay', 'rest-api-posts'),
                    ],
                ]
            );

            $this->add_control(
                'show_excerpt',
                [
                    'label' => __('Show Excerpt', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __('Yes', 'rest-api-posts'),
                    'label_off' => __('No', 'rest-api-posts'),
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );

            $this->end_controls_section();

            // --- Style Section: Card ---
            $this->start_controls_section(
                'style_section_card',
                [
                    'label' => __('Card Style', 'rest-api-posts'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'card_background_color',
                [
                    'label' => __('Background Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-card' => 'background-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'style' => ['default', 'minimal'],
                    ],
                ]
            );

            $this->add_control(
                'card_border_radius',
                [
                    'label' => __('Border Radius', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .rest-api-post-image' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                \Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'card_box_shadow',
                    'label' => __('Box Shadow', 'rest-api-posts'),
                    'selector' => '{{WRAPPER}} .rest-api-post-card',
                    'condition' => [
                        'style' => ['default'],
                    ],
                ]
            );

            $this->add_responsive_control(
                'card_padding',
                [
                    'label' => __('Padding', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'card_margin',
                [
                    'label' => __('Margin', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'card_hover_bg_color',
                [
                    'label' => __('Hover Background Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-card:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'card_hover_box_shadow',
                    'label' => __('Hover Box Shadow', 'rest-api-posts'),
                    'selector' => '{{WRAPPER}} .rest-api-post-card:hover',
                ]
            );

            $this->end_controls_section();

            // --- Style Section: Image ---
            $this->start_controls_section(
                'style_section_image',
                [
                    'label' => __('Image', 'rest-api-posts'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                'image_height',
                [
                    'label' => __('Height', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px', 'vh'],
                    'range' => [
                        'px' => [
                            'min' => 100,
                            'max' => 500,
                            'step' => 5,
                        ],
                        'vh' => [
                            'min' => 10,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                    ],
                ]
            );

            $this->add_control(
                'image_border_radius',
                [
                    'label' => __('Border Radius', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'image_border',
                    'label' => __('Border', 'rest-api-posts'),
                    'selector' => '{{WRAPPER}} .rest-api-post-image img',
                ]
            );

            $this->end_controls_section();

            // --- Style Section: Typography ---
            $this->start_controls_section(
                'style_section_typography',
                [
                    'label' => __('Typography', 'rest-api-posts'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'title_heading',
                [
                    'label' => __('Title', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'title_color',
                [
                    'label' => __('Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-card h3' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'selector' => '{{WRAPPER}} .rest-api-post-card h3',
                ]
            );

            $this->add_control(
                'date_heading',
                [
                    'label' => __('Date', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'date_color',
                [
                    'label' => __('Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-date' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'date_typography',
                    'selector' => '{{WRAPPER}} .rest-api-post-date',
                ]
            );

            $this->add_control(
                'excerpt_heading',
                [
                    'label' => __('Excerpt', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'show_excerpt' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'excerpt_color',
                [
                    'label' => __('Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-post-excerpt' => 'color: {{VALUE}};',
                    ],
                    'condition' => [
                        'show_excerpt' => 'yes',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'excerpt_typography',
                    'selector' => '{{WRAPPER}} .rest-api-post-excerpt',
                    'condition' => [
                        'show_excerpt' => 'yes',
                    ],
                ]
            );

            $this->end_controls_section();

            // --- Style Section: Navigation ---
            $this->start_controls_section(
                'style_section_navigation',
                [
                    'label' => __('Navigation', 'rest-api-posts'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'arrow_color',
                [
                    'label' => __('Arrow Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-prev, {{WRAPPER}} .rest-api-next' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .rest-api-prev::before, {{WRAPPER}} .rest-api-next::before' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'arrow_bg_color',
                [
                    'label' => __('Background Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-prev, {{WRAPPER}} .rest-api-next' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'arrow_bg_hover_color',
                [
                    'label' => __('Background Hover Color', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-prev:hover, {{WRAPPER}} .rest-api-next:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'arrow_border_radius',
                [
                    'label' => __('Border Radius', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-prev, {{WRAPPER}} .rest-api-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'custom_prev_arrow',
                [
                    'label' => __('Custom Previous Arrow', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'media_type' => 'image',
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-prev::before' => 'content: url("{{URL}}");',
                    ],
                ]
            );

            $this->add_control(
                'custom_next_arrow',
                [
                    'label' => __('Custom Next Arrow', 'rest-api-posts'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'media_type' => 'image',
                    'selectors' => [
                        '{{WRAPPER}} .rest-api-next::before' => 'content: url("{{URL}}");',
                    ],
                ]
            );

            $this->end_controls_section();

            // --- Style Section: Background ---
            $this->start_controls_section(
                'style_section_background',
                [
                    'label' => __('Background', 'rest-api-posts'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'widget_background',
                    'label' => __('Background', 'rest-api-posts'),
                    'types' => ['classic', 'gradient'],
                    'selector' => '{{WRAPPER}} .rest-api-posts-container',
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
            echo do_shortcode('[rest_api_posts url="' . esc_url($url) . '" count="' . intval($count) . '" lang="' . esc_attr($lang) . '" grid="' . intval($settings['grid']) . '" style="' . esc_attr($settings['style']) . '" show_excerpt="' . esc_attr($settings['show_excerpt']) . '"]');
        }
    }

    // Register the widget
    $widgets_manager->register(new REST_API_Posts_Widget());
}