<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// StudentResult class
require_once(EDUCARE_TEMP.'student-result-class.php');
// Educare all default search form template list
require_once(EDUCARE_TEMP.'educare-default-search-form.php');
// Educare all default profiles template list
require_once(EDUCARE_TEMP.'educare-default-profiles.php');
// Educare all default results card template list
require_once(EDUCARE_TEMP.'educare-default-results-card.php');
// Educare all default results card template list
require_once(EDUCARE_TEMP.'educare-default-certificate.php');
// Educare default edit profiles template
require_once(EDUCARE_TEMP.'educare-default-edit-profiles.php');

require_once(EDUCARE_ADMIN.'menu.php');
// Educare supported script for front-end 
require_once(EDUCARE_INC.'support/educare-themes.php');
// Educare roles and capabilities
require_once(EDUCARE_INC.'support/educare-roles.php');
// Educare grading systems fuctionality
require_once(EDUCARE_INC.'support/grading-systems.php');
// Educare results system (front view)
require_once(EDUCARE_INC.'support/results-systems.php');
// Educare profiles shortcode functionality
require_once(EDUCARE_INC.'support/shortcode/educare-profiles.php');
// Educare results and certificate shortcode functionality
require_once(EDUCARE_INC.'support/shortcode/educare-results.php');
// Educare Event Calender Class
require_once(EDUCARE_INC.'support/educare_event_calendar_class.php');
// Educare template list. Custom results card and this functinality
require_once(EDUCARE_TEMP.'template-functions.php');

// Begin educare template functionality


/**
 * Get template data for results card.
 * usage:
 * print_r(educare_get_template_data('fields'));	
 * 
 * @param string|null $key Optional. The specific key for template data retrieval.
 * @param bool $all Optional. Whether to retrieve all template data or only checked data.
 * @return mixed|array|null The retrieved template data based on the specified key and options.
 */
function educare_get_template_data($key = null, $all = true) {
	// Get the default template data
	$default_template = educare_default_results_card('', true);

	// Get the active/current template
	$active_template = educare_check_status('results_card_template');

	// Check if the active template function exists
	if ($active_template && function_exists($active_template)) {
		$template_data = $active_template('', true);

		$card_field = educare_check_status('results_card');
		$card_field = json_decode(json_encode($card_field), true);

		if (isset($card_field['template'][$active_template])) {
			$template_data['fields'] = $card_field['template'][$active_template];
		} else {
			$template_data = $active_template('', true);
		}
	} else {
		// If the active template function doesn't exist, use data from the default template
		$template_data = $default_template;
	}

	$new_template_data = array();

	if ($key) {
		// Retrieve specific key data/value
		if (key_exists($key, $template_data)) {
			$new_template_data = $template_data[$key];
	} else {
			// If the key doesn't exist in the active template, check the default template
			if (key_exists($key, $default_template)) {
				$new_template_data = $default_template[$key];
			}
		}
	} else {
		// Return all data
		$new_template_data = $template_data;
	}

	// Filter template data.
	$new_template_data = apply_filters( 'educare_filter_card_template', $new_template_data );

	if ($all) {
		return $new_template_data;
	} else {
		$checked_data = array();

		if ($new_template_data) {
			// Filter out data with status 'checked'
			foreach ($new_template_data as $key => $value) {
				if ($value['status'] == 'checked') {
					$checked_data[$key] = $value;
				}
			}
		}

		return $checked_data;
	}
}



/**
 * ### Get specific class or Group wise subject
 * Usage example: educare_get_options_for_subject('Class 6', 'Group');
 * Note: Return all data if $class_name is empty.
 * 
 * usage: 
 * print_r(educare_get_data('Year'));
 * 
 * @since 1.4.2
 * @last-update 1.4.2
 * @param string $class_name				For specific class or group subject
 * @param string $data_for					Select database (Class or Group);
 * 
 * @return array
 */
 function educare_get_data($data_for = 'Class', $class_name = false) {
	global $wpdb;
	$table = $wpdb->prefix.EDUCARE_PREFIX.'settings';
	
	$results = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM $table WHERE list = %s", $data_for)
	);
	
	if ($results) {
		foreach ( $results as $print ) {
			$data = $print->data;
			$data = json_decode($data, true);

			if ($class_name) {
				$subject_list = array();

				if (key_exists($class_name, $data)) {
					foreach ($data[$class_name] as $subject) {
						$subject_list[] = $subject;
					}

					return $subject_list;
				} else {
					return $subject_list;
				}

			} else {
				$all_calss = array();
				foreach ($data as $class => $sub) {
					if (is_array($sub)) {
						$all_calss[] = $class;
					} else {
						$all_calss[] = $sub;
					}
					
				}
				return $all_calss;
			}

		}
	}
}



/**
 * Output select options based on data retrieved from educare_get_data.
 *
 * usage: 
 * educare_get_option('Year')
 * 
 * @param string $data_for The data key for which options are to be retrieved.
 * @param bool $specific_key Optional. If true, retrieve options for a specific key within the data.
 * @return void Outputs HTML select options based on the retrieved data.
 */
function educare_get_option($data_for = 'Class', $specific_key = false) {
	$results = educare_get_data($data_for, $specific_key);
	if ($results) {
    if (isset($_POST['default'][$data_for])) {
      $current_value = sanitize_text_field( $_POST['default'][$data_for] );
    } else {
      $current_value = '';
    }

		foreach ($results as $value) {
			echo '<option value="'.esc_attr( $value ).'" '.selected($current_value, $value, false ).'>'.esc_html( $value ).'</option>';
		}
	} else {
		echo '<option value="">Not found</option>';
	}
}



/**
 * Retrieves data from a specified table in the WordPress database based on provided criteria.
 *
 * @param array $select An associative array containing query criteria:
 *                     - 'table': The table name to retrieve data from.
 *                     - 'row': Optional. The row/column name to filter by.
 *                     - 'value': Optional. The value to match in the specified row.
 *                     - 'data': Optional. The specific column/row to retrieve from each result.
 *                     - 'return_key': Optional. Return an array of keys only without data.
 *                     - 'target_key': Optional. Return a specific key's value from data.
 *                     - 'json_decode': Optional. If true, decode JSON-encoded data.
 *
 * @return mixed|array Returns the retrieved data as an array or a specific value, based on the criteria.
 *                     Returns false if no data is found.
 */
function educare_get_results($select) {
  /*
  // this is an sample table selection key and value
  $select = array (
		'table' => 'settings',     	// SELECT * FROM $select
		'row' => 'list',           	// WHERE $row (need to compare with value)
		'value' => 'Class',       	// WHERE $row equal to $value ($row='$value')
		'data' => 'data',          	// if found results print specific key/row (data) value
		'return_key' => false,			// Return key only without data
		'target_key' => 'Class 6',	// Return specific key value. ignore 'return_key' or set 'return_key' => false when target specific key for return data
	);
  */

  // table name is requred otherwise return
  if (!isset($select['table'])) return false;
  // All optional key and value
	isset($select['get_results']) ? : $select['get_results'] = 'get_results';
  // WHERE to select (WHERE $row='$value')
  isset($select['row']) ? : $select['row'] = '';
  // WHERE row equal to value (WHERE $row='$value')
  isset($select['value']) ? : $select['value'] = '';
  // if found results print specific key (data)
  isset($select['data']) ? : $select['data'] = '';
	// return key only
	isset($select['return_key']) ? : $select['return_key'] = '';
	// id data found target specific key for getting value
	isset($select['target_key']) ? : $select['target_key'] = '';
  isset($select['json_decode']) ? : $select['json_decode'] = false;
  
  global $wpdb;
  $table = $wpdb->prefix.EDUCARE_PREFIX.$select['table'];
  $row = $select['row'];
	$get_results = $select['get_results'];

  if ($row) {
    $val = $select['value'];
    if ($val) {
      $results = $wpdb->$get_results("SELECT * FROM $table WHERE $row='$val'");
    } else {
      $results = $wpdb->$get_results("SELECT $row FROM $table");
    }
    
  } else {
    $results = $wpdb->$get_results("SELECT * FROM $table");
  }

  if ($results) {
    $data = $select['data'];
    if ($data) {
      foreach ($results as $key => $value) {
        if (json_decode($value->$data)) {
					$data = json_decode($value->$data);

					if ($data) {
						$target = $select['target_key'];
						$return_key = $select['return_key'];

						if ($return_key) {
							$return_key_only = array();
							foreach ($data as $key => $value) {
								$return_key_only[] = $key;
							}

							return $return_key_only;
						}

						if ($target) {
							if (property_exists($data, $target)) {
								return $data->$target;
							} else {
								return false;
							}
						}
						return $data;
					} else {
						return false;
					}
        } else {
          return json_decode($value->$data);
        }
        
      }
    }

    if ($row and !$select['value']) {
      $new_data = array();
      
      foreach ($results as $key => $value) {
				if (property_exists($row,  $value)) {
					$new_data[] = $value->$row;
				}
      }

      return $new_data;
    }

    return $results;

  } else {
    return false;
  }
}



