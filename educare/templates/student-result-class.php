<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * The StudentResult class handles various calculations and operations related to student results.
 */
class StudentResult {
	private $data;

	/**
	 * Constructor for the StudentResult class.
	 *
	 * @param object $print The student data object containing details like marks, subjects, and remarks.
	 */
	public function __construct($print) {
		$this->data = $print;
	}

	/**
	 * Get students based on current student data.
	 *
	 * @param bool $all Whether to retrieve all students' data or current students' data.
	 * @return array An array of student data.
	 */
	public function getStudents($all = false) {
		global $wpdb;
		$requred_fields = array();

		if ($all) {
			if (is_array($all)) {
				if (educare_is_associative_array($all)) {
					foreach ($all as $fields_key => $value) {
						if (property_exists($this->data, $fields_key)) {
							$requred_fields[$fields_key] = $value;
						} else {
							$requred_fields[$fields_key] = '';
						}
					}
				} else {
					foreach ($all as $value) {
						if (property_exists($this->data, $value)) {
							$requred_fields[$value] = $this->data->$value;
						} else {
							$requred_fields[$value] = '';
						}
					}
				}
				
			} else {
				// All Students data
				// Assuming the class, exam, and year values are already defined
				$requred_fields['Class'] = sanitize_text_field( $this->data->Class );
				$requred_fields['Exam'] = sanitize_text_field( $this->data->Exam );
				$requred_fields['Year'] = sanitize_text_field( $this->data->Year );
			}
			
		} else {
			// Current students (previous exam) data
			$requred = educare_check_status('display');
			// Getting all requered field key and title
			$requred_fields = educare_requred_data($requred, true);
			$requred_fields = educare_get_requred_fields('results', $requred_fields);
			unset($requred_fields['Exam']);
		}

		$sql = array();
		foreach ($requred_fields as $key => $value) {
			// $sql[$key] = sanitize_text_field( $this->data->$key );
			$sql[$key] = sanitize_text_field( $value );
		}

		$sql = educare_get_sql_new('results', $sql);
		$allStudents = $wpdb->get_results($sql);
		
		return $allStudents;
	}

	/**
	 * Combine different terms marks into one value.
	 *
	 * @param object|null $subjectData Optional. The subject data containing marks for each term.
	 * @param bool $optional Whether to include optional subjects.
	 * @param array|null $combine_terms Optional. An array of terms to combine marks from.
	 * @param bool $average Whether to calculate the average.
	 * @return array An array of combined marks.
	 */
	public function combineMarks($subjectData = null, $optional = false, $combine_terms = null, $average = false) {
		if (!$subjectData) {
			$subjectData = json_decode($this->data->Subject);
		}
    
    $combineMarks = array();

		if (!$combine_terms) {
			// Default combine terms
			$combine_terms = array ( 
				'term1',
				'term2',
				'term3',
				'marks'
			);
		}

		if ($average) {
			$total_terms = count($combine_terms);
		}

    foreach ($subjectData as $subject => $marks) {
			$combine = 0;
			$hasData = false; // Flag to track if there's valid data for this subject

			foreach ($combine_terms as $term) {
				if (property_exists($marks, $term)) {
					if ($marks->$term) {
						$combine += $marks->$term;
						$hasData = true;
					}
				}
			}

			if ($average && $hasData) {
				$combine = round($combine / $total_terms, 2);
			}
		
		   // If no data exists, set combine to null
		   if (!$hasData) {
            $combine = null;
        }

			if ($optional) {
				$marks->combine = $combine;
				$combineMarks[$subject] = $marks;
			} else {
				$combineMarks[$subject] = $combine;
			}
			
    }

    return $combineMarks;
	}



	



		/**
 * Combine Continuous Assessments (CAs) or tests into one value.
 *
 * This function computes the total of all CAs/tests across specified terms,
 * excluding the final marks.
 *
 * @param object|null $subjectData Optional. The subject data containing marks for each term.
 * @param array|null $ca_terms Optional. An array of terms to combine CAs/tests from.
 * @return array An array of combined CA/test totals.
 */
public function combineCATotals($subjectData = null, $ca_terms = null) {
    if (!$subjectData) {
        $subjectData = json_decode($this->data->Subject);
    }

    $combineCAs = array();

    if (!$ca_terms) {
        // Default CA terms (e.g., tests/assessments but not final marks)
        $ca_terms = array(
            'term1',
            'term2',
            'term3'
        );
    }

    foreach ($subjectData as $subject => $marks) {
        $combine = 0;
		$hasData = false; // Flag to track if there's valid data for this subject

        foreach ($ca_terms as $term) {
            if (property_exists($marks, $term)) {
                if ($marks->$term) {
                    $combine += $marks->$term;
					$hasData = true; // Mark that valid data is present
                }
            }
        }

        // If data exists, set the total; otherwise, leave it blank
        $combineCAs[$subject] = $hasData ? $combine : '';
    }

    return $combineCAs;
}



