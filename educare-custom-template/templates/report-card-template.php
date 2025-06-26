<?php
/**
 * Custom template for Educare
 *
 * @package    Educare_Custom_Template
 * @subpackage Educare_Custom_Template/templates
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Custom template function for Educare
 *
 * @param object|null $print The student data object containing details like marks, subjects, and remarks.
 * @param bool $template_details Optional. If true, returns the template information. Default is false.
 * @param bool $settings Optional. Not used in this function.
 * @return array|null The template information if $template_details is true, otherwise null.
 */
function educare_custom_template($print = null, $template_details = false, $settings = false) {
    // Debug: Add a simple test
    echo '<!-- DEBUG: educare_custom_template called with: print=' . ($print ? 'YES' : 'NO') . ', template_details=' . ($template_details ? 'YES' : 'NO') . ' -->';

    // Return template information when called by Educare
    if (!$print && $template_details) {
        // Get custom thumbnail
        $custom_thumbnail = get_option('educare_custom_template_thumbnail');
        $thumbnail_url = $custom_thumbnail ? EDUCARE_URL . 'assets/img/' . $custom_thumbnail : EDUCARE_TEMPLATE_THUMBNAIL;

        // Define all terms/fields
        $fields = array(
            'term1' => array(
                'title' => 'CA 1',
                'subtitle' => '20%',
                'status' => 'checked',
                'default_value' => '20',
                'placeholder' => '20',
                'type' => 'number',
                'auto' => 'off',
                'hide' => 'off'
            ),
            'term2' => array(
                'title' => 'CA 2',
                'subtitle' => '20%',
                'status' => 'checked',
                'default_value' => '20',
                'placeholder' => '20',
                'type' => 'number',
                'auto' => 'off',
                'hide' => 'off'
            ),
            'term3' => array(
                'title' => 'CA 3',
                'subtitle' => '20%',
                'status' => 'checked',
                'default_value' => '20',
                'placeholder' => '20',
                'type' => 'number',
                'auto' => 'off',
                'hide' => 'off'
            ),
            'ca_total' => array(
                'title' => 'Total CA',
                'subtitle' => '60%',
                'status' => 'checked',
                'default_value' => '60',
                'placeholder' => '60',
                'type' => 'number',
                'auto' => 'on',
                'hide' => 'off'
            ),
            'exam' => array(
                'title' => 'Exam',
                'subtitle' => '40%',
                'status' => 'checked',
                'default_value' => '40',
                'placeholder' => '40',
                'type' => 'number',
                'auto' => 'off',
                'hide' => 'off'
            ),
            'total' => array(
                'title' => 'Total',
                'subtitle' => '100%',
                'status' => 'checked',
                'default_value' => '100',
                'placeholder' => '100',
                'type' => 'number',
                'auto' => 'on',
                'hide' => 'off'
            ),
            'First_Term_100' => array(
                'title' => '1st Term',
                'subtitle' => '100%',
                'status' => 'checked',
                'default_value' => '100',
                'placeholder' => '100',
                'type' => 'number',
                'auto' => 'off',
                'hide' => 'off'
            ),
            'Second_Term_100' => array(
                'title' => '2nd Term',
                'subtitle' => '100%',
                'status' => 'checked',
                'default_value' => '100',
                'placeholder' => '100',
                'type' => 'number',
                'auto' => 'off',
                'hide' => 'off'
            ),
            'Cumulative_Average_100' => array(
                'title' => 'Cumulative',
                'subtitle' => 'Average',
                'status' => 'checked',
                'default_value' => '100',
                'placeholder' => '100',
                'type' => 'number',
                'auto' => 'on',
                'hide' => 'off'
            ),
            'class_average' => array(
                'title' => 'Class',
                'subtitle' => 'Average',
                'status' => 'checked',
                'default_value' => '100.00',
                'placeholder' => '100.00',
                'type' => 'number',
                'auto' => 'on',
                'hide' => 'off'
            ),
            'highest_in_class' => array(
                'title' => 'Highest',
                'subtitle' => 'in Class',
                'status' => 'checked',
                'default_value' => '100',
                'placeholder' => '100',
                'type' => 'number',
                'auto' => 'on',
                'hide' => 'off'
            ),
            'lowest_in_class' => array(
                'title' => 'Lowest',
                'subtitle' => 'in Class',
                'status' => 'checked',
                'default_value' => '00',
                'placeholder' => '00',
                'type' => 'number',
                'auto' => 'on',
                'hide' => 'off'
            ),
            'grade' => array(
                'title' => 'Grade',
                'subtitle' => '',
                'status' => 'checked',
                'default_value' => 'A',
                'placeholder' => 'A',
                'type' => 'text',
                'auto' => 'on',
                'hide' => 'off'
            ),
            'remark' => array(
                'title' => 'Remarks',
                'subtitle' => '',
                'status' => 'checked',
                'default_value' => 'Excellent',
                'placeholder' => 'Excellent',
                'type' => 'textarea',
                'auto' => 'on',
                'hide' => 'off'
            )
        );

        $template_info = array(
            'title' => 'BOS Report Card Template',
            'thumbnail' => $thumbnail_url,
            'fields' => $fields,
            'import' => true
        );
        return $template_info;
    }

    // For certificate template
    if ($print && $template_details) {
        // Use our custom template's data processing instead of default
        return $print;
    }

    // Process student data using our custom template system
    // Don't use educare_process_default_result_system - use our own processing
    $student_data = $print; // Use the raw data directly

    // Get template settings
    $banner = educare_check_status('banner');
    $info = educare_check_status('details');
    $requred = educare_check_status('display');
    $requred_title = educare_requred_data($requred, true);

    // Get student details and others from the raw data
    $details = isset($student_data->Details) ? $student_data->Details : new stdClass();
    $others = isset($student_data->Others) ? $student_data->Others : new stdClass();
    
    // Ensure details is properly decoded
    if (is_string($details)) {
        $details = json_decode($details);
    }
    
    // Ensure others is properly decoded
    if (is_string($others)) {
        $others = json_decode($others);
    }

    // Get student data and calculate statistics using our custom approach
    // Don't use StudentResult class - use our own calculations
    $subjects = isset($student_data->Subject) ? $student_data->Subject : new stdClass();
    
    // Ensure subjects is properly decoded
    if (is_string($subjects)) {
        $subjects = json_decode($subjects);
    }

    // Calculate total marks and subjects for current student
    $total_marks = 0;
    $total_subjects = 0;
    $subjects_offered = 0;
    $combineMarks = array();
    
    // Process subjects and calculate marks
    if (is_object($subjects)) {
        foreach ($subjects as $subject => $fields) {
            $subject_total = 0;
            $subject_count = 0;
            $has_marks = false;
            
            if (is_object($fields)) {
                foreach ($fields as $field => $value) {
                    if (is_numeric($value) && $value > 0) {
                        $subject_total += $value;
                        $subject_count++;
                        $has_marks = true;
                    }
                }
            }
            
            if ($subject_count > 0) {
                $combineMarks[$subject] = $subject_total;
                $total_marks += $subject_total;
                $total_subjects++;
            }
            
            // Count subjects the student is actually offering
            if ($has_marks) {
                $subjects_offered++;
            }
        }
    }

    // Calculate student's average
    $student_average = $total_subjects > 0 ? round($total_marks / $total_subjects, 2) : 0;

    // Calculate position in class using Educare's StudentResult class with performance optimizations
    $position_in_class = 'N/A';
    $total_students = 0;
    $class_stats = array();
    
    // Use the performance optimization class for better efficiency
    if (class_exists('Educare_Custom_Template_Performance')) {
        $stats = Educare_Custom_Template_Performance::get_cached_class_stats($student_data, $subjects);
        $class_stats = $stats['class_stats'];
        $position_in_class = $stats['position_in_class'];
        $total_students = $stats['total_students'];
    } else {
        // Fallback to direct calculation if performance class is not available
        if (class_exists('StudentResult')) {
            $student_result = new StudentResult($student_data);
            $position_in_class = $student_result->positionInClass();
            $all_students = $student_result->getStudents(true);
            $total_students = is_array($all_students) ? count($all_students) : 0;
            
            if ($total_students > 0 && is_object($subjects)) {
                $class_averages = $student_result->classAverage();
                
                foreach ($subjects as $subject => $fields) {
                    if (is_object($fields)) {
                        $class_stats[$subject] = array(
                            'average' => isset($class_averages[$subject]) ? $class_averages[$subject] : 0,
                            'highest' => $student_result->positionInSubject($subject, 'highest_score'),
                            'lowest' => $student_result->positionInSubject($subject, 'lowest_score')
                        );
                    }
                }
            }
        }
    }

    // Get our custom template's field definitions
    // Get the actual field settings from Educare's card settings
    $results_card = educare_check_status('results_card');
    $grade_sheet_settings = isset($results_card->grade_sheet) ? $results_card->grade_sheet : new stdClass();
    
    // Get the current template's field settings
    $current_template = educare_check_status('results_card_template');
    $template_fields = array();
    
    if (isset($grade_sheet_settings->template->$current_template)) {
        $template_fields = $grade_sheet_settings->template->$current_template;
    } else {
        // Fallback to our default field definitions if no settings found
        $template_fields = array(
            'term1' => (object)array(
                'title' => 'CA 1',
                'subtitle' => '20%',
                'status' => 'checked'
            ),
            'term2' => (object)array(
                'title' => 'CA 2',
                'subtitle' => '20%',
                'status' => 'checked'
            ),
            'term3' => (object)array(
                'title' => 'CA 3',
                'subtitle' => '20%',
                'status' => 'checked'
            ),
            'ca_total' => (object)array(
                'title' => 'Total CA',
                'subtitle' => '60%',
                'status' => 'checked'
            ),
            'exam' => (object)array(
                'title' => 'Exam',
                'subtitle' => '40%',
                'status' => 'checked'
            ),
            'total' => (object)array(
                'title' => 'Total',
                'subtitle' => '100%',
                'status' => 'checked'
            ),
            'class_average' => (object)array(
                'title' => 'Class',
                'subtitle' => 'Average',
                'status' => 'checked'
            ),
            'highest_in_class' => (object)array(
                'title' => 'Highest',
                'subtitle' => 'in Class',
                'status' => 'checked'
            ),
            'lowest_in_class' => (object)array(
                'title' => 'Lowest',
                'subtitle' => 'in Class',
                'status' => 'checked'
            ),
            'grade' => (object)array(
                'title' => 'Grade',
                'subtitle' => '',
                'status' => 'checked'
            ),
            'remark' => (object)array(
                'title' => 'Remarks',
                'subtitle' => '',
                'status' => 'checked'
            )
        );
    }

    // Debug: Check template info
    // echo '<!-- DEBUG: Template fields count: ' . count($template_fields) . ' -->';

    // Use the actual field settings from Educare
    $enabled_fields = array();
    foreach ($template_fields as $field_key => $field_info) {
        if (isset($field_info->status) && $field_info->status == 'checked') {
            // Determine if field is auto-calculated
            $is_auto = false;
            if (in_array($field_key, ['ca_total', 'total', 'class_average', 'highest_in_class', 'lowest_in_class', 'grade', 'remark'])) {
                $is_auto = true;
            }
            
            $enabled_fields[$field_key] = array(
                'title' => $field_info->title,
                'subtitle' => $field_info->subtitle,
                'status' => $field_info->status,
                'auto' => $is_auto ? 'on' : 'off'
            );
        }
    }

    // Calculate total columns for colspan
    $total_columns = count($enabled_fields) + 2; // +2 for S/N and Subjects columns

    // Use the report-card.php template file
    $template_path = __DIR__ . '/report-card.php';
    
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        // Fallback to default template if file doesn't exist
        echo '<div class="error">Template file not found: ' . esc_html($template_path) . '</div>';
    }
}

// Apply or Install template
// Hook the function to the educare_results_card_template card action
add_action('educare_results_card_template', 'educare_custom_template', 1); 