/**
 * Generates an HTML form for searching student or teacher records based on roles.
 *
 * This function generates a search form that allows users to search for student or teacher records
 * based on the specified roles. The form includes input fields and select dropdowns for entering
 * search criteria, such as Name, Roll Number, and Registration Number, and a Search button to submit
 * the search query. The form also includes a security nonce to prevent unauthorized access.
 *
 * @param string $roles The roles for which the search form is being generated. Default is 'results'.
 * @return void Outputs the HTML form for searching student or teacher records.
 */
function educare_get_search_form($roles = 'results') {
	if (isset($_POST['roles'])) {
		$roles = sanitize_text_field( $_POST['roles'] );
	}
	
  ?>
  <div id="educare-form">
		<form id="search-form" class="add_results" method="post" action="">
			<div class="content">
				<input type="hidden" name="roles" value="<?php echo esc_attr($roles) ;?>">

				<?php
				// Student details begin
				$requred = educare_check_status('display');
				$requred_title = educare_requred_data($requred, true);
				$requred_title = educare_roles_wise_filed(array('roles' => $roles, 'fields' => $requred_title));
				$requred_title['auto_fill'] = true;
				unset($requred_title['user_pin']);
				$i = 0;

				foreach ($requred_title as $key => $value) {
					if($key == 'Name' || $key == 'Roll_No' || $key == 'Regi_No') {
						$field_value = '';

						if ($roles != 'teachers' and $key == 'Name') continue;

						if (isset($_POST['default'])) {
							if (key_exists($key, $_POST['default'])) {
								$field_value  = sanitize_text_field( $_POST['default'][$key] );
							}
						}
						
						echo '<p>'.esc_html__($value, 'educare').':</p>';
						echo '<input type="text" name="default['.esc_attr($key).']" value="'.esc_attr($field_value).'" placeholder="'.sprintf(__('Enter %s', 'educare'), esc_attr__($value, 'educare')).'">';
					} else {
						$i++;
						if($i % 2 == 1) {
							echo '<div class="select">';
						}

						if ($key == 'auto_fill') {
							echo '<input type="submit" name="search" value="'.__('Search', 'educare').'" class="educare_button flex-end crud-forms"></input>';
						} else {
							echo '<div>';
							echo '<p>'.esc_html__($value, 'educare').':</p>';
							echo '<select id="'.esc_attr($key).'" name="default['.esc_attr($key).']">';
							educare_get_option($key);
							echo '</select>';
							echo '</div>';
						}

						if($i % 2 == 0) {
							echo '</div>';
						}
					}
				}

				if($i % 2 == 1) {
					echo '</div>';
				}
			echo '</div>';

			// Security nonce for this form.
			$nonce = wp_create_nonce( 'educare_crud_data' );
			echo '<input type="hidden" name="crud_data_nonce" value="'.esc_attr($nonce).'">';

    echo '</form>';
  echo '</div>';
}



/**
 * Adjusts and filters fields based on the specified roles.
 *
 * This function adjusts and filters the fields to be displayed in forms based on the specified roles.
 * It takes an array of data as input, which includes the roles, fields, additional fields, and an option
 * to ignore certain fields. It processes the data and returns an array of filtered and adjusted fields
 * based on the specified roles, ensuring that certain fields are ignored or added based on the role type.
 *
 * @param array $data An array containing roles, fields, additional fields, and ignore option.
 * @return array An array of adjusted and filtered fields based on the specified roles.
 */
function educare_roles_wise_filed($data) {
	isset($data['roles']) ? : $data['roles'] = null;
	isset($data['fields']) ? : $data['fields'] = array();
	isset($data['add_fields']) ? : $data['add_fields'] = array();
	isset($data['get_ignore']) ? : $data['get_ignore'] = false;
	// $roles, $fields, $add_fields = null

	if ($data['roles']) {
		// For add field
		$data['add_fields'] = array();

		// for ingnore fields
		if ($data['roles'] == 'students') {
			$ignore = array(
				'Exam',
				'Group',
				'Results',
				'GPA',
				'auto_fill',
				// 'user_pin'
			);
		} elseif ($data['roles'] == 'teachers') {
			$ignore = array(
				'Roll_No',
				'Regi_No',
				'Class',
				'Group',
				'Exam',
				'Year',
				'Results',
				'GPA',
				'auto_fill',
				'user_pin'
			);
		} elseif ($data['roles'] == 'marks') {
			$ignore = array(
				'Name',
				'Roll_No',
				'Regi_No',
				'Results',
				'GPA',
				'auto_fill',
				'user_pin'
			);
		} else {
			// if results. because our forms build for results system
			$ignore = array(
				'Group',
				// 'user_pin'
			);
		}

		if ($data['get_ignore']) {
			return $ignore;
		}
		
		if ($ignore) {
			foreach ($ignore as $value) {
				if (key_exists($value, $data['fields'])) {
					unset($data['fields'][$value]);
				}
			}
		}

		if ($data['add_fields']) {
			foreach ($data['add_fields'] as $key => $value) {
				$data['fields'][$key] = $value;
			}
		}

		if (!current_user_can( 'administrator' )) {
			$user_school = get_user_meta(get_current_user_id(), 'School', true);
			if ($user_school) {
				unset($data['fields']['School']);
			}
		}

		return $data['fields'];
	}
}



/**
 * Generates and displays extra fields based on settings data.
 *
 * usage: 
 * educare_get_extra_field();
 * 
 * This function retrieves and processes extra field details from the database settings and generates
 * corresponding input fields in the form. It first queries the database for extra field details,
 * such as the field type and display name. Then, it dynamically generates HTML input fields based on
 * the retrieved details, allowing users to input additional information.
 * 
 * @param str $roles check if data for teachers or students
 *
 * @return void
 */
function educare_get_extra_field($roles) {
	$select = array (
		'table' => 'settings',     // SELECT * FROM $select
		'row' => 'list',           // WHERE $row (need to compare with value)
		'value' => 'Extra_field',  // WHERE $row equal to $value ($row='$value')
		'data' => 'data',          // if found results print specific key/row (data) value
	);

	$results = educare_get_results($select);

	if ($results) {
		foreach ($results as $details) {
			// show specific data only based on roles
			$data_for = educare_sanitize_array( $details->for );

			if ($roles == 'results') {
				$roles = 'students';
			}

			if (in_array($roles, $data_for) || !$data_for) {
				$display = sanitize_text_field( $details->title );
				$name = str_replace(' ', '_', $display);
				$type = sanitize_text_field( $details->type );

				if (isset($_POST['Details'][$name])) {
					$value = sanitize_text_field( $_POST['Details'][$name] );
				} else {
					$value = '';
				}

				echo '
				<div class="wrap-input">
					<span class="input-for">'.esc_html__($display, 'educare').':</span>
					<label for="'.esc_attr($display).'" class="labels" id="'.esc_attr($display).'"></label>
					<input type="'.esc_attr($type).'" name="Details['.esc_attr($name).']" class="fields" value="'.esc_attr($value).'" placeholder="'.sprintf(__('Enter %s', 'educare'), esc_attr__($display, 'educare')).'">
					<span class="focus-input"></span>
				</div>';
			}
		}
	} else {
		// No extra fields found
	}
	
}



/**
 * Filters and returns checked data from an array of objects.
 *
 * This function takes an array of objects and filters out the objects based on their status property.
 * It returns a new stdClass object containing the filtered data, either as titles only or complete objects,
 * depending on the specified parameters. The status parameter allows selecting objects with a specific status,
 * such as 'checked'. If title_only is set to true, the function returns an object with only the titles of
 * the filtered objects; otherwise, it returns the complete filtered objects.
 *
 * @param array   $data        An array of objects to filter.
 * @param bool    $title_only  Whether to include only titles in the returned object.
 * @param string  $status      The status value to filter by (e.g., 'checked').
 *
 * @return stdClass A new stdClass object containing the filtered data.
 */
function educare_checked_data($data, $title_only = true, $status = 'checked') {
	$newObject = new stdClass();
	
	foreach ($data as $key => $object) {
    if ($title_only) {
      if ($status == 'all') {
        $newObject->$key =  $object->title;
      } else {
        if (property_exists($object, 'status')) {
          if ($object->status == $status) {
            $newObject->$key =  $object->title;
          }
        }
      }
    } else {
      if (property_exists($object, 'status')) {
        if ($object->status == $status) {
          $newObject->$key =  $object;
        }
      }
    }
	}

	return $newObject;
}



/**
 * Outputs table header cells for a grade sheet based on provided term data.
 *
 * This function generates and outputs table header cells for a grade sheet based on the given term data.
 * The term data should be provided as an associative array, where each key represents a term and its value
 * contains additional information about the term, such as title and subtitle. The front parameter controls
 * whether to display hidden terms when set to false (default), and it's particularly useful when generating
 * grade sheets for display purposes.
 *
 * @param array   $terms  An associative array of term data.
 * @param bool    $front  Whether to display hidden terms (default is false).
 *  @param bool 	$text_domain	for translation
 *
 * @return void Outputs the HTML table header cells for the grade sheet.
 */
