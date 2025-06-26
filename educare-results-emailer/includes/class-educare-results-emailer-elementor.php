<?php
/**
 * Elementor Integration for Educare Results Emailer
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Results_Emailer_Elementor {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }

    public function add_elementor_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'educare-results',
            [
                'title' => __('Educare Results', 'educare-results-emailer'),
                'icon' => 'fa fa-graduation-cap',
            ]
        );
    }

    public function register_widgets() {
        require_once plugin_dir_path(__FILE__) . 'widgets/school-banner-widget.php';
        require_once plugin_dir_path(__FILE__) . 'widgets/student-info-widget.php';
        require_once plugin_dir_path(__FILE__) . 'widgets/subject-marks-widget.php';
        require_once plugin_dir_path(__FILE__) . 'widgets/result-status-widget.php';
        require_once plugin_dir_path(__FILE__) . 'widgets/remarks-widget.php';
        require_once plugin_dir_path(__FILE__) . 'widgets/ratings-widget.php';

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Educare_School_Banner_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Educare_Student_Info_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Educare_Subject_Marks_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Educare_Result_Status_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Educare_Remarks_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Educare_Ratings_Widget());
    }

    public function get_selected_student_id() {
        if (!session_id()) {
            session_start();
        }
        return isset($_SESSION['selected_student_id']) ? $_SESSION['selected_student_id'] : 0;
    }

    public function get_result_data($student_id = null) {
        // If no student ID is provided, try to get it from the session
        if ($student_id === null) {
            $student_id = $this->get_selected_student_id();
        }

        if (!$student_id) {
            return null;
        }

        global $wpdb;

        // Get student data from the database
        $student_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}educare_results WHERE id = %d",
            $student_id
        ));

        if (!$student_data) {
            return null;
        }

        // Process the student data using Educare's default result system
        if (function_exists('educare_process_default_result_system')) {
            $student = educare_process_default_result_system($student_data);
        } else {
            // Fallback processing if the function doesn't exist
            $student = new stdClass();
            $student->id = $student_data->id;
            $student->Name = $student_data->Name;
            $student->Class = $student_data->Class;
            $student->Roll_No = $student_data->Roll_No;
            $student->Regi_No = $student_data->Regi_No;
            $student->Exam = $student_data->Exam;
            $student->Year = $student_data->Year;
            $student->Group = isset($student_data->Group) ? $student_data->Group : '';
            $student->Section = isset($student_data->Section) ? $student_data->Section : '';
            $student->Subject = json_decode($student_data->Subject);
            $student->Photos = $student_data->Photos;
            $student->Details = json_decode($student_data->Details);
            $student->Others = json_decode($student_data->Others);

            // Calculate additional metrics
            $student->total_subjects = count((array)$student->Subject);
            $student->total_marks = 0;
            $student->obtainable_mark = 0;

            foreach ($student->Subject as $subject) {
                if (isset($subject->marks)) {
                    $student->total_marks += floatval($subject->marks);
                }
                if (isset($subject->obtainable_mark)) {
                    $student->obtainable_mark += floatval($subject->obtainable_mark);
                }
            }

            // Calculate average marks
            $student->average_marks = $student->total_subjects > 0 ? round($student->total_marks / $student->total_subjects, 2) : 0;

            // Get GPA and Grade
            $student->gpa = $this->calculate_gpa($student->average_marks);
            $student->grade = $this->get_grade($student->gpa);

            // Get position
            $student->position = $this->get_student_position($student_id, $student->Class, $student->Exam, $student->Year);
            $student->total_students = $this->get_total_students($student->Class, $student->Exam, $student->Year);

            // Set result status
            $student->result_status = $this->get_result_status($student->gpa);
        }

        return $student;
    }

    private function calculate_gpa($average_marks) {
        if ($average_marks >= 80) return 5.00;
        if ($average_marks >= 70) return 4.00;
        if ($average_marks >= 60) return 3.50;
        if ($average_marks >= 50) return 3.00;
        if ($average_marks >= 40) return 2.00;
        if ($average_marks >= 33) return 1.00;
        return 0.00;
    }

    private function get_grade($gpa) {
        if ($gpa >= 5.00) return 'A+';
        if ($gpa >= 4.00) return 'A';
        if ($gpa >= 3.50) return 'A-';
        if ($gpa >= 3.00) return 'B';
        if ($gpa >= 2.00) return 'C';
        if ($gpa >= 1.00) return 'D';
        return 'F';
    }

    private function get_student_position($student_id, $class, $exam, $year) {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id, total_marks FROM {$wpdb->prefix}educare_results 
            WHERE Class = %s AND Exam = %s AND Year = %s 
            ORDER BY total_marks DESC",
            $class, $exam, $year
        ));

        $position = 1;
        foreach ($results as $result) {
            if ($result->id == $student_id) {
                return $position;
            }
            $position++;
        }

        return 0;
    }

    private function get_total_students($class, $exam, $year) {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}educare_results 
            WHERE Class = %s AND Exam = %s AND Year = %s",
            $class, $exam, $year
        ));
    }

    private function get_result_status($gpa) {
        return $gpa >= 1.00 ? 'Passed' : 'Failed';
    }
}

// Initialize the Elementor integration
function educare_results_emailer_elementor_init() {
    if (did_action('elementor/loaded')) {
        Educare_Results_Emailer_Elementor::get_instance();
    }
}
add_action('plugins_loaded', 'educare_results_emailer_elementor_init'); 