	/**
 * Combine Cumulative_Aggregate_100 for 3rd term, 2nd term, and 1st term  into one value.
 *
 * This function computes the total of all 3rd, 2nd, and 3rd term across specified terms,
 * excluding the final marks.
 *
 * @param object|null $subjectData Optional. The subject data containing marks for each term.
 * @param array|null $combine_terms Optional. An array of terms to combine sum all the 3 terms.
 * @return array An array of combined CA/test totals.
 */

public function combineCumulativeTerms($subjectData = null, $optional = false, $combine_terms = null, $average = true) {
    if (!$subjectData) {
        $subjectData = json_decode($this->data->Subject);
    }

    $cumulativeMarks = array();

    if (!$combine_terms) {
        // Default terms for cumulative calculation (current term + 2nd and 3rd terms)
        $combine_terms = array(
            'term1',  // Current term
            'term2',  // Current term
            'term3',  // Current term
            'marks',  // Current term
            'Second_Term_100',  // Second term
            'Third_Term_100'   // Third term
        );
    }

	

	if ($average) {
		
        $total_terms = 3; // Total terms for averaging
    }

    foreach ($subjectData as $subject => $marks) {
        $cumulative = 0;

        foreach ($combine_terms as $term) {
            if (property_exists($marks, $term)) {
                if ($marks->$term) {
                    $cumulative += $marks->$term; // Add marks for the specified terms
                }
            }
        }

		if ($average && $total_terms > 0) {
            $cumulative = round($cumulative / $total_terms, 2); // Calculate and round the average to 2 decimal places
        }

        if ($optional) {
            $marks->cumulative = $cumulative;
            $cumulativeMarks[$subject] = $marks;
        } else {
            $cumulativeMarks[$subject] = $cumulative;
        }

		
    }

    return $cumulativeMarks;
}




/**
 * Calculate the average marks obtained by a student.
 *
 * Getting average marks obtained by student (based on combined marks).
 * 
 * @param object|null $subjectData Optional. The subject data containing marks.
 * @param bool $total Whether to return the total marks.
 * @return float|int The calculated average marks.
 */
public function getAverage($subjectData = null, $total = false) {
    if (!$subjectData) {
        $subjectData = json_decode($this->data->Subject);
    }
    
    $subjects = $this->combineMarks($subjectData);
    $average = $failed = $failed_marks = 0;
    $validSubjects = 0; // Count of subjects with scores greater than 0

    if ($subjects) {
        foreach ($subjects as $marks) {
            if ($marks > 0) { // Count only subjects with valid scores
                $validSubjects++;
                $average += $marks;

                if ($marks <= 32) { // Check for failed marks
                    $failed++;
                    $failed_marks += $marks;
                }
            }
        }

        // Return total marks
        if ($total) {
            return $average;
        }

        // Avoid division by zero
        if ($validSubjects > 0) {
            // Average marks. Divide by total valid subjects
            $average /= $validSubjects;
        } else {
            return 0; // No valid subjects to calculate average
        }

        // Handle failed subjects and formatting
        if ($failed) {
            if (educare_check_status('position_filtering') == 'checked') {
                return '('.-esc_html($failed).') ' . round($average, 2);
            } else {
                return round($average, 2);
            }
        } else {
            return round($average, 2);
        }
    }

    return 0; // No subjects found
}



	/**
	 * Calculate the class average for specific subjects.
	 *
	 * Getting specific subject class average. Total number of marks obtained by students in that subject divided by the number of students taking the subject.
	 * 
	 * @return array An array of class averages for each subject.
	 */
	public function classAverage() {
		$allStudents = $this->getStudents(true);
    $totalMarks = array();

    foreach ($allStudents as $student) {
			$subjects = json_decode($student->Subject);
			$combineMarks = $this->combineMarks($subjects);
			
			// All Marks
			foreach ($combineMarks as $sub => $marks) {
				// Check only first loop
				if (isset($totalMarks[$sub])) {
          $totalMarks[$sub]['marks'] += $marks;
        } else {
          $totalMarks[$sub]['marks'] = $marks;
        }

			}

			// All Students
			foreach ($subjects as $subject => $details) {
				if (isset($totalMarks[$subject]['students'])) {
					$totalMarks[$subject]['students']++;
				} else {
					$totalMarks[$subject]['students'] = 1;
				}
			}

    }

		$classAverage = array();

		foreach ($totalMarks as $subject => $marks) {
			$classAverage[$subject] = round($marks['marks'] / $marks['students'], 2);
		}

		return $classAverage;
	}