function educare_grade_sheet_th($terms, $front = false, $text_domain = null) {
	if (!$text_domain) {
		$text_domain = 'educare';
	}

	foreach ($terms as $term => $termInfo) {
		if ($term == 'grade_sheet') continue;
		$title = $subtitle = '';
		
		if (key_exists('hide', $termInfo) && $front === true) {
			if ($termInfo['hide'] == 'on') {
				continue;
			}
		}

		if (key_exists('title', $termInfo)) {
			$title = $termInfo['title'];
		}
		
		if (key_exists('subtitle', $termInfo)) {
			$subtitle = $termInfo['subtitle'];
			if ($subtitle) {
				$subtitle = '<small>'.esc_html__($subtitle, $text_domain).'</small>';
			}
		}
		
		echo '<th>'. esc_html__($title, $text_domain) . wp_kses_post( $subtitle ).'</th>';
	}
}



/**
 * Outputs table cells for entering marks data based on provided term and subject information.
 *
 * This function generates and outputs table cells for entering marks data based on the given term and subject information.
 *
 * @param array   $terms      An associative array of term data.
 * @param string  $subArray   The subarray key for organizing the data structure.
 * @param string  $unique     Unique identifier for the data entry.
 * @param string  $subject    The subject for which marks are being entered.
 *
 * @return void Outputs the HTML table cells for entering marks data.
 */
function educare_get_marks_input($terms, $subArray, $unique, $subject) {

	foreach ($terms as $term => $termInfo) {
		// hide field based on fields settings
		if (key_exists('hide', $termInfo)) {
			if ($termInfo['hide'] == 'on') {
				continue;
			}
		}

		echo '<td>';

		if (isset($_POST[$subArray][$unique][$subject][$term])) {
			$value = sanitize_text_field( $_POST[$subArray][$unique][$subject][$term] );
		} else {
			$value = '';
		}
		
		// Show auto status
		if ($termInfo['auto'] == 'on') {
			$auto = 'Auto';

			if ($term == 'gpa' or $term == 'grade') {
				if ($termInfo['auto'] == 'on') {
					if (isset($_POST[$subArray][$unique][$subject]['marks']) && !empty($_POST[$subArray][$unique][$subject]['marks'])) {
						if ($term == 'gpa') {
							$auto = educare_letter_grade($_POST[$subArray][$unique][$subject]['marks'], true);
						} else {
							$auto = educare_letter_grade($_POST[$subArray][$unique][$subject]['marks']);
						}
					} else {
						$auto = $auto;
					}
				}
			}

			echo '<div class="auto-fields">'.wp_kses_post($auto).'</div>';
			echo '<input type="hidden" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][' . $term . ']" value="' . $value . '">';
		} else {
			// Select field
			if ($termInfo['type'] == 'select') {
				echo '<select name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][' . $term . ']">';
				foreach ($termInfo['value'] as $val) {
					echo '<option value="'.esc_attr($val).'" '.selected( $val, $value, false).'>' . esc_html($val) . '</option>';
				}
				echo '</select>';
			} else {
				// Input field
				echo '<input type="' . esc_attr($termInfo['type']) . '" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][' . $term . ']" value="' . $value . '">';
			}
		}

		echo '</td>';

	}

	if (isset($_POST[$subArray][$unique][$subject]['optional'])) {
		$value = sanitize_text_field( $_POST[$subArray][$unique][$subject]['optional'] );
	} else {
		$value = '';
	}

	echo '<td>
	<input type="hidden" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][optional]">
	<input type="checkbox" value="yes" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][optional]" '.checked($value, 'yes', false).'>
	</td>';
}



/**
 * Generates and outputs the HTML table for entering marks data based on provided roles and subjects.
 *
 * This function generates and outputs an HTML table for entering marks data based on the provided roles (e.g., 'results')
 * and subjects. It fetches information about the available terms and subjects, displays relevant table headers, and
 * provides input fields for entering marks. The function also handles the display of optional subject checkboxes.
 *
 * @param string  $roles      The role for which marks data is being entered (e.g., 'results').
 * @param array   $subjects   An array of subjects for which marks are being entered.
 * @param int     $unique     Optional. Unique identifier for the data entry.
 * @param string  $subArray   Optional. The subarray key for organizing the data structure.
 *
 * @return void Outputs the HTML table for entering marks data.
 */
function educare_get_marks_fields($roles, $subjects, $unique = 0, $subArray = 'Subject') {
	// Marks fields bassed on template
	if ($roles == 'results') {
		$terms = educare_get_template_data('fields', false);
	} else {
		$terms = array();
	}

	$results_card = educare_check_status('results_card');
	// getting grade_sheet checked data
	$default_terms = educare_checked_data($results_card->grade_sheet, false);
	$default_terms = json_decode(json_encode($default_terms), true);

	// Show Mark sheet fields
	?>
	<div class="table_container">
		<table class="grade_sheet list">
			<thead>
				<tr>
					<?php
					educare_grade_sheet_th($default_terms);
					educare_grade_sheet_th($terms, true);
					?>

					<th class="optional_sub">
						<div class="action_menu"><i class="dashicons action_button dashicons-info"></i> <menu class="action_link msg"><b>Optional Subject</b><hr>If this student or your results system has an optional subject, then mark this. otherwise ignore it.</menu></div>
					</th>
				</tr>
			</thead>
			
			<tbody>
				<?php 
				$serialNo = 1;
				if ($subjects) {
					foreach ($subjects as $subject) {
						echo '<tr>';

						foreach ($default_terms as $term => $termInfo) {
							if ($term == 'grade_sheet') continue;
							if ($term == 'no') echo '<td>' . esc_html($serialNo++) . '</td>';
							if ($term == 'subject') echo '<td>' . esc_html($subject) . '</td>';
	
							if (property_exists($subject, $term)) {
								echo '<td>' . esc_html($subject->$term) . '</td>';
							}
						}

						// echo '<td>' . esc_html($serialNo) . '</td>';
						// echo '<td>' . esc_html($subject) . '</td>';

						educare_get_marks_input($terms, $subArray, $unique, $subject);
						
						/*
						foreach ($terms as $term => $termInfo) { 
							echo '<td>';

							if (isset($_POST[$subArray][$unique][$subject][$term])) {
								$value = sanitize_text_field( $_POST[$subArray][$unique][$subject][$term] );
							} else {
								$value = '';
							}
							
							// Show auto status
							if ($termInfo['auto'] == 'on') {
								$auto = 'Auto';

								if ($term == 'gpa' or $term == 'grade') {
									if ($termInfo['auto'] == 'on') {
										if (isset($_POST[$subArray][$unique][$subject]['marks']) && !empty($_POST[$subArray][$unique][$subject]['marks'])) {
											if ($term == 'gpa') {
												$auto = educare_letter_grade($_POST[$subArray][$unique][$subject]['marks'], true);
											} else {
												$auto = educare_letter_grade($_POST[$subArray][$unique][$subject]['marks']);
											}
										} else {
											$auto = $auto;
										}
									}
								}

								echo '<div class="auto-fields">'.wp_kses_post($auto).'</div>';
								echo '<input type="hidden" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][' . $term . ']" value="' . $value . '">';
							} else {
								// Select field
								if ($termInfo['type'] == 'select') {
									echo '<select name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][' . $term . ']">';
									foreach ($termInfo['value'] as $val) {
										echo '<option value="'.esc_attr($val).'">' . esc_html($val) . '</option>';
									}
									echo '</select>';
								} else {
									// Input field
									echo '<input type="' . esc_attr($termInfo['type']) . '" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][' . $term . ']" value="' . $value . '">';
								}
							}

							echo '</td>';

						}

						if (isset($_POST[$subArray][$unique][$subject]['optional'])) {
							$value = sanitize_text_field( $_POST[$subArray][$unique][$subject]['optional'] );
						} else {
							$value = '';
						}

						echo '<td>
						<input type="hidden" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][optional]">
						<input type="checkbox" value="yes" name="' . esc_attr($subArray) . '[' . esc_attr($unique) . '][' . esc_attr($subject) . '][optional]" '.checked($value, 'yes', false).'>
						</td>';
						*/

						echo '</tr>';
					}
				} else {
					// Subject not found
					$url = admin_url().'/admin.php?page=educare-management&menu=Class';
					// wen don't need to additon optional field. beacuse $default_terms has 3 incliding grade_sheet!
					$span = count($default_terms) + count($terms);

					echo '<tr><td colspan="'.esc_attr( $span ).'">';
					echo educare_guide_for("Currently, you don't have added any subject in this class. Please add some subject by <a href='".esc_url( $url)."' target='_blank'>Click Here</a> or <a href='#Class'>Change Class</a>. Thanks", '', false);
					echo '</td></tr>';
				}

				// <!-- getting group wise subject list -->
				?>

				<tbody id="Group_list"></tbody>
			</tbody>
		</table>
	</div>
	<?php
}



/**
 * Retrieves and merges the details and grade sheet data of the results card.
 *
 * This function retrieves the details and grade sheet data of the results card from the database and
 * merges them into a single object. It fetches the results card information using the `educare_check_status`
 * function and combines the details and grade sheet data into a single object. The merged results card object
 * is returned for further processing.
 *
 * @return object|null The merged results card data as an object, or null if no data is found.
 */
