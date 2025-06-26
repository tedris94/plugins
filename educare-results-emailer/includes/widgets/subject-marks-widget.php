<?php
/**
 * Subject Marks Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Subject_Marks_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_subject_marks';
    }

    public function get_title() {
        return __('Subject Marks', 'educare-results-emailer');
    }

    public function get_icon() {
        return 'eicon-table';
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
            'show_marks',
            [
                'label' => __('Show Marks', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['marks', 'grade', 'gpa'],
                'options' => [
                    'marks' => __('Marks', 'educare-results-emailer'),
                    'grade' => __('Grade', 'educare-results-emailer'),
                    'gpa' => __('GPA', 'educare-results-emailer'),
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
            'table_background',
            [
                'label' => __('Table Background', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .educare-subject-marks table' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'header_background',
            [
                'label' => __('Header Background', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .educare-subject-marks th' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'header_color',
            [
                'label' => __('Header Text Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .educare-subject-marks th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'cell_color',
            [
                'label' => __('Cell Text Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .educare-subject-marks td' => 'color: {{VALUE}};',
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
            echo '<div class="educare-subject-marks">' . __('No student selected', 'educare-results-emailer') . '</div>';
            return;
        }

        if (empty($student->Subject)) {
            echo '<div class="educare-subject-marks">' . __('No subject data available', 'educare-results-emailer') . '</div>';
            return;
        }

        $show_marks = $settings['show_marks'];

        echo '<div class="educare-subject-marks">';
        echo '<table>';
        echo '<thead><tr>';
        echo '<th>' . __('Subject', 'educare-results-emailer') . '</th>';
        
        if (in_array('marks', $show_marks)) {
            echo '<th>' . __('Marks', 'educare-results-emailer') . '</th>';
        }
        if (in_array('grade', $show_marks)) {
            echo '<th>' . __('Grade', 'educare-results-emailer') . '</th>';
        }
        if (in_array('gpa', $show_marks)) {
            echo '<th>' . __('GPA', 'educare-results-emailer') . '</th>';
        }
        
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($student->Subject as $subject_name => $subject_data) {
            echo '<tr>';
            echo '<td>' . esc_html($subject_name) . '</td>';
            
            if (in_array('marks', $show_marks)) {
                echo '<td>' . esc_html($subject_data->marks) . '</td>';
            }
            if (in_array('grade', $show_marks)) {
                echo '<td>' . esc_html($subject_data->grade) . '</td>';
            }
            if (in_array('gpa', $show_marks)) {
                echo '<td>' . esc_html($subject_data->gpa) . '</td>';
            }
            
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
} 