<?php
/**
 * Test file for verifying class statistics calculations
 * 
 * This file helps debug and verify that the class statistics calculations are working correctly.
 * You can run this file to see detailed information about the calculations.
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if we're in the right context
if (!defined('ABSPATH')) {
    die('Direct access not allowed');
}

// Check if Educare is active
if (!function_exists('educare_check_status')) {
    die('Educare plugin is not active');
}

// Check if StudentResult class exists
if (!class_exists('StudentResult')) {
    die('StudentResult class not found. Please ensure Educare is properly installed.');
}

echo "<h1>Educare Custom Template - Class Statistics Test (FIXED)</h1>";

// Test with your specific data
$test_data = (object) array(
    'Class' => 'SSS 1 SCIENCE',
    'Exam' => '2ND TERM EXAM',
    'Year' => '2024/2025'
);

try {
    $student_result = new StudentResult($test_data);
    $all_students = $student_result->getStudents(true);
    
    echo "<h2>Found " . count($all_students) . " students</h2>";
    
    if (count($all_students) > 0) {
        echo "<h3>Student Data:</h3>";
        foreach ($all_students as $student) {
            echo "<h4>Student: {$student->Name}</h4>";
            $subjects = json_decode($student->Subject);
            if ($subjects) {
                foreach ($subjects as $subject => $marks) {
                    echo "<p><strong>$subject:</strong> ";
                    $total = 0;
                    $fields = array();
                    foreach ($marks as $field => $value) {
                        if (is_numeric($value) && $value > 0) {
                            $total += $value;
                            $fields[] = "$field: $value";
                        }
                    }
                    echo implode(', ', $fields) . " = <strong>Total: $total</strong></p>";
                }
            }
        }
        
        // Test our fixed calculations
        echo "<h3>Fixed Calculations (Our Custom Template):</h3>";
        if (class_exists('Educare_Custom_Template_Performance')) {
            $subjects = json_decode($all_students[0]->Subject);
            $fixed_stats = Educare_Custom_Template_Performance::fix_student_result_calculations($student_result, $subjects);
            
            foreach ($fixed_stats as $subject => $stats) {
                echo "<p><strong>$subject:</strong> Average: {$stats['average']}, Highest: {$stats['highest']}, Lowest: {$stats['lowest']}</p>";
            }
        }
        
        // Test original StudentResult calculations (for comparison)
        echo "<h3>Original StudentResult Calculations (BROKEN):</h3>";
        $original_averages = $student_result->classAverage();
        foreach ($original_averages as $subject => $average) {
            $highest = $student_result->positionInSubject($subject, 'highest_score');
            $lowest = $student_result->positionInSubject($subject, 'lowest_score');
            echo "<p><strong>$subject:</strong> Average: $average, Highest: $highest, Lowest: $lowest</p>";
        }
        
        echo "<h3>Summary:</h3>";
        echo "<p><strong>ISSUE IDENTIFIED:</strong> The original StudentResult calculations only include CA marks (term1 + term2 + term3) but NOT the exam marks!</p>";
        echo "<p><strong>SOLUTION:</strong> Our custom template now correctly calculates total marks (CA + exam) for accurate class statistics.</p>";
        
    } else {
        echo "<p>No students found for the specified criteria.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Test completed. Check the results above to verify the calculations are now correct.</em></p>";
?> 