function educare_get_card_data() {
	$results_card_all = educare_check_status('results_card');
	$results_card_details = $results_card_all->details;
	$results_card_grade_sheet = $results_card_all->grade_sheet;
	$results_card = (object) array_merge((array) $results_card_details, (array) $results_card_grade_sheet);
	return $results_card;
}



/**
 * Displays the subject fields and marks input fields based on the roles and class selection.
 *
 * This function is responsible for rendering the subject fields and marks input fields based on the provided roles
 * and class selection. It dynamically fetches and displays the subject options, marks input fields, and additional
 * controls based on the user's selections. The function also handles the display of error or success messages.
 *
 * @param string $roles The user's role (e.g., 'results', 'students', 'teachers').
 * @param string|null $class_name The selected class name. Defaults to null.
 * @return void
 */
function educare_get_subject_field($roles = 'results', $class_name = null) {
	// if ($print) {
	// 	if (key_exists('default', $print)) {
	// 		if (key_exists('Class', $print['default'])) {
	// 			$class_name = $print['default']['Class'];
	// 		}
	// 	}
	// } else {
	// 	$print = $_POST;
	// }

	// echo '<pre>';	
	// print_r($_POST);	
	// echo '</pre>';
	
	if (key_exists('default', $_POST)) {
		if (key_exists('Class', $_POST['default'])) {
			$class_name = $_POST['default']['Class'];
		}
	}

	if (isset($_POST['default']['id']) or isset($_POST['Subject'][0])) {
		// getting selected class subject
		$subjects = educare_check_settings('Class', $_POST['default']['Class']);

		// getting selected group subject
		if (isset($_POST['default']['Group']) && !empty($_POST['default']['Group'])) {
			// getting selected group subject
			$get_group_sub = educare_check_settings('Group', $_POST['default']['Group']);
			// check if subject exists or not in the subject list '$_POST['Subject'][0]'
			if ($get_group_sub) {
				foreach ($get_group_sub as $sub_name) {
					if (key_exists($sub_name, $_POST['Subject'][0])) {
						$subjects[] =  $sub_name;
					}
				}
			}
		}

		// foreach ($_POST['Subject'] as $key => $value) {
		// 	$subjects[] = $key;
		// }
	} else {
		if ($class_name) {
			// getting class wise subject for specific class name
			$subjects = educare_get_data('Class', $class_name);
		} else {
			// if empty $class_name, then get first class name
			$first_class = educare_get_data('Class');
			$class_name = reset($first_class);
			// Now getting subject baseed on first class
			$subjects = educare_get_data('Class', $class_name);
		}
	}

  // Marge group wise subject
  if (isset($_POST['select_subject'])) {
    $group_sub = $_POST['select_subject'];
    if ($group_sub) {
			$subjects = educare_get_data('Class', $class_name);
      $subjects = array_unique(array_merge($subjects, $group_sub));
    }
  }

	// Define/Get all terms/fields that supported current template
	if (isset($_POST['roles'])) {
		$roles = sanitize_text_field( $_POST['roles'] );
	}

	$failed = educare_check_status('failed');
	$passed = educare_check_status('passed');
	$auto_results = educare_check_status('auto_results');
	$results_card = educare_get_card_data();

	$results_card = educare_requred_data($results_card, true, true);
	$result = $gpa = $Status = '';

	if (isset($_POST['default']['Result'])) {
		$result = sanitize_text_field( $_POST['default']['Result'] );
	}
	if (isset($_POST['default']['GPA'])) {
		$gpa = sanitize_text_field( $_POST['default']['GPA'] );
	}
	if (isset($_POST['default']['Status'])) {
		$Status = sanitize_text_field( $_POST['default']['Status'] );
	}

	if ($roles == 'results') {
		// Auto results guidelines
		if ($auto_results == 'checked') {
			echo educare_guide_for(
				sprintf(
					__(
						"Please <a href='%s' target='_blank'>Disable Auto Results</a> system from Educare settings to manually set <b>%s</b> or <b>%s</b>.",
						'educare'
					),
					esc_url('/wp-admin/admin.php?page=educare-settings&menu=Results_System'),
					esc_html($results_card["result"]),
					esc_html($results_card["gpa"])
				)
			);
		}

		// Show results and GPA fields
		?>
		<div class="select">
			<div>
				<p><?php echo esc_html( $results_card['result'] ) ;?>:</p>
				<select name="default[Result]" class="form-control" <?php disabled( 'checked', $auto_results ) ;?>>
					<option value="Passed" <?php selected( 'Passed', esc_attr($result) ) ;?>><?php echo esc_html( $passed ) ;?></option>
					<option value="Failed" <?php selected( 'Failed', esc_attr($result) ) ;?>><?php echo esc_html( $failed ) ;?></option>
				</select>
			</div>
			
			<div>
				<p><?php echo esc_html( $results_card['gpa'] ) ;?>:</p>
				<input type="number" name="default[GPA]" class="fields" value="<?php echo esc_attr($gpa) ;?>" placeholder="0.00" step="any" <?php disabled( 'checked', $auto_results ) ;?>>
			</div>
		</div>
	<?php
	}
	
	// Show subject guide
	if ($roles == 'results') {
		echo educare_guide_for('add_subject');
	} else {
		echo educare_guide_for(
			sprintf(
				__(
					"Click here to <a href='%s' target='_blank'>Add More Subject</a>.",
					'educare'
				),
				esc_url('/wp-admin/admin.php?page=educare-management&Subject'),
				esc_url('/wp-admin/admin.php?page=educare-settings&menu=Card_Settings')
			)
		);
	}
	
	// Show Mark sheet fields
	educare_get_marks_fields($roles, $subjects);

	?>

	<!-- display error or success msg -->
	<div id="sub_msgs"></div>
	<!-- Show button for add or edit group wise subject -->
	<div id="add_to_button">
		<div id='edit_add_subject' class='educare_button'>
			<i class='dashicons dashicons-edit'></i>
		</div>
	</div>

	<br>
	<!-- Begin Group -->
	<?php echo educare_guide_for(sprintf(__('Click here to <a href="%s" target="_blank">Add More Group</a>.', 'educare'), '/wp-admin/admin.php?page=educare-management&Group'));?>

	<div class="select">
		<!-- Show All Group -->
		<div>
			<p><?php _e('Select Group', 'educare')?>:</p>
			<select id="Group" name="default[Group]">
				<option value=""><?php _e('None (Default)', 'educare')?></option>
				<?php educare_get_option("Group");?>
			</select>
		</div>

		<div>
			<p><?php _e('Visibility Status', 'educare')?>:</p>
			<select id="publish" name="default[Status]">
				<option value="publish" <?php selected( 'publish', esc_attr($Status) ) ;?>><?php _e('Publish', 'educare')?></option>
				<option value="scheduled" <?php selected( 'scheduled', esc_attr($Status) ) ;?>><?php _e('Scheduled', 'educare')?></option>
			</select>
		</div>
		
	</div>
	
	<?php
}



/**
 * Retrieves the required fields based on user roles and field data.
 *
 * Educare Queary Functions 1. Getting Requred Fields
 * This function retrieves the required fields for a given set of user roles and field data. It filters out any fields
 * marked as ignored for the specified roles and adds additional conditions, such as excluding the 'Name' field for
 * roles other than 'teachers'. The resulting list of required fields is then returned.
 *
 * @param string $roles The user's role (e.g., 'results', 'students', 'teachers').
 * @param array $fields_data An array containing field data for processing.
 * @return array An array containing the required fields based on the specified roles and field data.
 */
function educare_get_requred_fields($roles, $fields_data) {
	$ignore = educare_roles_wise_filed(array('roles' => $roles, 'get_ignore' => true));

	// Requred name field for teachers roles
	if ($roles != 'teachers') {
		array_push($ignore, 'Name');
	}
	
	$requred = educare_check_status('display');
	$requred_fields = educare_combine_fields($requred, $ignore, $fields_data);

	return $requred_fields;
}



/**
 * Get required fields based on roles and optionally return only the values.
 *
 * @param string|array $roles      The roles for which required fields are being fetched.
 * @param bool         $only_value Whether to return only the values or the full array.
 *
 * @return array|string Returns an array of required fields or sanitized values based on the conditions.
 */
function educare_required_fields($roles, $only_value = false) {
  // Get the display status
  $required = educare_check_status('display');

  // Get required data for display
  $required_title = educare_requred_data($required, true);

  // Filter required data based on roles
  $required_title = educare_roles_wise_filed(array('roles' => $roles, 'fields' => $required_title));

  // Get user profile status
  $user_profiles = educare_check_status('user_profiles');

  // If not 'results' role and user profiles are checked, add default user fields
  if ($roles != 'results' && $user_profiles == 'checked') {
    $required_title['user_login'] = 'User Login';
    $required_title['user_email'] = 'User Email';
    $required_title['user_pass'] = 'User Pass';
  }

  // If only returning values, sanitize and return
  if ($only_value) {
    $required_value = array();

    if ($required_title) {
      foreach ($required_title as $value) {
        $required_value[] = sanitize_text_field($value);
      }
    }

    return $required_value;
  }

  // Return the full array of required fields
  return $required_title;
}



