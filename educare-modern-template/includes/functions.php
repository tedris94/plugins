<?php
// Add custome sysle for this template
function educare_modern_template__style() {
	wp_enqueue_style('educare-modern-template', dirname( plugin_dir_url( __FILE__ ) ).'/assets/css/style.css', array('educare-results'), '', '');
}

add_action('wp_enqueue_scripts', 'educare_modern_template__style');

/**
 * Fetch and process modern student data.
 *
 * @param stdClass $print The student data as an object.
 * @return stdClass The modified student data object.
 */
function educare_get_modern_student_data($print) {
	// Decode JSON properties of $print into PHP objects
	$print->Details = json_decode($print->Details);
	$print->Others = json_decode($print->Others);
	$Subject = json_decode($print->Subject);

	if ($Subject) {
		// We use Educare pre build class StudentResult(). So we just provide student data ($print) into StudentResult() for getting combineMarks, classAverage, highestInClass, lowestInClass, positionOutofAllStudents. also getAllStudent to get all students based on Class, Exam, Year

		// 1. combineMarks: We need to combine value in Subject (based on term1, term2, term3 and marks
		// 2. classAverage: Total number of marks obtained by others students in educare_results (based on current student - Class, Exam and Year) in that subject divided by the number of students taking the subject.
		// 3. highestInClass: bassed on this student previous exam in same class combained marks
		// 4. lowestInClass: same as highestInClass. but here we need to lowest
		// 5. positionOutofAllStudents: Position in class is determined by all student (in educare_results based on current student - Class, Exam and Year) with highest combained marks. For example, if the highest combained marks is 88, that student is automatically 1st in class. If someone has 87, they are automatically 2nd in class.

		// Create a new StudentResult object using $print
		$student = new StudentResult($print);

		// Calculate combined marks and class average
		// arg of combineMarks($subjectData = null, $optional = false, $combine_terms = null, $average = false)
		/**
		 * custome combine
		 $combine_terms = array ( 
			'term1',
			'term2',
		);
		
		$combineMarks = $student->combineMarks('', '', $combine_terms);
		 */

		$combineMarks = $student->combineMarks();
		$classAverage = $student->classAverage();
		$combineCATotals = $student->combineCATotals();
		$combineCumulativeTerms = $student->combineCumulativeTerms();

		// Set 'Result' and 'GPA' properties based on student's status
		$print->Result = $student->getStatus('Result');
		$print->GPA = $student->getStatus('GPA');

		// Get the total number of subjects
		
		// Calculate total subjects based on filled data
		$total_subject = 0;

		foreach ((array)$Subject as $subject => $fields) {
   	   // Check if the subject has filled data
    	foreach ($fields as $key => $value) {
      	  if (!empty($value)) { // Increment if any field has non-empty data
            $total_subject++;
            break; // Count the subject only once and move to the next
       		 }
 		}
	}

// Create a quick overview array with various statistics
$quick_overview = array(
    'total_subject' => $total_subject,
    'total_obtainable_marks' => $total_subject . '00',
    'marks_obtainable' => $student->getAverage($Subject, true),
    'average' => $student->getAverage($Subject),
    'position_in_class' => $student->positionInClass(),
    'out_of' => count($student->getStudents(true)),
);






		// Add quick_overview property to Others object
		$print->Others->quick_overview = (object)$quick_overview;

		// Fetch terms and fields based on a template
		$terms = educare_get_template_data('fields', false);

		// Process and update subject data based on the template
		foreach ($Subject as $subject => $fields) {
			foreach ($terms as $term => $termInfo) {
				if (property_exists($fields, $term)) {
					$value = sanitize_text_field($fields->$term);
				} else {
					$value = '';
				}

				// Handle auto-generated fields (e.g., GPA, grade, combine, class_average, etc.)
				if ($termInfo['auto'] == 'on') {
					// if ($term == 'gpa' or $term == 'grade') {
					// 	$value = $combineMarks[$subject];
						
					// 	if ($term == 'gpa') {
					// 		$value = educare_letter_grade($value, true);
					// 	} else {
					// 		$value = educare_letter_grade($value);
					// 	}
					// }

					if (in_array($term, ['gpa', 'grade', 'remark', 'obtainable_mark'])) {
						$value = $combineMarks[$subject]; // Combined marks for the subject
				
						if ($term == 'gpa') {
							$value = educare_letter_grade($value, true); // Get GPA (point grade)
						} elseif ($term == 'grade') {
							$value = educare_letter_grade($value); // Get letter grade
						} elseif ($term == 'remark') {
							// Fetch remarks from the grade system
							$grade_system = educare_check_status('grade_system');
							$current = $grade_system->current;
							$grade_system = $grade_system->rules->$current;
							$grade_system = json_decode(json_encode($grade_system), true);
				
							foreach ($grade_system as $rules => $grade) {
								$rules1 = strtok($rules, '-'); // Get lower bound
								$rules2 = substr(strstr($rules, '-'), 1); // Get upper bound
				
								if ($value >= $rules1 && $value <= $rules2) {
									$value = $grade[2]; // Get the remark
									break;
								}
							}
						} elseif ($term == 'obtainable_mark') {
							// Static value for obtainable marks
							$value = 100; // Define constant maximum mark
						}
						
					}

					//cumulativeAverage

					if ($term == 'Cumulative_Average_100') {
						$value = $combineCumulativeTerms[$subject];
					}

					// if ($term == 'Cumulative_Average_100') {
					// 	// Check if getCumulativeAverage returns a valid value
					// 	$cumulativeAverageValue = $student->getCumulativeAverage($Subject);
					
					// 	if (is_array($cumulativeAverageValue)) {
					// 		// Ensure we are using the correct subject's cumulative average
					// 		if (isset($cumulativeAverageValue[$subject])) {
					// 			$value = $cumulativeAverageValue[$subject];
					// 		} else {
					// 			$value = 0; // Default value if subject not found
					// 		}
					// 	} elseif (is_numeric($cumulativeAverageValue)) {
					// 		// Handle case where cumulative average is returned as a single numeric value
					// 		$value = $cumulativeAverageValue;
					// 	} else {
					// 		$value = 0; // Default fallback
					// 	}
					// }
					




					// Set specific value
					if ($term == 'combine') {
						$value = $combineMarks[$subject];
					}

					if($term == 'term4'){
						$value = $combineCATotals[$subject];
					}

					if ($term == 'class_average') {
						$value = $classAverage[$subject];
					}
					if ($term == 'highest_in_class') {
						/**
						 * when a student participates in an exam for the first time, that is his highest and lowest score. Because, here he has no other (exam) data. And when adding another result it will calculate the highest and lowest score by comparing it with the previous result. Basically it is based on previous results.
						 * 
						 * Eg: A student got 75 in Bengali in Exam 1. This is currently his highest and lowest score. And that student got 80 in Bengali in Exam 2. Now its highest is 80 and lowest is 75. After that he got 50 in Bengali in Exam 3 now his highest (as per previous result) is 80 and lowest will be 50.
						 */
						// Based on speciic (current) students
						// $value = $student->highestInClass($subject);

						/**
						 * Highest score based on overall students in that particular subject
						 * Highest in class is the student with the highest score in that subject. 
						 * Lowest in class is the student with lowest score in that subject.
						 * For example, if we took an English subject and we are 5 in class, highest score is 83 and lowest is 23. 
						 * In that column for Highest, the answer will be 83 and the lowest will be 23
						 */
						$value = $student->positionInSubject($subject, 'highest_score');
					}
					if ($term == 'lowest_in_class') {
						// Based on speciic (current) students
						// $value = $student->highestInClass($subject, true);

						// based on overall students
						$value = $student->positionInSubject($subject, 'lowest_score');
					}
					if ($term == 'position') {
						$value = $student->positionInSubject($subject);
					}
				}

				// Update the subject's term with the calculated value
				$Subject->$subject->$term = $value;
			}
		}

		// Update the $print object with the modified subject data
		$print->Subject = $Subject;
	}

	// Return the modified student data object
	return $print;
}