	/**
	 * Get the highest or lowest marks in a specific subject.
	 *
	 * Highest in class is the student with the highest score in the subject and lowest is the student with the lowest score.
	 * 
	 * @param string $subject_name The name of the subject.
	 * @param bool $lowestInClass Whether to find the lowest in the class.
	 * @return int The highest or lowest marks in the subject.
	 */
	public function highestInClass($subject_name, $lowestInClass = false) {
		$allStudents = $this->getStudents();
		
		if ($allStudents) {
      $all_results = array();

      foreach ($allStudents as $studentData) {
        // $student_id = $studentData->id;
        $subjects = json_decode($studentData->Subject);
        $sub_marks = array();
				
        if ($subjects) {
          $combine_marks = $this->combineMarks($subjects);

          foreach ($subjects as $sub_name => $marks) {
            // $combine['id'] = $student_id;
            $sub_marks[$sub_name] = $combine_marks[$sub_name];
          }

        }
        
        $all_results[] = $sub_marks;
      }

      if ($subject_name) {
        $highest = PHP_INT_MIN;
        $lowest = PHP_INT_MAX;

        foreach ($all_results as $result) {
          if (isset($result[$subject_name])) {
            $marks = $result[$subject_name];

            if ($marks > $highest) {
              $highest = $marks;
              // return $marks;
            }
              
            if ($marks < $lowest) {
              $lowest = $marks;
              // return $marks;
            }

          } else {
            // set default value
            $highest = $lowest =  0;
          }

        }

        // echo "Highest marks in $subject_name: $highest\n";
        // echo "Lowest marks in $subject_name: $lowest\n";

        if ($lowestInClass) {
					return $lowest;
        } else {
					return $highest;
        }

      }
    }
	}
	
	/**
	 * Calculate the position in subject based on the highest average.
	 *
	 * Position in subject is determined by specific subject with highest average. For example, if the highest average is 88%, that subject (who taking the subject) is automatically 1st in class. If someone has 87%, they are automatically 2nd in class.
	 * 
	 * @param string $subject_name The name of the subject.
	 * @param bool $all get total student in specific subject.
	 * @return int The position in the subject.
	 */
	public function positionInSubject($subject_name, $all = false) {
		$allStudents = $this->getStudents(true);

		if ($allStudents) {
			$id = $this->data->id;
			$combine1 = array();
			$no = 1;

			foreach ($allStudents as $key => $student_data) {
				$student_id = $student_data->id;
				$subject = json_decode($student_data->Subject);
				$combine = array();

				if ($subject) {
					foreach ($subject as $name => $marks) {
						if ($name != $subject_name) {
							continue;
						}

						$combine['id'] = $student_id;
						$combine_marks = $this->combineMarks($subject);
						$combine[$name] = $combine_marks[$name];
					}
				}
				
				// if only subject in
				if ($combine) {
					$combine1[$no++] = $combine;
				}
				
			}

			// if ($all) return count($combine1);

			if ($combine1) {
				// Sort array by stock in descending order
				$combined_data = educare_sort_array_by_key($combine1, $subject_name);

				if ($all) {
					if ($all == 'highest_score') {
						return $combined_data[0][$subject_name];
					} elseif ($all == 'lowest_score') {

						  // Filter out zero scores
						  $non_zero_scores = array_filter($combined_data, function($entry) use ($subject_name) {
							return isset($entry[$subject_name]) && $entry[$subject_name] > 0;
						});
					       // Return the lowest score greater than 0
						   if (!empty($non_zero_scores)) {
							return end($non_zero_scores)[$subject_name];
						} else {
							return null; // No valid scores
						}
					} else {
						return $combined_data;
					}
				}

				foreach ($combine1 as $key => $val) {
					// If compare not found
					if (!key_exists($subject_name ,$val)) {
						return 1;
					}
				}

				

				foreach ($combined_data as $pos => $value) {
					if ($value['id'] == $id) {
						// addition 1, because array start at 0
						return $pos + 1;
					}
				}

			}
		}
	}

	 /**
	 * Calculate the position in class based on the highest average.
	 *
	 * Position in class is determined by student with highest average. For example, if the highest average is 88%, that student is automatically 1st in class. If someone has 87%, they are automatically 2nd in class.
	 * 
	 * @return int The position in the class.
	 */
	public function positionInClass() {
		$allStudents = $this->getStudents(true);
		$student_id =  $this->data->id;

		if ($allStudents) {
			$serial = array();

			foreach ($allStudents as $student_data) {
				$id = $student_data->id;
				$subject = json_decode($student_data->Subject);
				$serial[$id] = $this->getAverage($subject);
			}

			// Sort by average marks
			// arsort($serial);
			$serial = educare_position_filtering($serial);
			$position = 0;

			foreach ($serial as $pos_id => $val) {
				$position++;
				if ($pos_id == $student_id) return $position;
			}

		}
	}