/**
 * Generates dynamic SQL query based on provided filters and options.
 *
 * This function generates a dynamic SQL query based on the provided filters and options, such as required fields,
 * sorting criteria, and pagination. It constructs the SQL query using the WordPress $wpdb global object and prepares
 * the query with sanitized values. The resulting SQL query string is returned.
 *
 * @param array $requred_fields An array of required fields and filtering options.
 * @param string $roles The user's role (e.g., 'results', 'students', 'teachers').
 * @return string The dynamically generated SQL query string.
 */
function educare_dynamic_sql($requred_fields, $roles = 'results') {
	global $wpdb;
	// Define table name to access data
	$table = $wpdb->prefix.EDUCARE_PREFIX.$roles;
	$table_structure = $wpdb->get_results("DESCRIBE $table");
	$table_field = array();
	unset($requred_fields['dashboard'], $requred_fields['profiles_id'], $requred_fields['profiles_for']);

	// Getting table field for dynamically search
	if (!empty($table_structure)) {
		foreach ($table_structure as $column) {
			$table_field[] = $column->Field;
		}
	}

	// echo '<pre>';	
	// print_r($table_field);	
	// echo '</pre>';

	$prepared_values = array();
	$orderby = $limit = '';
	$sql = "WHERE 1=1 ";

	foreach ($requred_fields as $key => $value) {
		if ($key == 'search') {
			$sql .= "AND `id` LIKE '%" . esc_sql( $value ) . "%'";
			// Search form table
			if ($table_field) {
				foreach ($table_field as $field) {
					// Ignnore id. because previously we con it with $sql. Also, we need to ignore $requred_fields key for unique search
					if ($field == 'id' or key_exists($field, $requred_fields)) continue;
					$sql .= "OR `".esc_sql( $field )."` LIKE '%" . esc_sql( $value ) . "%' "; 
				}
			}
			// $sql .= "AND `Name` LIKE '%" . esc_sql( $value ) . "%' ";
			// $sql .= "AND `Name` LIKE '%" . esc_sql( $value ) . "%' OR `Roll_No` LIKE '%" . esc_sql( $value ) . "%' OR `Regi_No` LIKE '%" . esc_sql( $value ) . "%' ";
			// $sql .= "AND `Name` LIKE '%{$value}%' OR `Roll_No` LIKE '%{$value}%' ";
			continue;
		}

		if ($key == 'order_by' or $key == 'order') {
			$orderby = sanitize_text_field($requred_fields['order_by']);
			$order = sanitize_text_field($requred_fields['order']);
			$orderby = "ORDER BY $orderby $order";
			continue;
		}

		// if ($key == 'order') {
		// 		$order = sanitize_text_field($value);
		// 		continue;
		// }

		if ($key == 'per-page' or $key == 'offset') {
			$limit = (int) sanitize_text_field($requred_fields['per-page']);
			$offset = (int) sanitize_text_field($requred_fields['offset']);
			$limit = "LIMIT $offset, $limit";
			continue;
		}

		// if ($key == 'offset') {
		// 	$offset = (int) $value;
		// 	continue;
		// }

		$sql .= "AND `$key`=%s ";
		$prepared_values[] = sanitize_text_field($value);
	}

	$sql .= "$orderby $limit";
	if ($prepared_values) {
		$sql = $wpdb->prepare($sql, $prepared_values);
	}
	
	return $sql;
}



/**
 * Generates SQL query for fetching data from a specific table based on provided filters.
 *
 * This function generates an SQL query for fetching data from a specific table based on provided filters and options.
 * It constructs the SQL query using the WordPress $wpdb global object and prepares the query with sanitized values.
 * The resulting SQL query string is returned.
 *
 * @param string $roles The user's role (e.g., 'results', 'students', 'teachers').
 * @param array $requred_fields An array of required fields and filtering options.
 * @param bool $crud Whether the query is for CRUD (Create, Read, Update, Delete) operations. Default is false.
 * @return string The dynamically generated SQL query string.
 */
function educare_get_sql_new($roles, $requred_fields, $crud = false) {
	global $wpdb;
	// Define table name
	$table = $roles;
	$table_name = $wpdb->prefix.EDUCARE_PREFIX.$table;

	// Build the SELECT query
	if ($requred_fields) {
		$sql = "SELECT * FROM $table_name WHERE ";
	} else {
		$sql = "SELECT * FROM $table_name ";
	}
	
	$prepared_values = array();

	foreach ($requred_fields as $key => $value) {
		// We need to encrypt the plain text password using wp_hash_password() to match the stored encrypted password in the user_pin field. But  wp_hash_password() generates a different hash each time it's called, even for the same password. In that case, you won't be able to directly compare the encrypted password stored in the database with the hashed password generated by wp_hash_password(). To verify the password, you can use the wp_check_password() function instead.
		if ($key == 'user_pin') {
			continue;
			// $value = wp_check_password($value);
		}

		$sql .= "`$key`=%s AND ";

		$prepared_values[] = $value;
	}

	// Check to ignore specific ID
	if ($crud) {
		if (isset($_POST['id']) && !empty($_POST['id'])) {
			$id = sanitize_text_field( $_POST['id'] );
			$sql .= $wpdb->prepare('id <> %d AND ', $id);
		}
	}
	
	// Remove the last 'AND'
	$sql = rtrim($sql, 'AND ');
	$sql = $wpdb->prepare($sql, $prepared_values);
	return $sql;
}



/**
 * Handles CRUD (Create, Read, Update, Delete) operations for educare data.
 *
 * This function processes the CRUD operations for educare data based on user input from POST requests.
 * It performs various checks, validations, and database operations to insert, update, or delete data records.
 *
 * @param bool $import Whether the function is used for data import. Default is false.
 * @return string|null A status message indicating the result of the CRUD operation or null.
 */
