<?php
/**
 * School Banner Widget for Elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_School_Banner_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'educare_school_banner';
    }

    public function get_title() {
        return __('School Banner', 'educare-results-emailer');
    }

    public function get_icon() {
        return 'eicon-banner';
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
            'show_logo',
            [
                'label' => __('Show School Logo', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'logo_size',
            [
                'label' => __('Logo Size', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 300,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 150,
                ],
                'condition' => [
                    'show_logo' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_contact',
            [
                'label' => __('Show Contact Information', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_founded',
            [
                'label' => __('Show Founded Date', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
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
            'banner_background',
            [
                'label' => __('Banner Background', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .school-banner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'school_name_color',
            [
                'label' => __('School Name Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .school-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'contact_color',
            [
                'label' => __('Contact Info Color', 'educare-results-emailer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .school-contact' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'school_name_typography',
                'label' => __('School Name Typography', 'educare-results-emailer'),
                'selector' => '{{WRAPPER}} .school-name',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'contact_typography',
                'label' => __('Contact Info Typography', 'educare-results-emailer'),
                'selector' => '{{WRAPPER}} .school-contact',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Get school information from Educare settings
        $school_name = get_option('educare_school_name', '');
        $school_phone = get_option('educare_school_phone', '');
        $school_email = get_option('educare_school_email', '');
        $school_founded = get_option('educare_school_founded', '');
        $school_logo = get_option('educare_school_logo', '');

        if (empty($school_name)) {
            echo __('School information not configured', 'educare-results-emailer');
            return;
        }

        ?>
        <div class="educare-school-banner">
            <div class="school-banner">
                <div class="school-banner-content">
                    <?php if ($settings['show_logo'] === 'yes' && !empty($school_logo)): ?>
                        <div class="school-logo">
                            <img src="<?php echo esc_url($school_logo); ?>" 
                                 alt="<?php echo esc_attr($school_name); ?>"
                                 style="width: <?php echo esc_attr($settings['logo_size']['size']); ?>px;">
                        </div>
                    <?php endif; ?>

                    <div class="school-info">
                        <h1 class="school-name"><?php echo esc_html($school_name); ?></h1>
                        
                        <?php if ($settings['show_contact'] === 'yes'): ?>
                            <div class="school-contact">
                                <?php if (!empty($school_phone)): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo esc_html($school_phone); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($school_email)): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo esc_html($school_email); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($settings['show_founded'] === 'yes' && !empty($school_founded)): ?>
                            <div class="school-founded">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo esc_html($school_founded); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} 