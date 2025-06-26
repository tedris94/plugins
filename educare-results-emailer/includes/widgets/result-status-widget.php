<?php
/**
 * Result Status Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Result_Status_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_result_status';
    }

    public function get_title() {
        return __('Result Status', 'educare-results-emailer');
    }

    public function get_icon() {
        return 'eicon-check-circle-o';
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
            'show_icon',
            [
                'label' => __('Show Icon', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_message',
            [
                'label' => __('Show Message', 'educare-results-emailer'),
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
            'icon_color',
            [
                'label' => __('Icon Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#28a745',
                'selectors' => [
                    '{{WRAPPER}} .educare-result-status .status-icon' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .educare-result-status .status-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'message_color',
            [
                'label' => __('Message Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .educare-result-status .status-message' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_message' => 'yes',
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
            echo '<div class="educare-result-status">' . __('No student selected', 'educare-results-emailer') . '</div>';
            return;
        }

        $show_icon = $settings['show_icon'] === 'yes';
        $show_message = $settings['show_message'] === 'yes';
        $is_passed = $student->result_status === 'Passed';

        echo '<div class="educare-result-status">';
        
        if ($show_icon) {
            $icon_class = $is_passed ? 'eicon-check-circle' : 'eicon-close-circle';
            echo '<i class="status-icon ' . esc_attr($icon_class) . '"></i>';
        }

        echo '<div class="status-text">' . esc_html($student->result_status) . '</div>';

        if ($show_message) {
            $message = $is_passed 
                ? sprintf(__('Congratulations! You have passed the %s examination.', 'educare-results-emailer'), $student->Exam)
                : sprintf(__('You need to improve your performance in the %s examination.', 'educare-results-emailer'), $student->Exam);
            echo '<div class="status-message">' . esc_html($message) . '</div>';
        }

        echo '</div>';
    }
} 