function educare_crud_data_new($import = false) {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$user_school = '';

		if (current_user_can( 'administrator' )) {
			if (isset($_POST['default']['School'])) {
				$user_school = sanitize_text_field($_POST['default']['School']);
			}
		} else {
			$user_school = get_user_meta(get_current_user_id(), 'School', true);
			if ($user_school) {
				$_POST['default']['School'] = sanitize_text_field( $user_school );
			}
		}

		if (isset($_POST['roles'])) {
			$roles = sanitize_text_field( $_POST['roles'] );
		} else {
			// return 'Roles Must be requred';
			$roles = 'results';
		}

		// Define sample data to be inserted
		// $data = array();
		// Define the ID to ignore
		$requred_fields = array();
		$id = $sql = '';
		
		if (isset($_POST['id']) && !empty($_POST['id'])) {
			global $wpdb;
			$id = sanitize_text_field( $_POST['id'] );

			$requred_fields = array(
				'id' => $id
			);
			// Get data only for specific ID
			// SELECT * FROM wp_educare_students WHERE `id`='10'
			$sql = educare_get_sql_new($roles, $requred_fields);
			$results = $wpdb->get_row($sql);

			if ($results) {
				$data = json_decode(json_encode($results), true);
				unset($data['id']);
			}
		}
		
		// Process edit action otherwise CRUD
		if (!isset($_POST['edit'])) {
			// Process CRUD actions
			$requred_fields = educare_get_requred_fields($roles, $_POST['default']);
			$user_profiles = '';

			if ($roles != 'results' && !isset($_POST['auto_fill']) && !isset($_POST['search'])) {
				$user_profiles = educare_check_status('user_profiles');

				if ($user_profiles == 'checked') {
					$user_data = array (
						'user_login',
						'user_email',
						'user_pass'
					);
					
					$user_data = educare_combine_fields($user_data, '', $_POST['default'], true);
					$requred_fields = array_merge($requred_fields, $user_data);
				}
			}

			if (isset($_POST['id']) or isset($_POST['search'])) {
				unset($requred_fields['user_pass'], $requred_fields['user_pin']);
			}

			if (isset($_POST['auto_fill'])) {
				if (key_exists('user_pin', $requred_fields)) {
					unset($requred_fields['user_pin']);
				}
			}

			// remove user_pin if is results
			if ($roles == 'results') {
				unset($requred_fields['user_pin']);
			}
	
			// Notify when requred data is empty
			if (educare_is_empty($requred_fields)) {
				if ($import) {
					return 'empty';
				} else {
					// Notify Empty requred fields
					echo educare_is_empty($requred_fields);

					if(isset($_POST['search'])) {
						educare_get_search_form();
					}
				}
				
			} else {
				// Requred data is filled or not empty
				// SELECT * FROM wp_educare_students WHERE `Roll_No`='45' AND `Regi_No`='45' AND `Class`='JSS 1' AND `Year`='2022'
				global $wpdb;
				$requred_fields['School'] = $user_school;
				$sql = educare_get_sql_new($roles, $requred_fields, true);
				$results = $wpdb->get_row($sql);
				// get back to real
				unset($requred_fields['School']);
				
				if ($results) {
					$data = json_decode(json_encode($results), true);
					unset($data['id']);
				}
			
				// Procces data
				if (isset($_POST['default']) && !empty($_POST['default'])) {
					if ($roles != 'results') {
						$old_username = $user_id = '';
						
						if (isset($data)) {
							$old_username = sanitize_text_field($data['user_login']);
							$old_email = sanitize_text_field($data['user_email']);
							$user_id = sanitize_text_field($data['user_id']);	
						}
					}
					
					// The 'details' POST variable exists and is not empty
					foreach ($_POST['default'] as $key => $value) {
						// do something with details
						$key = sanitize_text_field( $key );
						// The wp_hash_password() function takes the plain-text password as input and returns the hashed password. 
						if ($key == 'user_pass') {
							if (!$value) {
								continue;
							}
							$value = wp_hash_password($value);

						} elseif ($key == 'user_pin') {
							if (!$value) {
								continue;
							}
							$value = educare_encrypt_data($value);
						} else {
							$value = sanitize_text_field( $value );
						}
						
						if ($value) {
							$data[$key] = $value;
						}

					}
					
					if (isset($_POST['crud']) && $user_profiles == 'checked' && $roles != 'results') {
						$new_username = sanitize_text_field($data['user_login']);
						$new_email = sanitize_text_field($data['user_email']);

						$username_exists = username_exists($new_username);
						$email_exists = email_exists($new_email);

						if ($username_exists && ($username_exists != username_exists($old_username)) && $email_exists && ($email_exists != email_exists($old_email))) {
							// Both username and email exist
							return educare_show_msg('<b>Username</b> and <b>Email</b> already exist.', false);
						} elseif ($username_exists && ($username_exists != username_exists($old_username))) {
							// Username exists
							return educare_show_msg('<b>Username</b> already exists.', false);
						} elseif ($email_exists && ($email_exists != email_exists($old_email))) {
							// Email exists
							return educare_show_msg('<b>Email</b> already exists.', false);
						} elseif (!is_email($new_email)) {
							// The email is not valid.
							return educare_show_msg('<b>Invalid Email.</b> Pleace enter a valid email address.', false);
						} else {
							$roles = sanitize_text_field($roles);
							$user_data = array(
								'ID' => $user_id,
								'user_login' => $new_username,
								'user_email' => $new_email,
								'user_nicename' => $new_username,
								'display_name' => sanitize_text_field($data['Name']),
								'role' => 'educare_'.$roles,
								'meta_input' => array (
									'roles' => $roles
								)
							);

							if (isset($_POST['default']['user_pass']) && !empty($_POST['default']['user_pass'])) {
								$user_data['user_pass'] = $_POST['default']['user_pass'];
							} else {
								if (!$data['user_pass']) {
									// Empty password
									return educare_show_msg('<b>Password should not be empty!</b> pleace enter password.', false);
								}
							}
							
							if ($user_id) {
								// Update the user
								$crud_user = wp_update_user($user_data);
								// Update user login
								$wpdb->update(
									$wpdb->users,
									['user_login' => $new_username],
									['ID' => $user_id]
								);
							} else {
								// Insert the user
								unset($user_data['ID']);
								$crud_user = wp_insert_user($user_data);
							}

							if (!is_wp_error($crud_user)) {
								// echo 'User created successfully';
								$data['user_id'] = $crud_user;
							} else {
								if ($import) {
									return 'error_creating_user';
								} else {
									return educare_show_msg('Error creating user: ' . $crud_user->get_error_message(), false);
								}
								
							}
						}
					}
				} else {
					// The 'details' POST variable does not exist or is empty
					// Handle the error or set a default value
					if ($import) {
						return 'empty';
					} else {
						return educare_show_msg('Requred data is missing!');
					}
				}

				if (isset($_POST['Details']) && !empty($_POST['Details'])) {
					// The 'details' POST variable exists and is not empty
					$data['Details'] = json_encode($_POST['Details']);
				} else {
					// The 'details' POST variable does not exist or is empty
					// Handle the error or set a default value
					// $data['Details'] = '';
					$data['Details'] = array();
				}

				if (isset($_POST['Others']) && !empty($_POST['Others'])) {
					// The 'details' POST variable exists and is not empty
					$data['Others'] = json_encode($_POST['Others']);
				} else {
					// The 'details' POST variable does not exist or is empty
					// Handle the error or set a default value
					// $data['Others'] = '';
					$data['Others'] = array();
				}

				if (isset($_POST['Subject'][0]) && !empty($_POST['Subject'][0])) {
					$data['Subject'] = json_encode($_POST['Subject'][0]);
				} else {
					// Handle the error or set a default value
					// $data['Subject'] = '';
					$data['Subject'] = array();
				}
			}

		}

		// Check is requred data missing or not
		if (educare_is_empty($requred_fields)) {
			// 'No data found for insert'
			// return educare_show_msg('Requred data is missing!', false);
			return;
		}

		global $wpdb;
		// Define table name
		$table = $roles;
		$table_name = $wpdb->prefix.EDUCARE_PREFIX.$table;
		
		// execute the query and get the first row
		if ($sql) {
			$row = $wpdb->get_row($sql);
		} else {
			$row = null;
		}
		
		// if data not found
		if ($row !== null) {
			// data found or Data already exists
			if(isset($_POST['search']) or isset($_POST['edit']) or isset($_POST['auto_fill'])) {
				// Store all data in to post. 
				foreach ($row as $key => $value) {
					if ($key == 'Details' or $key == 'Others' or $key == 'Subject') {
						if ($key == 'Subject') {
							$_POST[$key][0] = json_decode($value, true);
						} else {
							$_POST[$key] = json_decode($value, true);
						}
						
					} else {
						if ($key == 'user_pin') {
							$_POST['default'][$key] = educare_decrypt_data( $value );
						} else {
							$_POST['default'][$key] = sanitize_text_field( $value );
						}
						
					}
				}
				
				// Show forms
				if (!isset($_POST['auto_fill'])) {
					educare_get_forms();
				}
				
				return;
			} else {
				// Data already exists, return error message or do something else
				if ($import) {
					return 'exist';
				} else {
					return educare_show_msg(esc_html( ucfirst($roles) ) . ' already exists', false);
				}
			}

		} else {
			// unique data or not found
			// if request for find results
			if (isset($_POST['search']) or isset($_POST['edit']) or isset($_POST['auto_fill'])) {
				echo educare_show_msg('Data not found', false);

				if (isset($_POST['search'])) {
					educare_get_search_form();
				}
				
				return;
			} else {
				// if request for update or edit
				if ($id) {
					// Define the where condition
					$where = array(
						// replace with the ID of the row to update
						'id' => $id,
					);

					// Perform the update
					if (isset($_POST['delete'])) {
						// Execute the DELETE query
						$process = $wpdb->delete( $table_name, $where );
						
						if (get_userdata($user_id) !== false) {
							// User exists, force to delete user
							if (!wp_delete_user($user_id)) {
								// Failed to delete user.
								echo educare_show_msg('Failed to delete user.', false);
							}
						} else {
							// User does not exist
							echo educare_show_msg('User does not exist! in WP users list!', false);
						}

					} else {
						$process = $wpdb->update( $table_name, $data, $where );
						update_user_meta($user_id, 'user_id', $id);
					}

					// Check if the update was successful
					if ( $process === false ) {
						// handle error
						// Error to porocess request
						return educare_show_msg('There was an error processing your request');
					} elseif ( $process == 0 ) {
						// no rows were updated, handle accordingly
						return educare_show_msg('No changes were found in this request');
					} else {
						// update was successful, handle accordingly
						// Perform the update
						if (isset($_POST['delete'])) {
							// Execute the DELETE query
							$_POST = array();
							return educare_show_msg('Successfully deleted ' . esc_html( $roles ));
						} else {
							return educare_show_msg(esc_html( ucfirst($roles) ) . ' update was successful');
						}
					}

				} else {
					// if request for insert
					// Data is unique, insert into table
					$insert = $wpdb->insert($table_name, $data);
					
					if ($insert === false) {
						// Error occurred, handle it
						// echo '<pre>';	
						// print_r($wpdb->last_error);	
						// echo '</pre>';
						
						if ($import) {
							return 'error';
						} else {
							return educare_show_msg('Error occurred to handle it', false);
						}

					} else {
						// Data inserted successfully
						$data_id = $wpdb->insert_id;
						$_POST['default']['id'] = $data_id;

						// Set educare user id in to wp user
						if ($roles != 'results') {
							if (isset($crud_user)) {
								update_user_meta($crud_user, 'user_id', $data_id);
							}
						}

						// Show success msgs
						if ($import) {
							return 'success';
						} else {
							return educare_show_msg(esc_html( ucfirst($roles) ) . ' inserted successfully');
						}
						
					}
				}
			}
		}
  }
}



/**
 * Generates and displays an HTML form for adding, editing, or viewing data based on user roles.
 *
 * This function generates and displays an HTML form for adding, editing, or viewing data based on user roles.
 * It takes an optional parameter `$roles` to specify the user's role (e.g., 'results', 'students', 'teachers').
 * If not provided, the default role is 'results'. The function constructs and outputs the HTML form elements
 * including input fields, dropdowns, buttons, and other necessary components for the form.
 *
 * @param string $roles The user's role (e.g., 'results', 'students', 'teachers').
 * @return void Outputs the HTML form elements for adding, editing, or viewing data.
 */