/**
 * Generate and display the modern student grade sheet with ratings (if available).
 *
 * @param stdClass $student_data The student data as an object.
 * @return void
 */
function educare_get_modern_marks_terms($student_data) {
	// Extract subject data from the student_data object
	$subject = $student_data->Subject;
	
	// Fetch template data for terms fields
	$terms = educare_get_template_data('fields', false);
	
	// Fetch results_card data and checked grade_sheet data
	$results_card = educare_check_status('results_card');
	$default_terms = educare_checked_data($results_card->grade_sheet, false);
	$default_terms = json_decode(json_encode($default_terms), true);

	// Display the title and subtitle if 'grade_sheet' is defined in template data
	if (key_exists('grade_sheet', $default_terms)) {
		$subtitle = '';

		if (key_exists('subtitle', $default_terms['grade_sheet'])) {
			$subtitle = $default_terms['grade_sheet']['subtitle'];
			if ($subtitle) {
				$subtitle = '<small clss="subtitle">' . esc_html($subtitle) . '</small>';
			}
		}

		echo '<h2 class="head">' . esc_html($default_terms['grade_sheet']['title']) . wp_kses_post($subtitle) . '</h2>';
	}

	// Initialize variables for ratings data
	$rattings = $fullWidth = '';
	$rattings_system = educare_check_status('rattings_system');

	// Fetch rattings data if 'rattings_system' is checked and available in Others object
	if ($rattings_system == 'checked') {
		if (property_exists($student_data, 'Others')) {
			$others = $student_data->Others;
			if (property_exists($others, 'Rattings')) {
				$rattings = $others->Rattings;
			}
		}
	}

	// Set full width class if rattings data is not available
	if (!$rattings) {
		$fullWidth = 'w-100';
	}

	// Start HTML output for the main grade sheet table
	?>

	<div class="row">
		<div class="col-md-9 <?php echo esc_attr($fullWidth); ?>">
			<div class="table-responsive">
				<table class="grade_sheet list">
					<thead>
						<tr>
							<?php
							// Generate table headers for default_terms and terms
							educare_grade_sheet_th($default_terms);
							educare_grade_sheet_th($terms);
							// Uncomment the line below to add an extra header
							// echo '<th>Some Extra Header</th>';
							?>
						</tr>
					</thead>

					<tbody>
						<?php
						if ($subject) {
							// Initialize serial number for subjects
							$serialNo = 1;

							// Loop through each subject and its fields
							foreach ($subject as $sub_name => $fields) {
								$optional = $value = '';
								
								// Check if optional field is defined for the subject
								if (property_exists($fields, 'optional')) {
									$optional = educare_check_status('optional_sybmbol');
									$optional = sanitize_text_field($fields->optional) ? ' ' . $optional : '';
								}

								// Start a new row for each subject
								echo '<tr>';

								// Loop through default_terms and display the corresponding data
								foreach ($default_terms as $term => $termInfo) {
									if ($term == 'grade_sheet') continue;
									if ($term == 'no') echo '<td>' . esc_html($serialNo++) . '</td>';
									if ($term == 'subject') echo '<td>' . esc_html($sub_name . $optional) . '</td>';

									if (property_exists($fields, $term)) {
										echo '<td>' . esc_html($fields->$term) . '</td>';
									}
								}

								// Loop through terms and display corresponding data
								foreach ($terms as $term => $termInfo) {
									if (property_exists($fields, $term)) {
										if ($term == 'gpa' or $term == 'grade') {
											$value = wp_kses_post($fields->$term);
										} else {
											$value = sanitize_text_field($fields->$term);
										}
									}

									echo '<td>' . wp_kses_post($value) . '</td>';
								}

								// End row for the subject
								echo '</tr>';
							}
						} else {
							// Subject not found
							$span = count($default_terms) + count($terms);
							echo '<tr><td colspan="' . esc_attr($span) . '">No Data</td></tr>';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>

		<?php


		// Rattings system
		if ($rattings) {
			echo '<div class="col-md-3">';

			// Loop through rattings and display them in separate tables
			foreach ($rattings as $term => $remark) {
				echo '<div class="table-responsive">
				<table class="grade_sheet rattings">
				<thead>
					<tr>
						<th>' . esc_html($term) . '</th>
						<th>Rating</th>
					</tr>
				</thead>
				<tbody>
				';

				// Loop through each remark and its rattings
				foreach ($remark as $remark_title => $rattings) {
					echo '<tr>
						<td>' . esc_html($remark_title) . '</td>
						<td class="rattings-status">' . esc_html($rattings) . '</td>
					</tr>';
				}

				echo '</tbody></table></div>';
			}
			echo '</div>';
		} else {
			// Display a message if rattings are not available
			if ($rattings_system == 'checked') {
				echo '<div class="text-center text-danger">Rating data not found</div>';
			}
		}
		?>
	</div>
	<?php
}


?>