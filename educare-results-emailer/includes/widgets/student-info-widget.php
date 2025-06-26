<?php
/**
 * Student Info Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Student_Info_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_student_info';
    }

    public function get_title() {
        return __('Student Info', 'educare-results-emailer');
    }

    public function get_icon() {
        return 'eicon-user-circle-o';
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
            'show_photo',
            [
                'label' => __('Show Photo', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_fields',
            [
                'label' => __('Show Fields', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['name', 'class', 'roll', 'regi', 'exam', 'year', 'group', 'section'],
                'options' => [
                    'name' => __('Name', 'educare-results-emailer'),
                    'class' => __('Class', 'educare-results-emailer'),
                    'roll' => __('Roll No', 'educare-results-emailer'),
                    'regi' => __('Registration No', 'educare-results-emailer'),
                    'exam' => __('Exam', 'educare-results-emailer'),
                    'year' => __('Year', 'educare-results-emailer'),
                    'group' => __('Group', 'educare-results-emailer'),
                    'section' => __('Section', 'educare-results-emailer'),
                ],
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
                    '{{WRAPPER}} .educare-student-info' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .student-info-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => __('Value Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .student-info-value' => 'color: {{VALUE}};',
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
            echo '<div class="educare-student-info">' . __('No student selected', 'educare-results-emailer') . '</div>';
            return;
        }

        $show_fields = $settings['show_fields'];
        $show_photo = $settings['show_photo'] === 'yes';

        echo '<div class="educare-student-info">';
        
        if ($show_photo && !empty($student->Photos)) {
            echo '<div class="student-photo">';
            echo '<img src="' . esc_url($student->Photos) . '" alt="' . esc_attr($student->Name) . '">';
            echo '</div>';
        }

        echo '<div class="student-info-grid">';

        $field_mapping = [
            'name' => ['label' => __('Name', 'educare-results-emailer'), 'value' => $student->Name],
            'class' => ['label' => __('Class', 'educare-results-emailer'), 'value' => $student->Class],
            'roll' => ['label' => __('Roll No', 'educare-results-emailer'), 'value' => $student->Roll_No],
            'regi' => ['label' => __('Registration No', 'educare-results-emailer'), 'value' => $student->Regi_No],
            'exam' => ['label' => __('Exam', 'educare-results-emailer'), 'value' => $student->Exam],
            'year' => ['label' => __('Year', 'educare-results-emailer'), 'value' => $student->Year],
            'group' => ['label' => __('Group', 'educare-results-emailer'), 'value' => $student->Group],
            'section' => ['label' => __('Section', 'educare-results-emailer'), 'value' => $student->Section],
        ];

        foreach ($show_fields as $field) {
            if (isset($field_mapping[$field])) {
                $field_data = $field_mapping[$field];
                echo '<div class="student-info-item">';
                echo '<span class="student-info-label">' . esc_html($field_data['label']) . ':</span>';
                echo '<span class="student-info-value">' . esc_html($field_data['value']) . '</span>';
                echo '</div>';
            }
        }

        echo '</div>'; // .student-info-grid
        echo '</div>'; // .educare-student-info
    }
} 