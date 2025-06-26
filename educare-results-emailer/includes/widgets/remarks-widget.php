<?php
/**
 * Remarks Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Remarks_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_remarks';
    }

    public function get_title() {
        return __('Remarks', 'educare-results-emailer');
    }

    public function get_icon() {
        return 'eicon-text';
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
            'show_headmaster',
            [
                'label' => __('Show Headmaster Remarks', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_form_teacher',
            [
                'label' => __('Show Form Teacher Remarks', 'educare-results-emailer'),
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
                    '{{WRAPPER}} .educare-remarks' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .educare-remarks .remarks-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .educare-remarks .remarks-text' => 'color: {{VALUE}};',
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
            echo '<div class="educare-remarks">' . __('No student selected', 'educare-results-emailer') . '</div>';
            return;
        }

        $show_headmaster = $settings['show_headmaster'] === 'yes';
        $show_form_teacher = $settings['show_form_teacher'] === 'yes';

        if (!$show_headmaster && !$show_form_teacher) {
            return;
        }

        echo '<div class="educare-remarks">';

        if ($show_headmaster && !empty($student->Details->headmaster_remarks)) {
            echo '<div class="remarks-section">';
            echo '<div class="remarks-title">' . __('Headmaster Remarks', 'educare-results-emailer') . '</div>';
            echo '<div class="remarks-text">' . esc_html($student->Details->headmaster_remarks) . '</div>';
            echo '</div>';
        }

        if ($show_form_teacher && !empty($student->Details->form_teacher_remarks)) {
            echo '<div class="remarks-section">';
            echo '<div class="remarks-title">' . __('Form Teacher Remarks', 'educare-results-emailer') . '</div>';
            echo '<div class="remarks-text">' . esc_html($student->Details->form_teacher_remarks) . '</div>';
            echo '</div>';
        }

        echo '</div>';
    }
} 