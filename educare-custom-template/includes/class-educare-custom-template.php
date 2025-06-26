<?php
/**
 * Main plugin class
 *
 * @package    Educare_Custom_Template
 * @subpackage Educare_Custom_Template/includes
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Performance optimization class for class statistics calculations
 */
class Educare_Custom_Template_Performance {
    
    /**
     * Batch process class statistics for better performance
     *
     * @param object $student_result StudentResult object
     * @param object $subjects Student subjects data
     * @return array Class statistics
     */
    public static function batch_process_class_stats($student_result, $subjects) {
        $class_stats = array();
        
        if (!$student_result || !$subjects) {
            error_log('Educare Custom Template: batch_process_class_stats - student_result or subjects is null');
            return $class_stats;
        }
        
        // Use our fixed calculation method instead of the broken StudentResult methods
        $class_stats = self::fix_student_result_calculations($student_result, $subjects);
        
        // Log the fixed calculations
        foreach ($class_stats as $subject => $subject_stats) {
            error_log("Educare Custom Template: Fixed calculations for $subject - Average: {$subject_stats['average']}, Highest: {$subject_stats['highest']}, Lowest: {$subject_stats['lowest']}");
        }
        
        return $class_stats;
    }
    
    /**
     * Get cached class statistics or calculate and cache them
     *
     * @param object $student_data Student data object
     * @param object $subjects Student subjects data
     * @return array Cached or calculated class statistics
     */
    public static function get_cached_class_stats($student_data, $subjects) {
        // Create cache key based on class, exam, and year
        $cache_key = 'educare_class_stats_' . md5($student_data->Class . $student_data->Exam . $student_data->Year);
        
        // Try to get cached data
        $cached_stats = wp_cache_get($cache_key, 'educare_results');
        
        if ($cached_stats !== false) {
            return $cached_stats;
        }
        
        // Calculate fresh statistics
        $stats = self::calculate_fresh_class_stats($student_data, $subjects);
        
        // Cache for 1 hour (3600 seconds)
        wp_cache_set($cache_key, $stats, 'educare_results', 3600);
        
        return $stats;
    }
    
    /**
     * Calculate fresh class statistics using ONLY real Educare calculations
     *
     * @param object $student_data Student data object
     * @param object $subjects Student subjects data
     * @return array Fresh class statistics
     */
    private static function calculate_fresh_class_stats($student_data, $subjects) {
        $stats = array(
            'class_stats' => array(),
            'position_in_class' => 'N/A',
            'total_students' => 0
        );
        
        // CRITICAL: Only use real Educare StudentResult calculations
        if (!class_exists('StudentResult')) {
            error_log('Educare Custom Template: StudentResult class not found. Cannot calculate real class statistics.');
            return $stats;
        }
        
        try {
            $student_result = new StudentResult($student_data);
            $stats['position_in_class'] = $student_result->positionInClass();
            
            $all_students = $student_result->getStudents(true);
            $stats['total_students'] = is_array($all_students) ? count($all_students) : 0;
            
            if ($stats['total_students'] > 0 && is_object($subjects)) {
                $stats['class_stats'] = self::batch_process_class_stats($student_result, $subjects);
                
                // Log the final calculations
                foreach ($stats['class_stats'] as $subject => $subject_stats) {
                    error_log("Educare Custom Template: Final calculations for $subject - Average: {$subject_stats['average']}, Highest: {$subject_stats['highest']}, Lowest: {$subject_stats['lowest']}");
                }
            } else {
                error_log('Educare Custom Template: No students found or subjects not object');
            }
        } catch (Exception $e) {
            error_log('Educare Custom Template: Error calculating class stats - ' . $e->getMessage());
            // Return empty stats instead of simulated ones
            return $stats;
        }
        
        return $stats;
    }

