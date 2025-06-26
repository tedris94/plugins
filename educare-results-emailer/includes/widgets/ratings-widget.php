<?php
/**
 * Ratings Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Ratings_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_ratings';
    }

    public function get_title() {
        return __('Ratings', 'educare-results-emailer');
    }

    public function get_icon() {
        return 'eicon-star-o';
    }

    public function get_categories() {
        return ['educare-results'];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'educare-results-emailer'),
            ]
        );

        $this->add_control(
            'show_skills',
            [
                'label' => __('Show Skills', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_development',
            [
                'label' => __('Show Development', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'educare-results-emailer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .educare-ratings' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .educare-ratings .ratings-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => __('Label Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .educare-ratings .rating-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'star_color',
            [
                'label' => __('Star Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffc107',
                'selectors' => [
                    '{{WRAPPER}} .educare-ratings .rating-stars' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $elementor = Educare_Results_Emailer_Elementor::get_instance();
        $student = $elementor->get_result_data();

        if (!$student) {
            echo '<div class="educare-ratings">' . __('No student selected', 'educare-results-emailer') . '</div>';
            return;
        }

        $show_skills = $settings['show_skills'] === 'yes';
        $show_development = $settings['show_development'] === 'yes';

        if (!$show_skills && !$show_development) {
            return;
        }

        echo '<div class="educare-ratings">';

        if ($show_skills && !empty($student->Details->skills)) {
            echo '<div class="ratings-section">';
            echo '<div class="ratings-title">' . __('Skills', 'educare-results-emailer') . '</div>';
            echo '<div class="ratings-grid">';

            foreach ($student->Details->skills as $skill => $rating) {
                echo '<div class="rating-item">';
                echo '<div class="rating-label">' . esc_html($skill) . '</div>';
                echo '<div class="rating-stars">';
                for ($i = 1; $i <= 5; $i++) {
                    $star_class = $i <= $rating ? 'eicon-star' : 'eicon-star-o';
                    echo '<i class="' . esc_attr($star_class) . '"></i>';
                }
                echo '</div>';
                echo '</div>';
            }

            echo '</div>'; // .ratings-grid
            echo '</div>'; // .ratings-section
        }

        if ($show_development && !empty($student->Details->development)) {
            echo '<div class="ratings-section">';
            echo '<div class="ratings-title">' . __('Development', 'educare-results-emailer') . '</div>';
            echo '<div class="ratings-grid">';

            foreach ($student->Details->development as $item => $rating) {
                echo '<div class="rating-item">';
                echo '<div class="rating-label">' . esc_html($item) . '</div>';
                echo '<div class="rating-stars">';
                for ($i = 1; $i <= 5; $i++) {
                    $star_class = $i <= $rating ? 'eicon-star' : 'eicon-star-o';
                    echo '<i class="' . esc_attr($star_class) . '"></i>';
                }
                echo '</div>';
                echo '</div>';
            }

            echo '</div>'; // .ratings-grid
            echo '</div>'; // .ratings-section
        }

        echo '</div>'; // .educare-ratings
    }
} 