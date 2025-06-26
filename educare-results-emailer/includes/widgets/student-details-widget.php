<?php
/**
 * Student Details Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Student_Details_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_student_details';
    }

    public function get_title() {
        return __('Student Details', 'educare-results-emailer');
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
                'label' => __('Show Student Photo', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'photo_size',
            [
                'label' => __('Photo Size', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 300,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 150,
                ],
                'condition' => [
                    'show_photo' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_fields',
            [
                'label' => __('Show Fields', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    'name' => __('Name', 'educare-results-emailer'),
                    'class' => __('Class', 'educare-results-emailer'),
                    'roll_no' => __('Roll No', 'educare-results-emailer'),
                    'regi_no' => __('Registration No', 'educare-results-emailer'),
                    'exam' => __('Exam', 'educare-results-emailer'),
                    'year' => __('Year', 'educare-results-emailer'),
                    'group' => __('Group', 'educare-results-emailer'),
                    'section' => __('Section', 'educare-results-emailer'),
                ],
                'default' => ['name', 'class', 'roll_no', 'regi_no', 'exam', 'year'],
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
                    '{{WRAPPER}} .student-details-box' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => __('Label Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .student-detail-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => __('Value Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .student-detail-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => __('Label Typography', 'educare-results-emailer'),
                'selector' => '{{WRAPPER}} .student-detail-label',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'label' => __('Value Typography', 'educare-results-emailer'),
                'selector' => '{{WRAPPER}} .student-detail-value',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $student_id = get_query_var('student_id', 0);

        if (!$student_id) {
            echo __('No student selected', 'educare-results-emailer');
            return;
        }

        $elementor = Educare_Results_Emailer_Elementor::get_instance();
        $student = $elementor->get_result_data($student_id);

        if (!$student) {
            echo __('No student data available', 'educare-results-emailer');
            return;
        }

        ?>
        <div class="educare-student-details">
            <div class="student-details-box">
                <?php if ($settings['show_photo'] === 'yes' && !empty($student->Photos)): ?>
                    <div class="student-photo" style="width: <?php echo esc_attr($settings['photo_size']['size']); ?>px; height: <?php echo esc_attr($settings['photo_size']['size']); ?>px;">
                        <img src="<?php echo esc_url($student->Photos); ?>" alt="<?php echo esc_attr($student->Name); ?>">
                    </div>
                <?php endif; ?>

                <div class="student-details-grid">
                    <?php
                    $fields = $settings['show_fields'];
                    $field_mapping = [
                        'name' => ['label' => __('Name', 'educare-results-emailer'), 'value' => $student->Name],
                        'class' => ['label' => __('Class', 'educare-results-emailer'), 'value' => $student->Class],
                        'roll_no' => ['label' => __('Roll No', 'educare-results-emailer'), 'value' => $student->Roll_No],
                        'regi_no' => ['label' => __('Registration No', 'educare-results-emailer'), 'value' => $student->Regi_No],
                        'exam' => ['label' => __('Exam', 'educare-results-emailer'), 'value' => $student->Exam],
                        'year' => ['label' => __('Year', 'educare-results-emailer'), 'value' => $student->Year],
                        'group' => ['label' => __('Group', 'educare-results-emailer'), 'value' => isset($student->Group) ? $student->Group : ''],
                        'section' => ['label' => __('Section', 'educare-results-emailer'), 'value' => isset($student->Section) ? $student->Section : ''],
                    ];

                    foreach ($fields as $field) {
                        if (isset($field_mapping[$field])) {
                            $field_data = $field_mapping[$field];
                            ?>
                            <div class="student-detail-item">
                                <div class="student-detail-label"><?php echo esc_html($field_data['label']); ?></div>
                                <div class="student-detail-value"><?php echo esc_html($field_data['value']); ?></div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
} 