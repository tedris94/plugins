<?php
/**
 * Quick Overview Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Quick_Overview_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_quick_overview';
    }

    public function get_title() {
        return __('Quick Overview', 'educare-results-emailer');
    }

    public function get_icon() {
        return 'eicon-info-circle-o';
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
            'show_items',
            [
                'label' => __('Show Items', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    'total_subjects' => __('Total Subjects', 'educare-results-emailer'),
                    'total_marks' => __('Total Marks', 'educare-results-emailer'),
                    'average_marks' => __('Average Marks', 'educare-results-emailer'),
                    'gpa' => __('GPA', 'educare-results-emailer'),
                    'grade' => __('Grade', 'educare-results-emailer'),
                    'position' => __('Position', 'educare-results-emailer'),
                    'total_students' => __('Total Students', 'educare-results-emailer'),
                ],
                'default' => ['total_subjects', 'total_marks', 'average_marks', 'gpa', 'grade', 'position'],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'educare-results-emailer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'box_background',
            [
                'label' => __('Box Background', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quick-overview-box' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => __('Label Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quick-overview-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => __('Value Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quick-overview-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => __('Label Typography', 'educare-results-emailer'),
                'selector' => '{{WRAPPER}} .quick-overview-label',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'label' => __('Value Typography', 'educare-results-emailer'),
                'selector' => '{{WRAPPER}} .quick-overview-value',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $elementor = Educare_Results_Emailer_Elementor::get_instance();
        $student = $elementor->get_result_data();

        if (!$student) {
            echo __('No student selected', 'educare-results-emailer');
            return;
        }

        ?>
        <div class="educare-quick-overview">
            <div class="quick-overview-grid">
                <?php
                $items = $settings['show_items'];
                $item_mapping = [
                    'total_subjects' => [
                        'label' => __('Total Subjects', 'educare-results-emailer'),
                        'value' => $student->total_subjects,
                        'icon' => 'fa-book'
                    ],
                    'total_marks' => [
                        'label' => __('Total Marks', 'educare-results-emailer'),
                        'value' => $student->total_marks,
                        'icon' => 'fa-star'
                    ],
                    'average_marks' => [
                        'label' => __('Average Marks', 'educare-results-emailer'),
                        'value' => $student->average_marks,
                        'icon' => 'fa-calculator'
                    ],
                    'gpa' => [
                        'label' => __('GPA', 'educare-results-emailer'),
                        'value' => $student->gpa,
                        'icon' => 'fa-chart-line'
                    ],
                    'grade' => [
                        'label' => __('Grade', 'educare-results-emailer'),
                        'value' => $student->grade,
                        'icon' => 'fa-award'
                    ],
                    'position' => [
                        'label' => __('Position', 'educare-results-emailer'),
                        'value' => sprintf(__('%d out of %d', 'educare-results-emailer'), $student->position, $student->total_students),
                        'icon' => 'fa-trophy'
                    ],
                    'total_students' => [
                        'label' => __('Total Students', 'educare-results-emailer'),
                        'value' => $student->total_students,
                        'icon' => 'fa-users'
                    ],
                ];

                foreach ($items as $item) {
                    if (isset($item_mapping[$item])) {
                        $item_data = $item_mapping[$item];
                        ?>
                        <div class="quick-overview-box">
                            <i class="fas <?php echo esc_attr($item_data['icon']); ?>"></i>
                            <div class="quick-overview-label"><?php echo esc_html($item_data['label']); ?></div>
                            <div class="quick-overview-value"><?php echo esc_html($item_data['value']); ?></div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
} 