    /**
     * Manual verification of class statistics calculations
     * This function helps debug and verify that calculations are correct
     *
     * @param object $student_data Student data object
     * @param object $subjects Student subjects data
     * @return array Manual verification results
     */
    public static function manual_verification($student_data, $subjects) {
        $verification = array();
        
        if (!class_exists('StudentResult')) {
            error_log('Educare Custom Template: StudentResult class not found for manual verification');
            return $verification;
        }
        
        try {
            $student_result = new StudentResult($student_data);
            $all_students = $student_result->getStudents(true);
            
            if (!$all_students || !is_array($all_students)) {
                error_log('Educare Custom Template: No students found for manual verification');
                return $verification;
            }
            
            error_log('Educare Custom Template: Manual verification - Found ' . count($all_students) . ' students');
            
            // Manual calculation for each subject
            if (is_object($subjects)) {
                foreach ($subjects as $subject_name => $subject_fields) {
                    error_log("Educare Custom Template: Manual verification for subject: $subject_name");
                    
                    $subject_totals = array();
                    $subject_students = 0;
                    
                    // Calculate manually for each student
                    foreach ($all_students as $student) {
                        $student_subjects = json_decode($student->Subject);
                        if ($student_subjects && isset($student_subjects->$subject_name)) {
                            $student_marks = $student_subjects->$subject_name;
                            
                            // Calculate total marks for this student in this subject
                            $total_marks = 0;
                            $has_marks = false;
                            
                            // Check for different possible field names
                            $possible_fields = array('term1', 'term2', 'term3', 'exam', 'marks');
                            
                            foreach ($possible_fields as $field) {
                                if (isset($student_marks->$field) && is_numeric($student_marks->$field) && $student_marks->$field > 0) {
                                    $total_marks += $student_marks->$field;
                                    $has_marks = true;
                                    error_log("Educare Custom Template: Manual verification - Student {$student->Name} has $field: {$student_marks->$field}");
                                }
                            }
                            
                            if ($has_marks) {
                                $subject_totals[] = $total_marks;
                                $subject_students++;
                                error_log("Educare Custom Template: Manual verification - Student {$student->Name} total marks: $total_marks");
                            }
                        }
                    }
                    
                    if ($subject_students > 0) {
                        $manual_average = array_sum($subject_totals) / $subject_students;
                        $manual_highest = max($subject_totals);
                        $manual_lowest = min($subject_totals);
                        
                        $verification[$subject_name] = array(
                            'manual_average' => round($manual_average, 2),
                            'manual_highest' => $manual_highest,
                            'manual_lowest' => $manual_lowest,
                            'total_students' => $subject_students,
                            'all_totals' => $subject_totals
                        );
                        
                        error_log("Educare Custom Template: Manual verification results for $subject_name:");
                        error_log("  Average: $manual_average");
                        error_log("  Highest: $manual_highest");
                        error_log("  Lowest: $manual_lowest");
                        error_log("  Students: $subject_students");
                        error_log("  All totals: " . implode(', ', $subject_totals));
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log('Educare Custom Template: Error in manual verification - ' . $e->getMessage());
        }
        
        return $verification;
    }

    /**
     * Fix StudentResult calculations by providing correct field names
     * This method overrides the default combineMarks behavior to include 'exam' field
     *
     * @param object $student_result StudentResult object
     * @param object $subjects Student subjects data
     * @return array Fixed class statistics
     */
    public static function fix_student_result_calculations($student_result, $subjects) {
        $class_stats = array();
        
        if (!$student_result || !$subjects) {
            return $class_stats;
        }
        
        // Get all students for manual calculation
        $all_students = $student_result->getStudents(true);
        
        if (!$all_students || !is_array($all_students)) {
            return $class_stats;
        }
        
        // Calculate manually for each subject
        foreach ($subjects as $subject_name => $subject_fields) {
            $subject_totals = array();
            $subject_students = 0;
            
            // Calculate manually for each student
            foreach ($all_students as $student) {
                $student_subjects = json_decode($student->Subject);
                if ($student_subjects && isset($student_subjects->$subject_name)) {
                    $student_marks = $student_subjects->$subject_name;
                    
                    // Calculate total marks for this student in this subject
                    $total_marks = 0;
                    $has_marks = false;
                    
                    // Check for different possible field names
                    $possible_fields = array('term1', 'term2', 'term3', 'exam', 'marks');
                    
                    foreach ($possible_fields as $field) {
                        if (isset($student_marks->$field) && is_numeric($student_marks->$field) && $student_marks->$field > 0) {
                            $total_marks += $student_marks->$field;
                            $has_marks = true;
                        }
                    }
                    
                    if ($has_marks) {
                        $subject_totals[] = $total_marks;
                        $subject_students++;
                    }
                }
            }
            
            if ($subject_students > 0) {
                $manual_average = array_sum($subject_totals) / $subject_students;
                $manual_highest = max($subject_totals);
                $manual_lowest = min($subject_totals);
                
                $class_stats[$subject_name] = array(
                    'average' => round($manual_average, 2),
                    'highest' => $manual_highest,
                    'lowest' => $manual_lowest
                );
            }
        }
        
        return $class_stats;
    }
}

/**
 * Main plugin class
 */
class Educare_Custom_Template {

    /**
     * Initialize the plugin
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Plugin initialization code here
        add_action('init', array($this, 'register_grading_key_setting'), 20);
        add_action('init', array($this, 'add_template'));
    }

    /**
     * Register grading key setting with Educare
     */
    public function register_grading_key_setting() {
        // Check if Educare function exists and add the setting to default settings
        if (function_exists('educare_check_status')) {
            // Hook into Educare's settings initialization
            add_action('admin_init', function() {
                // Get current settings
                $settings = get_option('educare_settings', array());
                
                // Add grading_key setting if it doesn't exist
                if (!isset($settings['grading_key'])) {
                    $settings['grading_key'] = 'unchecked'; // Default to unchecked
                    update_option('educare_settings', $settings);
                }
            });
            
            // Add admin notice to inform about the new setting
            add_action('admin_notices', function() {
                if (isset($_GET['page']) && $_GET['page'] === 'educare-settings') {
                    echo '<div class="notice notice-info is-dismissible">';
                    echo '<p><strong>Educare Custom Template:</strong> A new "Key to Grading" option has been added to Card Settings. You can enable it to show the grading system table on result cards.</p>';
                    echo '</div>';
                }
            });
        }
    }

    /**
     * Add custom template to Educare
     */
    public function add_template($templates) {
        $templates['educare-custom-template'] = array(
            'title' => 'BOS Report Card Template',
            'file' => plugin_dir_path(__FILE__) . '../templates/report-card-template.php',
            'function' => 'educare_custom_template'
        );
        return $templates;
    }

    /**
     * Enqueue scripts and styles for frontend
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'educare-custom-template-style',
            EDUCARE_CUSTOM_TEMPLATE_URL . 'assets/css/report-card-style.css',
            array('educare-results'),
            EDUCARE_CUSTOM_TEMPLATE_VERSION
        );
        
        // Print CSS for A4 optimization
        wp_enqueue_style(
            'educare-custom-template-print-style',
            EDUCARE_CUSTOM_TEMPLATE_URL . 'assets/css/report-card-print.css',
            array('educare-results'),
            EDUCARE_CUSTOM_TEMPLATE_VERSION
        );
    }

    /**
     * Enqueue scripts and styles for admin
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style(
            'educare-custom-template-admin-style',
            EDUCARE_CUSTOM_TEMPLATE_URL . 'assets/css/style.css',
            array(),
            EDUCARE_CUSTOM_TEMPLATE_VERSION
        );
    }
} 