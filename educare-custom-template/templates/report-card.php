<?php
// This template file is included by report-card-template.php
// All variables are already processed and available from the parent template
?>

<!-- DEBUG: BOS Custom Template Loaded -->
<div class="result_body">
    <div class="results-container">
    <!-- Header Banner -->
    <?php if (educare_check_status('show_banner') == 'checked'): ?>
    <div class="bos-banner">
        <?php 
        // Get banner logo with fallback - try logo1 first, then logo2
        $banner_logo = '';
        if ($banner && isset($banner->logo1) && $banner->logo1) {
            $banner_logo = educare_get_attachment($banner->logo1, 'logo1');
        } elseif ($banner && isset($banner->logo2) && $banner->logo2) {
            $banner_logo = educare_get_attachment($banner->logo2, 'logo2');
        }
        
        // If we have a banner logo, display it
        if ($banner_logo) {
            echo '<img src="' . esc_url($banner_logo) . '" alt="School Banner">';
        } else {
            // Show school name as text banner if no logo
            if ($banner && isset($banner->title) && $banner->title) {
                echo '<div style="text-align: center; padding: 20px; background: #573b8a; color: white; font-size: 24px; font-weight: bold;">' . esc_html($banner->title) . '</div>';
            }
        }
        ?>
    </div>

    <!-- School Address -->
    <div class="school-address">
        <p>
            <?php 
            if ($banner && isset($banner->subtitle1) && $banner->subtitle1) {
                echo esc_html($banner->subtitle1);
            }
            ?>
            <?php if ($banner && isset($banner->subtitle1) && $banner->subtitle1 && ($banner->subtitle2 || $banner->subtitle3)): ?>
            <span style="color: #573b8a;">&#9733;</span> 
            <?php endif; ?>
            <?php 
            if ($banner && isset($banner->subtitle2) && $banner->subtitle2) {
                echo esc_html($banner->subtitle2);
            }
            ?>
            <?php if ($banner && isset($banner->subtitle2) && $banner->subtitle2 && $banner->subtitle3): ?>
            <span style="color: #573b8a;">&#9733;</span> 
            <?php endif; ?>
            <?php 
            if ($banner && isset($banner->subtitle3) && $banner->subtitle3) {
                echo esc_html($banner->subtitle3);
            }
            ?>
        </p>
        <hr>
    </div>
    
    
    <?php endif; ?>

    <!-- Student Details Section -->
    <div class="header">
        <div class="student-details-wrapper">
            <table class="student-details-table">
            <tr>
                <td style="background-color: #573b8a; color: #fff; text-align: center;" colspan="8">Student Details</td>
            </tr>
            <tr>
                <td>Name</td>
                <td><?php echo esc_html($student_data->Name); ?></td>
                <td>Class</td>
                <td><?php echo esc_html($student_data->Class); ?></td>
                <td>Admission No</td>
                <td><?php echo esc_html($student_data->Regi_No); ?></td>
                <td rowspan="5">
                    <?php if (educare_check_status('photos') == 'checked' && !empty($others->Photos)): ?>
                        <img src="<?php echo esc_url(educare_get_attachment($others->Photos)); ?>" alt="Student Image">
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Reg. No</td>
                <td><?php echo esc_html($student_data->Regi_No); ?></td>
                <td>Exam</td>
                <td><?php echo esc_html($student_data->Exam); ?></td>
                <td>Academic Year</td>
                <td><?php echo esc_html($student_data->Year); ?></td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td><?php echo esc_html($details->Date_of_Birth ?? ''); ?></td>
                <td>Father's Name</td>
                <td><?php echo esc_html($details->{'Father\'s_Name'} ?? ''); ?></td>
                <td>Father's Phone No</td>
                <td><?php echo esc_html($details->{'Father\'s_Phone_No'} ?? ''); ?></td>
            </tr>
            <tr>
                <td>Mother's Name</td>
                <td><?php echo esc_html($details->{'Mother\'s_Name'} ?? ''); ?></td>
                <td>Mother's Phone No</td>
                <td><?php echo esc_html($details->{'Mother\'s_Phone_No'} ?? ''); ?></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>Parent's Email</td>
                <td><?php echo esc_html($details->Parent_Email ?? ''); ?></td>
                <td>Next Term Begins</td>
                <td style="font-weight: bold;"><?php echo esc_html($details->Next_Term_Begins ?? ''); ?></td>
                <td>School Fees for Next Term</td>
                <td style="font-weight: bold;"><?php echo esc_html($details->School_Fees_for_Next_Term ?? ''); ?></td>
            </tr>
        </table>
        </div>
    </div>
    <br>

    <!-- Grade Sheet Section -->
    <div class="grade-sheet">
        <table>
            <tr>
                <td style="background-color: #573b8a; color: #fff; text-align: center;" colspan="<?php echo $total_columns; ?>">Grade Sheet</td>
            </tr>
            <tr>
                <td>S/N</td>
                <td>Subjects</td>
                <?php foreach ($enabled_fields as $field_key => $field_data): ?>
                    <td><?php echo esc_html($field_data['title']); ?><?php if (isset($field_data['subtitle'])): ?><br><?php echo esc_html($field_data['subtitle']); ?><?php endif; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php
            $serial = 1;
            if (is_object($subjects)) {
                foreach ($subjects as $subject => $fields): ?>
                    <tr>
                        <td><?php echo esc_html($serial++); ?></td>
                        <td><?php 
                            // Remove the class code prefix (e.g., "SSS1-SCI: MATHEMATICS" -> "MATHEMATICS")
                            $subject_name = $subject;
                            if (strpos($subject, ':') !== false) {
                                $subject_name = trim(substr($subject, strpos($subject, ':') + 1));
                            }
                            echo esc_html($subject_name); 
                        ?></td>
                        <?php foreach ($enabled_fields as $field_key => $field_info): ?>
                            <td>
                                <?php
                                // Get the actual value from the student's data
                                $field_value = '';
                                
                                if (isset($fields->$field_key)) {
                                    $field_value = $fields->$field_key;
                                }
                                
                                if (isset($field_info['auto']) && $field_info['auto'] == 'on') {
                                    // Handle auto-generated fields
                                    switch ($field_key) {
                                        case 'term1':
                                        case 'term2':
                                        case 'term3':
                                        case 'exam':
                                            // Show the actual stored value
                                            echo is_numeric($field_value) ? esc_html($field_value) : '';
                                            break;
                                            
                                        case 'ca_total':
                                            // Calculate Total CA (term1 + term2 + term3)
                                            $ca_total = 0;
                                            $has_marks = false;
                                            if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) {
                                                $ca_total += $fields->term1;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) {
                                                $ca_total += $fields->term2;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) {
                                                $ca_total += $fields->term3;
                                                $has_marks = true;
                                            }
                                            echo $has_marks ? esc_html($ca_total) : '';
                                            break;
                                            
                                        case 'total':
                                            // Calculate Total (ca_total + exam)
                                            $ca_total = 0;
                                            $has_marks = false;
                                            if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) {
                                                $ca_total += $fields->term1;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) {
                                                $ca_total += $fields->term2;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) {
                                                $ca_total += $fields->term3;
                                                $has_marks = true;
                                            }
                                            
                                            $exam = isset($fields->exam) && is_numeric($fields->exam) && $fields->exam > 0 ? $fields->exam : 0;
                                            if ($exam > 0) $has_marks = true;
                                            
                                            $total = $ca_total + $exam;
                                            echo $has_marks ? esc_html($total) : '';
                                            break;
                                            
                                        case 'class_average':
                                            // Only show if student has marks for this subject
                                            $has_marks = false;
                                            if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) $has_marks = true;
                                            if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) $has_marks = true;
                                            if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) $has_marks = true;
                                            if (isset($fields->exam) && is_numeric($fields->exam) && $fields->exam > 0) $has_marks = true;
                                            
                                            // Use real class average from StudentResult calculations
                                            echo ($has_marks && isset($class_stats[$subject]['average']) && $class_stats[$subject]['average'] > 0) ? esc_html($class_stats[$subject]['average']) : '';
                                            break;
                                            
                                        case 'highest_in_class':
                                            // Only show if student has marks for this subject
                                            $has_marks = false;
                                            if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) $has_marks = true;
                                            if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) $has_marks = true;
                                            if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) $has_marks = true;
                                            if (isset($fields->exam) && is_numeric($fields->exam) && $fields->exam > 0) $has_marks = true;
                                            
                                            // Use real highest score from StudentResult calculations
                                            echo ($has_marks && isset($class_stats[$subject]['highest']) && $class_stats[$subject]['highest'] > 0) ? esc_html($class_stats[$subject]['highest']) : '';
                                            break;
                                            
                                        case 'lowest_in_class':
                                            // Only show if student has marks for this subject
                                            $has_marks = false;
                                            if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) $has_marks = true;
                                            if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) $has_marks = true;
                                            if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) $has_marks = true;
                                            if (isset($fields->exam) && is_numeric($fields->exam) && $fields->exam > 0) $has_marks = true;
                                            
                                            // Use real lowest score from StudentResult calculations
                                            echo ($has_marks && isset($class_stats[$subject]['lowest']) && $class_stats[$subject]['lowest'] > 0) ? esc_html($class_stats[$subject]['lowest']) : '';
                                            break;
                                            
                                        case 'grade':
                                            // Calculate Grade using Educare's grade system
                                            $ca_total = 0;
                                            $has_marks = false;
                                            if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) {
                                                $ca_total += $fields->term1;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) {
                                                $ca_total += $fields->term2;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) {
                                                $ca_total += $fields->term3;
                                                $has_marks = true;
                                            }
                                            
                                            $exam = isset($fields->exam) && is_numeric($fields->exam) && $fields->exam > 0 ? $fields->exam : 0;
                                            if ($exam > 0) $has_marks = true;
                                            
                                            $total = $ca_total + $exam;
                                            
                                            // Only show grade if student has marks
                                            if ($has_marks) {
                                                echo educare_letter_grade($total);
                                            }
                                            break;
                                            
                                        case 'remark':
                                            // Calculate remark using Educare's grade system
                                            $ca_total = 0;
                                            $has_marks = false;
                                            if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) {
                                                $ca_total += $fields->term1;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) {
                                                $ca_total += $fields->term2;
                                                $has_marks = true;
                                            }
                                            if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) {
                                                $ca_total += $fields->term3;
                                                $has_marks = true;
                                            }
                                            
                                            $exam = isset($fields->exam) && is_numeric($fields->exam) && $fields->exam > 0 ? $fields->exam : 0;
                                            if ($exam > 0) $has_marks = true;
                                            
                                            $total = $ca_total + $exam;
                                            
                                            // Only show remark if student has marks
                                            if ($has_marks) {
                                                // Get grade system to extract remarks
                                                $grade_system = educare_check_status('grade_system');
                                                $current = $grade_system->current;
                                                $grade_system = $grade_system->rules->$current;
                                                $grade_system = json_decode(json_encode($grade_system), true);
                                                
                                                $remark = '';
                                                foreach ($grade_system as $rules => $grade) {
                                                    $rules1 = strtok($rules, '-');
                                                    $rules2 = substr(strstr($rules, '-'), 1);
                                                    
                                                    if ($total >= $rules1 && $total <= add_floatNum($rules2)) {
                                                        $remark = $grade[2]; // Get remark from grade system
                                                        break;
                                                    }
                                                }
                                                
                                                echo esc_html($remark);
                                            }
                                            break;
                                            
                                        default:
                                            // For other auto fields, show the stored value
                                            echo esc_html($field_value);
                                            break;
                                    }
                                } else {
                                    // For non-auto fields, show the stored value
                                    echo esc_html($field_value);
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach;
            }
            ?>
        </table>
    </div>

    <!-- Skills Section -->
    <div class="skills">
        <?php if (educare_check_status('rattings_system') == 'checked' && !empty($others->Rattings)): ?>
            <?php foreach ($others->Rattings as $category => $skills): ?>
                <table>
                    <tr>
                        <td style="background-color: #573b8a; color: #fff; text-align: center;" colspan="2"><?php echo esc_html($category); ?></td>
                    </tr>
                    <?php foreach ($skills as $skill => $rating): ?>
                        <tr>
                            <td><?php echo esc_html($skill); ?></td>
                            <td><?php echo esc_html($rating); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Key to Grading Section -->
    <?php if (educare_check_status('grading_key') == 'checked'): ?>
    <div class="grading-key">
        <table>
            <tr>
                <td style="background-color: #573b8a; color: #fff; text-align: center;" colspan="4">KEY TO GRADING</td>
            </tr>
            <tr>
                <td style="background-color: #f0f0f0; font-weight: bold; text-align: center;">MARKS</td>
                <td style="background-color: #f0f0f0; font-weight: bold; text-align: center;">GRADE</td>
                <td style="background-color: #f0f0f0; font-weight: bold; text-align: center;">POINT</td>
                <td style="background-color: #f0f0f0; font-weight: bold; text-align: center;">REMARK</td>
            </tr>
            <?php
            // Get the grading system from Educare settings
            $grade_system = educare_check_status('grade_system');
            $current = $grade_system->current;
            $grade_rules = $grade_system->rules->$current;
            $grade_rules = json_decode(json_encode($grade_rules), true);
            
            // Display each grading rule
            foreach ($grade_rules as $marks_range => $grade_info):
                $marks_parts = explode('-', $marks_range);
                $min_marks = $marks_parts[0];
                $max_marks = isset($marks_parts[1]) ? $marks_parts[1] : $marks_parts[0];
                $display_range = $min_marks . ' - ' . $max_marks;
                
                $grade_letter = $grade_info[1]; // Grade letter (A, B, C, etc.)
                $grade_point = $grade_info[0];  // Grade point (4.0, 3.5, etc.)
                $grade_remark = $grade_info[2]; // Remark (Excellent, Good, etc.)
            ?>
            <tr>
                <td style="text-align: center;"><?php echo esc_html($display_range); ?></td>
                <td style="text-align: center;"><?php echo esc_html($grade_letter); ?></td>
                <td style="text-align: center;"><?php echo esc_html($grade_point); ?></td>
                <td style="text-align: center;"><?php echo esc_html($grade_remark); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

    <!-- Overview Section -->
    <div class="overview">
        <table>
            <tr>
                <td colspan="6" style="background-color: #573b8a; color: #fff; font-weight: bold; text-align: center;">QUICK OVERVIEW</td>
            </tr>
            <tr>
                <td>Number of Subject</td>
                <td><?php echo esc_html($subjects_offered); ?></td>
                <td>Total Obtainable Marks</td>
                <td><?php 
                    // Calculate total obtainable marks: subjects offered × 100
                    $total_obtainable = $subjects_offered * 100;
                    echo esc_html($total_obtainable);
                ?></td>
                <td>Marks Obtained</td>
                <td><?php 
                    // Calculate marks obtained: sum of total exam scores for each subject
                    $marks_obtained = 0;
                    if (is_object($subjects)) {
                        foreach ($subjects as $subject => $fields) {
                            if (is_object($fields)) {
                                // Calculate total for this subject (CA + Exam)
                                $subject_total = 0;
                                $has_marks = false;
                                
                                if (isset($fields->term1) && is_numeric($fields->term1) && $fields->term1 > 0) {
                                    $subject_total += $fields->term1;
                                    $has_marks = true;
                                }
                                if (isset($fields->term2) && is_numeric($fields->term2) && $fields->term2 > 0) {
                                    $subject_total += $fields->term2;
                                    $has_marks = true;
                                }
                                if (isset($fields->term3) && is_numeric($fields->term3) && $fields->term3 > 0) {
                                    $subject_total += $fields->term3;
                                    $has_marks = true;
                                }
                                if (isset($fields->exam) && is_numeric($fields->exam) && $fields->exam > 0) {
                                    $subject_total += $fields->exam;
                                    $has_marks = true;
                                }
                                
                                // Add to total marks obtained if student has marks for this subject
                                if ($has_marks) {
                                    $marks_obtained += $subject_total;
                                }
                            }
                        }
                    }
                    echo esc_html($marks_obtained);
                ?></td>
            </tr>
            <tr>
                <td>Average</td>
                <td><?php echo esc_html($student_average . '%'); ?></td>
                <td>Position in Class</td>
                <td><?php 
                    if ($position_in_class && $position_in_class != 'N/A') {
                        echo esc_html($position_in_class);
                        if ($total_students > 0) {
                            echo ' out of ' . esc_html($total_students);
                        }
                    } else {
                        echo esc_html($position_in_class);
                    }
                ?></td>
                <td>Passed/Failed</td>
                <td><?php 
                    // Determine if student passed based on overall average
                    // Using Educare's default passing mark (usually 40% or 50%)
                    $passing_mark = 40; // You can adjust this based on your school's policy
                    $status = $student_average >= $passing_mark ? 'PASSED' : 'FAILED';
                    $status_class = $student_average >= $passing_mark ? 'passed' : 'failed';
                    echo '<span class="' . esc_attr($status_class) . '">' . esc_html($status) . '</span>';
                ?></td>
            </tr>
        </table>
    </div>

    <!-- Remarks Section -->
    <div class="remarks">
        <table>
            <tr>
                <td style="background-color: #573b8a; color: #fff; text-align: center;" colspan="6">REMARKS</td>
            </tr>
            <tr>
                <td>Class Teacher's Remark</td>
                <td colspan="5"><?php 
                    // Extract remarks from others column
                    $remarks = array(
                        'teacher' => array(
                            'comments' => '',
                            'name' => '',
                            'date' => '',
                            'signature' => ''
                        ),
                        'principal' => array(
                            'comments' => '',
                            'name' => '',
                            'date' => '',
                            'signature' => ''
                        )
                    );
                    
                    if (isset($others->remarks)) {
                        $remarks = $others->remarks;
                    }
                    
                    echo esc_html($remarks->teacher->comments ?? '');
                ?></td>
            </tr>
            <tr>
                <td>Class Teacher's Name</td>
                <td><?php echo esc_html($remarks->teacher->name ?? ''); ?></td>
                <td>Class Teacher's Signature</td>
                <td>
                    <?php 
                    if (!empty($remarks->teacher->signature)) {
                        $signature = educare_get_attachment($remarks->teacher->signature, true);
                        if ($signature) {
                            echo '<img src="' . esc_url($signature) . '" alt="Class Teacher Signature" style="width: 40px; height: auto; border: 1px solid #ddd; border-radius: 4px;">';
                        }
                    }
                    ?>
                </td>
                <td>Date</td>
                <td><?php echo esc_html($remarks->teacher->date ?? ''); ?></td>
            </tr>
            <tr>
                <td>Principal's Remark</td>
                <td colspan="5"><?php echo esc_html($remarks->principal->comments ?? ''); ?></td>
            </tr>
            <tr>
                <td>Principal's Name</td>
                <td><?php echo esc_html($remarks->principal->name ?? ''); ?></td>
                <td>Principal's Signature</td>
                <td>
                    <?php 
                    if (!empty($remarks->principal->signature)) {
                        $signature = educare_get_attachment($remarks->principal->signature, true);
                        if ($signature) {
                            echo '<img src="' . esc_url($signature) . '" alt="Principal Signature" style="width: 40px; height: auto; border: 1px solid #ddd; border-radius: 4px;">';
                        }
                    }
                    ?>
                </td>
                <td>Date</td>
                <td><?php echo esc_html($remarks->principal->date ?? ''); ?></td>
            </tr>
        </table>
    </div>

    </div>

    <!-- Print button for screen view -->
    <div class="no_print">
        <button onClick="<?php echo esc_js('window.print()');?>" class="print_button"><i class="fa fa-print"></i> Print Report Card</button>
        <button id="educare-undo" class="undo-button" onClick="window.location.href = window.location.href;"><i class="fa fa-undo"></i> Search Again</button>
    </div>
</div> 