	/**
	 * Calculate the GPA (Grade Point Average) for a student.
	 *
	 * @param array|null $subjects Optional. An array of subjects and marks.
	 * @return float The calculated GPA.
	 */
	public function getGPA($subjects) {
		if (!$subjects) {
			$subjects = $this->combineMarks('', true);
		}
		
		$subjectList = array();
		
		foreach ($subjects as $sub => $value) {
			$subjectList[$sub]['gpa'] = educare_letter_grade($value->combine, true);
			$subjectList[$sub]['optional'] = $value->optional;
		}
	
		$optinal_mark = 0;
	
		foreach ($subjectList as $sub => $value) {
			if ($value['optional']) {
				if ($value['gpa'] > 2) {
					$optinal_mark += $value['gpa'] - 2;
				}
	
				// remove optional subject
				unset($subjectList[$sub]);
			}
		}
	
		$pass = true;
		$sum = $optinal_mark;
		
		foreach ($subjectList as $sub => $value) {
			if ($value['gpa']) {
				$sum += $value['gpa'];
			} else {
				$pass = false;
			}
		}
	
		if ($pass === false) {
			$gpa = 0;
		} else {
			$gpa = $sum;
			if (count($subjectList) >= 0) {
				$gpa /= count($subjectList);
			}
	
			// adjust max value based on educare (settings) grading system
			$settings_rules = educare_check_status('grade_system');
			$current_rules = $settings_rules->current;
			$current_rules = $settings_rules->rules->$current_rules;
			$current_rules = json_decode(json_encode($current_rules), true);
			$max_rules = max($current_rules)[0];
			
			if ($gpa > $max_rules) {
				$gpa = $max_rules;
			}
		}
	
		return floor($gpa * 100) / 100;
	}

	/**
	 * Get the status of the student (Passed/Failed/GPA).
	 *
	 * @param string $showStatus The status to display (GPA by default).
	 * @param array|null $subjects Optional. An array of subjects and marks.
	 * @return mixed The student's status.
	 */
	public function getStatus($showStatus = 'GPA', $subjects = null) {
		if (!$subjects) {
			$subjects = $this->combineMarks('', true);
		}
		
		$gpa = $this->getGPA($subjects);
		$passed = "<div class='success results_passed'>Passed</div>";
		$failed = "<div class='failed results_failed'>Failed</div>";
		
		if ($gpa) {
			$status = $passed;
		} else {
			$status = $failed;
		}
		
		// Get auto results, if auto result is checked
		if (educare_check_status('auto_results') == 'checked') {
			if ($showStatus === 'GPA') {
				return esc_html($gpa);
			} else {
				return wp_kses_post($status);
			}
	
		} else {
			if (property_exists($this->data, $showStatus)) {
				return esc_html($this->data->$showStatus);
			} else {
				return false;
			}
		}
	}
}



/**
 * The `studentData` class encapsulates methods for retrieving student data based on given parameters.
 *
 * This class provides methods for retrieving student data based on the provided parameters. It can be used to
 * fetch information about students based on different criteria, such as class, exam, year, and other required fields.
 */
class studentData {
	private $data;

	/**
	 * Constructor for the `studentData` class.
	 *
	 * Initializes an instance of the class with the provided data.
	 *
	 * @param mixed $print The student data to work with.
	 */
	public function __construct($print) {
		$this->data = $print;
	}

	/**
	 * Get students based on the current student data.
	 *
	 * Retrieves student information based on the provided student data. The method allows fetching either all students
	 * data or data for current students (previous exam) depending on the `$all` parameter. It constructs a SQL query
	 * based on the required fields and values, fetches the data from the database, and returns the result.
	 *
	 * @param bool $all If `true`, retrieves data for all students. If `false`, retrieves data for current students.
	 * @return object|array|null Returns an object or array containing student data, or `null` if no data is found.
	 */
	public function getStudents($all = false) {
		global $wpdb;
		$requred_fields = array();

		if ($all) {
			// All Students data
			$requred_fields['Class'] = sanitize_text_field( $this->data->Class );
			$requred_fields['Year'] = sanitize_text_field( $this->data->Year );
		} else {
			// Current students (previous exam) data
			$requred = educare_check_status('display');
			$requred_fields = educare_requred_data($requred, true);
			$requred_fields = educare_get_requred_fields('students', $requred_fields);
			unset($requred_fields['Exam']);
		}

		$sql = array();
		foreach ($requred_fields as $key => $value) {
			if ($key == 'user_pin') {
				continue;
			}

			$sql[$key] = sanitize_text_field( $this->data->$key );
		}

		$sql = educare_get_sql_new('students', $sql);
		// for multiple students
		// $students = $wpdb->get_results($sql);
		// for a single student
		$students = $wpdb->get_row($sql);
		
		return $students;
	}
}


?>