function educare_get_forms($roles = 'results') {
	if (isset($_POST['roles'])) {
		$roles = sanitize_text_field( $_POST['roles'] );
	}
	
	?>
	<!-- <h1>Subject Form</h1> -->
	<div id="educare-form">
		<form id="crud-forms" class="add_results" method="post" action=""><!-- Replace "submit.php" with the filename of your PHP script -->
			<div class="content">
				<input type="hidden" name="roles" value="<?php echo esc_attr($roles) ;?>">

				<?php
				$id = '';
				if (isset($_POST['default'])) {
					if (key_exists('id', $_POST['default'])) {
						$id = sanitize_text_field( $_POST['default']['id'] );
						echo '<input type="hidden" name="default[id]" value="'.esc_attr( $id ).'">';
					}
				}

				if (isset($_POST['Others']['Photos'])) {
					$photos = $_POST['Others']['Photos'];
				} else {
					$photos = '';
				}

				educare_files_selector($photos);

				$show_roles = $roles;

				if ($roles == 'results') {
					$show_roles = 'students';
				}

				// Student details begin
				echo '<h2>'.sprintf(__('%s Details', 'educare'), esc_html__(ucfirst($show_roles), 'educare')).'</h2>';
				
				$requred = educare_check_status('display');
				$requred_title = educare_requred_data($requred, true);
				// $requred_fields = educare_combine_fields($requred, $ignore);
				// $requred_data = educare_combine_fields($requred);
				$requred_title['auto_fill'] = true;
				$requred_title = educare_roles_wise_filed(array('roles' => $roles, 'fields' => $requred_title));
				

				// atik
				// if (!current_user_can( 'administrator' )) {
				// 	$user_school = get_user_meta(get_current_user_id(), 'School', true);
				// 	if ($user_school) {
				// 		unset($requred_title['School']);
				// 	}
				// }

				if ($roles == 'results') {
					unset($requred_title['user_pin']);
				}
				
				$i = 0;

				foreach ($requred_title as $key => $value) {
					if($key == 'user_pass' or $key == 'user_pin') {
						continue;
					}

					if($key == 'Name' || $key == 'Roll_No' || $key == 'Regi_No') {
						$field_value = '';

						if (isset($_POST['default'])) {
							if (key_exists($key, $_POST['default'])) {
								$field_value  = sanitize_text_field( $_POST['default'][$key] );
							}
						}
						
						echo '<p>'.esc_html__($value, 'educare').':</p>';
						echo '<input type="text" name="default['.esc_attr($key).']" value="'.esc_attr($field_value).'" placeholder="'.sprintf(__('Enter %s', 'educare'), esc_attr__($value, 'educare')).'">';
					} else {
						$i++;
						if($i % 2 == 1) {
							echo '<div class="select">';
						}

						if ($key == 'auto_fill') {
							// you need to remove name="auto_fill" when direcly request. otherwise it's auto fill bassed on old results. that massed query! (this is for AJAX only)
							echo '<div class="flex-end"><div name="auto_fill" class="educare_button crud-forms" style="margin: 0px;" title="'.__('Get data from specific student by roll no, regi no, class and year.', 'educare').'">'.__('Auto Fill', 'educare').'</div></div>';
						} else {
							echo '<div>';
							echo '<p>'.esc_html__($value, 'educare').':</p>';
							echo '<select id="'.esc_attr($key).'" name="default['.esc_attr($key).']">';
							educare_get_option($key);
							echo '</select>';
							echo '</div>';
						}

						if($i % 2 == 0) {
							echo '</div>';
						}
					}
				}

				if($i % 2 == 1) {
					echo '</div>';
				}

				// Login Form
				if ($roles != 'results') {
					$user_profiles = educare_check_status('user_profiles');

					if ($user_profiles == 'checked') {
						echo '<div class="pass-control">';
						$user_login = $user_email = $user_pass = '';
						
						if (isset($_POST['default'])) {
							$user_login = sanitize_text_field( $_POST['default']['user_login'] );
							$user_email = sanitize_text_field( $_POST['default']['user_email'] );
							$user_pass = sanitize_text_field( $_POST['default']['user_pass'] );
						}

						$user_pass = $disabled = '';
						$placegolder = 'Enter Password';

						if (isset($_POST['default'])) {
							$user_pass = sanitize_text_field( $_POST['default']['user_pass'] );
						}

						if (isset($_POST['id']) or isset($_POST['search'])) {
							$user_pass = '';
							$disabled = 'disabled';
							$placegolder = 'Click here for update';
						}

						echo '<h2>'.__('Login Details', 'educare').'</h2>';
						echo educare_guide_for(__('Please carefully fill login information. If you want to disable login system, just <a href="/wp-admin/admin.php?page=educare-settings&menu=Security" target="_blank">Click here</a>, Then uncheck <b>User Profiles</b> options.', 'educare'));
						// Username
						echo '<p>'.__('Username', 'educare').'</p>';
						echo '<input type="text" name="default[user_login]" value="'.esc_attr( $user_login ).'" placeholder="Enter Username">';
						// Emanil
						echo '<p>'.__('Email', 'educare').'</p>';
						echo '<input type="email" name="default[user_email]" value="'.esc_attr( $user_email ).'" placeholder="Enter Email">';
						// Password
						echo '<p>'.__('Password', 'educare').'</p>';
						echo '<input class="user_pass" type="password" name="default[user_pass]" value="'.esc_attr( $user_pass ).'" placeholder="'.esc_attr( $placegolder ).'" '.esc_attr( $disabled ).'>';


						echo '
						<span class="button updatePasswordBtn" style="display: none;">'.__('Update', 'educare').'</span>
						<span class="button showHideBtn">'.__('Show', 'educare').'</span>
						<span class="button cancelBtn" style="display: none;">'.__('Cancel', 'educare').'</span>
						';

						echo '</div>';
					}
				}

				// Protected Results
				if ($roles == 'students') {
					if (key_exists('user_pin', $requred_title)) {
						echo '<div class="pass-control">';
						echo '<h2>'.__('Protected Results', 'educare').'</h2>';
						echo educare_guide_for(__('Secure results with password or pin code', 'educare'));

						if (isset($_POST['default'])) {
							$user_pin = sanitize_text_field( $_POST['default']['user_pin'] );
							// $user_pin = educare_decrypt_data($user_pin);
						} else {
							$user_pin = '';
						}

						if (!$user_pin) {
							// Generate a random number with 9 digits
							$user_pin = rand(100000000, 999999999);
						}

						$placegolder = __('Enter Password', 'educare');
						$disabled = '';
						// if (isset($_POST['id']) or isset($_POST['search'])) {
						// 	$user_pin = '';
						// 	$disabled = 'disabled';
						// 	$placegolder = 'Click here for update';
						// }

						// user_pin
						echo '<p>'.esc_html__( $requred_title['user_pin'], 'educare' ).':</p>';
						echo '<input class="user_pass user_pin" type="password" name="default[user_pin]" value="'.esc_attr( $user_pin ).'" placeholder="'.esc_attr( $placegolder ).'" '.esc_attr( $disabled ).'>';

						echo '
						<span class="button updatePasswordBtn">'.__('Update', 'educare').'</span>
						<span class="button showHideBtn" style="display: inline-block;">'.__('Show', 'educare').'</span>
						<span class="button cancelBtn" style="display: none;">'.__('Cancel', 'educare').'</span>
						';
						echo '</div>';

						echo '<p>'.sprintf(__( '%s Status', 'educare' ), esc_html__($requred_title['user_pin'], 'educare')).':</p>';
						if (isset($_POST['default']['pin_status'])) {
							$expire = sanitize_text_field($_POST['default']['pin_status'] );
						} else {
							$expire = '';
						}

						echo '<select name="default[pin_status]">
							<option value="valid" '.esc_attr(selected( $expire, 'valid', false )).'>'.__('Valid', 'educare').'</option>
							<option value="expire" '.esc_attr(selected( $expire, 'expire', false )).'>'.__('Expire', 'educare').'</option>
						</select>';
					}

				}

				?>
				<!-- Default value -->
				<div class="educare_data_field">
					<div class="educareTemplateForm_id" data-value="<?php echo esc_attr($id);?>"></div>
				</div>
				<?php

				// Extra fields
				echo '<h2>'.__('Others Info', 'educare').'</h2>';
				echo educare_guide_for('add_extra_field');
				educare_get_extra_field($roles);

				// Rattings sections begins
				if ($roles == 'results' && educare_check_status('rattings_system') == 'checked') {
					echo '<h3 class="center">'.__('Rattings', 'educare').'</h3>';

					echo educare_guide_for(
						sprintf(
							__(
								"You can <a href='%s' target='_blank'>Add More Rating Fields</a> from Educare management menu. Also, you can <a href='%s' target='_blank'>Disable Ratings System</a> from Educare settings.",
								'educare'
							),
							esc_url('/wp-admin/admin.php?page=educare-management&Rattings'),
							esc_url('/wp-admin/admin.php?page=educare-settings&menu=Results_System')
						)
					);

					$Rattings = educare_check_settings('Rattings');

					if ($Rattings) {
						foreach ($Rattings as $term => $remarks) {
							if ($remarks) {
								echo '<div claa="table_container">
								<table class="grade_sheet list"> 
								<thead>
									<tr>
										<th>'.__('No.', 'educare').'</th>
										<th>'.esc_html__($term, 'educare').'</th>
										<th class="rattings-status">'.__('Rattings', 'educare').'</th>
									</tr>
								</thead>
								<tbody>
								';
								$no = 1;
								foreach ($remarks as $remarks_field) {
									if (isset($_POST['Others']['Rattings'][$term][$remarks_field])) {
										$value = $_POST['Others']['Rattings'][$term][$remarks_field];
									} else {
										$value = '';
									}

									echo '<tr>
										<td>'.esc_html($no++).'</td>
										<td>'.esc_html__($remarks_field, 'educare').'</td>
										<td><input type="text" value="'.esc_attr($value).'" name="Others[Rattings]['.esc_attr($term).']['.esc_attr($remarks_field).']" step="any"></td>
									</tr>';
								}

								echo '</tbody></table></div><br>';
								
							}
						}
					}
				}

				// Remarks sections begins
				if ($roles == 'results' && educare_check_status('remarks') == 'checked') {
					$remarks = array (
						'teacher' => [
							'comments' => '',
							'name' => '',
							'date' => '',
							'signature' => '',
						],
						'principal' => [
							'comments' => '',
							'name' => '',
							'date' => '',
							'signature' => '',
						]
					);

					if (isset($_POST['Others']['remarks'])) {
						$remarks = $_POST['Others']['remarks'];

					}

					// Remarks sections
					echo '<h2>'.__('Remarks', 'educare').'</h2>';

					echo educare_guide_for(
						sprintf(
							__(
								"Click here to <a href='%s' target='_blank'>Disable Remarks System</a>.",
								'educare'
							),
							esc_url('/wp-admin/admin.php?page=educare-settings&menu=Results_System')
						)
					);

					// Master/Mistress Remarks
					echo '<p>'.__('Master/Mistress Remarks', 'educare').':</p>';
					echo '<input type="text" name="Others[remarks][teacher][comments]" value="'.esc_attr($remarks['teacher']['comments']).'" placeholder="'.__('Comments', 'educare').'">';
					// Master/Mistress name
					echo '<input type="text" name="Others[remarks][teacher][name]" value="'.esc_attr($remarks['teacher']['name']).'" placeholder="'.__('Master/Mistress Name', 'educare').'">';
					// remarks date
					echo '<input type="date" name="Others[remarks][teacher][date]" value="'.esc_attr($remarks['teacher']['date']).'" placeholder="'.__('Date', 'educare').'">';
					// signature
					echo '<div class="getAttachment">
					<p>'.__('Signature', 'educare').':';
					echo '<div class="attachmentPreview"><div class="attachmentImg">';

					$signature = educare_get_attachment($remarks['teacher']['signature'], true);

					if ($signature) {
						echo '<img src="'.esc_url( educare_get_attachment($remarks['teacher']['signature'], true) ).'">';
					}

					echo '</div></div>
					<input type="hidden" name="Others[remarks][teacher][signature]" value="'.esc_attr($remarks['teacher']['signature']).'">
					<span class="button attachmentInput">'.__('Select Signature', 'educare').'</span>
					<span class="button attachmentRemove">'.__('Remove', 'educare').'</span>
					</p></div>';

					// principal remarks
					echo '<p>'.__("Principal's Remarks", "educare").':</p>';
					echo '<input type="text" name="Others[remarks][principal][comments]" value="'.esc_attr($remarks['principal']['comments']).'" placeholder="'.__('Comments', 'educare').'">';
					// Principal name
					echo '<input type="text" name="Others[remarks][principal][name]" value="'.esc_attr($remarks['principal']['name']).'" placeholder="'.__('Name of Principal', 'educare').'">';
					// remarks date
					echo '<input type="date" name="Others[remarks][principal][date]" value="'.esc_attr($remarks['principal']['date']).'" placeholder="'.__('Date', 'educare').'">';
					// signature
					echo '<div class="getAttachment">
					<p>Signature:';
					echo '<div class="attachmentPreview"><div class="attachmentImg">';

					$signature = educare_get_attachment($remarks['principal']['signature'], true);

					if ($signature) {
						echo '<img src="'.esc_url( educare_get_attachment($remarks['principal']['signature'], true) ).'">';
					}

					echo '</div></div>
					<input type="hidden" name="Others[remarks][principal][signature]" value="'.esc_attr($remarks['principal']['signature']).'">
					<span class="button attachmentInput">'.__('Select Signature', 'educare').'</span>
					<span class="button attachmentRemove">'.__('Remove', 'educare').'</span>
					</p></div>';
				}
				
				if ($roles == 'students' or $roles == 'results') {
					echo '<h2>'.__('Subject List', 'educare').'</h2>';

					echo '<div id="result_msg">';
					educare_get_subject_field($roles);
					echo '</div>';
				} 
				
				else {
					// teachers class and subject fields
					// do somethink!
					echo '<p>'.__('Select Subject', 'educare').':</p>';
					echo educare_guide_for(__('You can select multiple subject by pressing <code>Ctrl + Subject</code>.', 'educare'));
					
					// keep all selected
					// $all_selected = '';
					// if (in_array('all', $_POST['Subject'][0]['all'])) {
					// 	$all_selected = 'all';
					// }
					
					echo '<select name="Subject[0][all][]" multiple>';
					// echo '<option value="all" '.esc_attr( selected( $all_selected, 'all', false ) ).'>'.__('All', 'educare').'</option>';
					echo educare_get_all_subject(true);
					echo '</select>';

				
					$terms = educare_get_template_data('fields', false);

					if ($terms) {

						$selected_terms = array();
						if (isset($_POST['Others']['terms'])) {
							$selected_terms = $_POST['Others']['terms'];
						}

						echo '<p>'.__('Select Terms:', 'educare').'</p>';
						echo '<select name="Others[terms][]" multiple>';
						echo '<option value="0" '.selected(in_array(0, $selected_terms), true, false).'>All</option>';

						foreach ($terms as $terms_key => $terms_value) {
							$is_selected = in_array($terms_key, $selected_terms);

							echo '<option value="'.esc_attr($terms_key).'" '.selected($is_selected, true, false).'>'.esc_html($terms_value['title']).'</option>';
						}

						echo '</select>';
					}
				}
				
				$submit = 'Add';
				if (isset($_POST['default']['id'])) {
					$submit = 'Update';
				}

				echo '<br>';
				echo '<div class="select">';
				if ($submit == 'Update') {
					echo '<button type="submit" name="crud" class="educare_button crud-forms"><i class="dashicons dashicons-update"></i> '.esc_html__($submit, 'educare').'</button>';
				} else {
					echo '<button type="submit" name="crud" class="educare_button crud-forms"><i class="dashicons dashicons-plus-alt"></i> '.esc_html__($submit, 'educare').'</button>';
				}

				if (isset($_POST['default']['id'])) {
					if ($roles == 'results') {
						$url = '/'.educare_check_status("results_page");
					} else {
						$url = admin_url();
						$url .= 'admin.php?page=educare-all-'.$roles.'';
						$url .= '&profiles=' . $id;
					}

					echo '<input type="hidden" name="id" value="'.esc_attr( $id ).'">';
					echo '<button type="submit" name="view" formaction="'.esc_url($url).'" formtarget="_blank" class="educare_button"><i class="dashicons dashicons-visibility"></i> '.__('View', 'educare').'</button>';
					echo '<button type="submit" name="delete" class="educare_button crud-forms"><i class="dashicons dashicons-trash"></i> '.__('Delete', 'educare').'</button>';
					echo '<button type="button" class="educare_button" onclick="location.reload()"><i class="dashicons dashicons-plus-alt"></i> '.__('Add New', 'educare').'</button>';
				}

				echo '</div>';

				// Security nonce for this form.
				$nonce = wp_create_nonce( 'educare_crud_data' );
				echo '<input type="hidden" name="crud_data_nonce" value="'.esc_attr($nonce).'">';
				$nonce = wp_create_nonce( 'educare_form_nonce' );
				echo '<input type="hidden" name="nonce" value="'.esc_attr($nonce).'">';
				?>
				
			</div>
		</form>
	</div>
	
	<?php
}





/**
 * Filters the array elements based on position and returns the sorted array.
 *
 * @param array  $array  The input array to be filtered and sorted.
 * @param string $order  Optional. The order in which the elements should be sorted. Default is 'asc'.
 *
 * @return array The sorted array with elements arranged according to the specified order.
 */

function educare_position_filtering($array, $order = 'asc') {
	uasort($array, function($a, $b) use ($order) {
		$a_value = is_numeric($a) ? $a : substr($a, strpos($a, ' ') + 1);
		$b_value = is_numeric($b) ? $b : substr($b, strpos($b, ' ') + 1);

		if ($order == 'asc') {
			return $b_value <=> $a_value;
		} else {
			return $a_value <=> $b_value;
		}
	});

	return $array;
}


?>