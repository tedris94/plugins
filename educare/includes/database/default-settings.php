<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * ### Educare default settings
 *
 * @since 1.0.0
 * @last-update 3.4.8
 *
 * @param mixed $list			For Settings, Class, Group, Exam, Year, Extra_field
 * @return void
 */

function educare_add_default_settings($list, $show_data = null, $new_data = null) {
	global $wpdb;
	$table = $wpdb->prefix."educare_settings";

	if ($list == 'Settings') {
		// Default setting for settings
		$target = array(
			'confirmation' => 'checked',
			'guide' => 'checked',
			'photos' => 'checked',
			'auto_results' => 'checked',
			'position_filtering' => 'unchecked',
			// 'auto_average' => 'checked',
			// 'auto_positions' => 'unchecked',
			'certificate_system' => 'checked',
			'rattings_system' => 'checked',
			'grading_key' => 'unchecked',
			'student_info' => 'checked',
			'details' => 'checked',
			'grade_sheet' => 'checked',
			'show_banner' => 'checked',
			'quick_overview' => 'checked',
			'remarks' => 'checked',
			'publish_results_by_teachers' => 'unchecked',
			'print_only_remarks' => 'unchecked',
			'results_card_template' => 'educare_default_results_card',
			'search_form_template' => 'educare_default_search_form',
			'profiles_template' => 'educare_default_profiles',
			'certificate_template' => 'educare_default_certificate',
			'custom_certificate_bg' => '',
			'data_per_page' => 15,
			'advance' => 'checked',
			'problem_detection' => 'checked',
			'clear_data' => 'unchecked',
			// 'custom_results' => 'unchecked',
			'results_page' => 'results',
			'profiles_page' => 'profiles',
			'edit_profiles_page' => 'edit-profiles',
			'front_dashboard' => 'dashboard',
			'institute' => 'Name Of The Institutions (Title) Or Slogan',
			'optional_sybmbol' => '✓',
			'passed' => 'Passed',
			'failed' => 'Failed',
			'group_subject' => 3,
			'user_profiles' => 'checked',
			// 'secure_results' => 'unchecked',
			're_captcha' => 'unchecked',
			'site_key' => '',
			'secret_key' => '',
			'display' => [
				'Name' => [
					'title' => 'Name',
					'status' => 'checked',
					'type' => 'text',
				],
				'Roll_No' => [
					'title' => 'Roll No',
					'status' => 'checked',
					'type' => 'number',
				],
				'Regi_No' => [
					'title' => 'Regi No',
					'status' => 'checked',
					'type' => 'number',
				],
				'Class' => [
					'title' => 'Class',
					'status' => 'checked',
					'type' => 'text',
				],
				'Group' => [
					'title' => 'Group',
					'status' => 'checked',
					'type' => 'text',
				],
				'Exam' => [
					'title' => 'Exam',
					'status' => 'checked',
					'type' => 'text',
				],
				'Year' => [
					'title' => 'Year',
					'status' => 'checked',
					'type' => 'number',
				],
				'user_pin' => [
					'title' => 'User Pin',
					'status' => 'unchecked',
					'type' => 'password',
				]
			],
			'results_card' => [
				'details' => [
					'details' => [
						'title' => 'Details',
						'subtitle' => '',
						'status' => 'checked'
					],
					'result' => [
						'title' => 'Result',
						'subtitle' => '',
						'status' => 'checked'
					],
					'year' => [
						'title' => 'Year',
						'subtitle' => '',
						'status' => 'checked'
					],
					'gpa' => [
						'title' => 'GPA',
						'subtitle' => '',
						'status' => 'checked'
					]
				],

				'grade_sheet' => [
					'grade_sheet' => [
						'title' => 'Grade Sheet',
						'subtitle' => '',
						'status' => 'checked'
					],
					'no' => [
						'title' => 'No.',
						'subtitle' => '',
						'status' => 'checked'
					],
					'subject' => [
						'title' => 'Subject',
						'subtitle' => '',
						'status' => 'checked'
					]
				],

				'template' => [
					'educare_default_results_card' => [
						'marks' => array(
							'title' => 'Marks',
							'subtitle' => 'Exam Marks',
							'status' => 'checked',
							'default_value' => '80',
							'placeholder' => '00',
							'type' => 'number',
							'auto' => 'off'
						),
						'gpa' => array(
							'title' => 'GPA',
							'subtitle' => 'Number Points',
							'status' => 'checked',
							'default_value' => '5',
							'placeholder' => '5',
							'type' => 'number',
							'auto' => 'on'
						),
						'grade' => array(
							'title' => 'Grade',
							'subtitle' => 'Letter Grade',
							'status' => 'checked',
							'default_value' => 'A+',
							'placeholder' => 'A+',
							'type' => 'text',
							'auto' => 'on'
						),
					]
				]
			],
			'banner' => [
				'title' => 'Institutions Name',
				'subtitle1' => 'Enter the school address',
				'subtitle2' => 'School Contact Phone No',
				'subtitle3' => 'Founded in '. date('Y'),
				'logo1' => 0, // Default logo EDUCARE_URL . 'assets/img/card-logo1.svg'
				'logo2' => 0, // Default logo EDUCARE_URL . 'assets/img/card-logo2.svg'
				// 'status' => 'checked'
			],
			'grade_system' => [
				'current' => 'Default',
				'rules' => [
					'Default' => [
						'80-100' => [5, 'A', 'Excellence'],
						'70-79'  => [4, 'B', 'Very Good'],
						'60-69'  => [3.5, 'C', 'Good'],
						'50-59'  => [3, 'D', 'Average'],
						'40-49'  => [2, 'E', 'Fair'],
						'0-39'   => [1, 'F', 'Very Poor']
					]
				]
			],
			'educare_info' => [
				'version' => '1.2.0',
				'educare_settings' => '1.0',
				'educare_results' => '1.0',
				'package' => 'pro'
			]
		);
	} elseif ($list == 'Class') {
		// Default setting for class
		$subject = array(
			'English',
			'Mathematics',
			'ICT',
			'History',
			'Social Studies',
			'Fine Arts',
			'Science',
			'Economics',
			'Religion',
			'Agriculture'
		);
		$target = array(
			'Class 6' => $subject,
			'Class 7' => [],
			'Class 8' => [],
			'Class 9' => [],
			'Class 10' => []
		);
	} elseif ($list == 'Group') {
		// Default group wise subject
		$sub_for_science = array(
			'Physics',
			'Chemistry',
			'Biology'
		);
		
		$sub_for_commerce = array(
			'Finance & Banking',
			'Accounting',
			'Business Ent'
		);

		// Default setting for group
		$target = array(
			'Science' => $sub_for_science,
			'Commerce' => $sub_for_commerce,
			'Arts' => []
		);
	} elseif ($list == 'Rattings') {
		// Default setting for group;
		$target = array(
			'Development' => [
				'Activeness',
				'Attendance',
				'Punctuality',
				'Self control',
				'Honesty',
				'Humility',
				'Leadership'
			],
			'Skills' => [
				'Handwriting',
				'Fluency',
				'Musical skills'
			]
		);
	} elseif ($list == 'Exam') {
		// Default setting for exam
		$target = array(
			'Exam no 1',
			'Exam no 2',
			'Exam no 3'
		);
	} elseif ($list == 'Year') {
		// Default setting for year
		$target = array(
			'2023',
			'2024',
			'2025',
		);
	} elseif ($list == 'Extra_field') {
		// Default setting for extra fiels
		$target = array(
			array (
				'title' => 'Date of Birth',
				'type' => 'date',
        'for' => Array ('students', 'teachers')
			),
			array (
				'title' => 'Fathers Name',
				'type' => 'text',
        'for' => Array ('students', 'teachers')
			),
			array (
				'title' => 'Mothers Name',
				'type' => 'text',
        'for' => Array ('students', 'teachers')
			),
			array (
				'title' => 'Fathers Name',
				'type' => 'Type',
        'for' => Array ('students')
			),
			array (
				'title' => ' Mobile No',
				'type' => 'number',
        'for' => Array ('students')
			)
		);
	} else {
		$target = array();
	}

	if ($show_data) {
		return $target;
	} else {
		$search = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $table WHERE list = %s", $list)
		);

		if ($new_data) {
			$target = $new_data;
		}
	}
	
	if ($search) {
		foreach ($search as $print) {
			$id = $print->id;
			unset($print->id);
			$print->data = json_encode($target);
		}

		$print = json_decode(json_encode($print), TRUE);
		$wpdb->update($table, $print, array('ID' => $id));
		
	} else {
		$wpdb->insert($table, array(
			"list" => $list,
			"data" => json_encode($target)
		));
	}
}

// create function for store default settings/all in one
function educare_default_settings() {
	educare_add_default_settings('Settings');
	educare_add_default_settings('Class');
	educare_add_default_settings('Group');
	educare_add_default_settings('Rattings');
	educare_add_default_settings('Exam');
	educare_add_default_settings('Year');
	educare_add_default_settings('Extra_field');
}


?>