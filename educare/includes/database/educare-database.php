<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Educare default settings
require_once(EDUCARE_INC.'database/default-settings.php');

/**
 * ### Check educare database version
 * 
 * @since 1.2.0
 * @last-update 1.2.4
 * 
 * @return void
 */

function educare_database_check($db) {
	global $wpdb;
	$table = $wpdb->prefix.$db;

	// if database not exists
	if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
		// database not exists
	 return true;
	} else {
		// if database exists, and check (educare) database version
		$info = educare_check_status('educare_info');
		// if database version exists, that's mean our database version 1.2.0+ otherwise our database is old. (educare) old databse not support educare_info key.

		if ($info) {

			if ($db == 'educare_settings') {
				$current_db = EDUCARE_SETTINGS_VERSION;
				$istaled_db = $info->$db;

				// if database db_version exists. check if our current (educare) database version 1.0+ or not. if is less then 1.0 then return TRUE. and create/insert our latest database
				if (!($current_db <= $istaled_db)) {
					return true;
				}
				
			}
			
		} else {
			// old (educare) database
			return true;
		}
		
	}
}



/**
 * ### Create educare database
 * 
 * Create educare database for store results, students and settings data
 * - educare_settings
 * - educare_results
 * - educare_students
 * - educare_marks
 * 
 * @since 1.0.0
 * @last-update 1.2.8
 * 
 * @return void
 */

function educare_database_table($db = null) {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

	// Create table for educare (plugins) settings
  $settings = $wpdb->prefix."educare_settings";

  $table1 = "CREATE TABLE $settings (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`list` varchar(80) NOT NULL,
		`data` longtext NOT NULL,
		`School` varchar(255) NOT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY list (list)
	) $charset_collate;";

	// Create table for educare results system
	$results = $wpdb->prefix."educare_results";
   
	$table2 = "CREATE TABLE $results (
		`id` mediumint(11) NOT NULL AUTO_INCREMENT,
		`user_pin` varchar(255) NOT NULL,
		`pin_status` varchar(100) NOT NULL,
		`Name` varchar(80) NOT NULL,
		`Roll_No` varchar(80) NOT NULL,
		`Regi_No` varchar(80) NOT NULL,
		`Class` varchar(80) NOT NULL,
		`Exam` varchar(80) NOT NULL,
		`Year` varchar(80) NOT NULL,
		`Group` varchar(80) NOT NULL,
		`Details` longtext NOT NULL,
		`Subject` longtext NOT NULL,
		`Result` varchar(80),
		`GPA` varchar(80),
		`School` varchar(255) NOT NULL,
		`Others` longtext NOT NULL,
		`Status` varchar(80),
		PRIMARY KEY (id)
  ) $charset_collate;";

	// Create table for educare (plugins) settings
  $students = $wpdb->prefix."educare_students";

  $table3 = "CREATE TABLE $students (
		`id` mediumint(11) NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) NOT NULL,
		`user_login` varchar(60) NOT NULL,
		`user_email` varchar(100) NOT NULL,
		`user_pass` varchar(255) NOT NULL,
		`user_pin` varchar(255) NOT NULL,
		`pin_status` varchar(100) NOT NULL,
		`Name` varchar(80) NOT NULL,
		`Roll_No` varchar(80) NOT NULL,
		`Regi_No` varchar(80) NOT NULL,
		`Class` varchar(80) NOT NULL,
		`Year` varchar(80) NOT NULL,
		`Group` varchar(80) NOT NULL,
		`Details` longtext NOT NULL,
		`Subject` longtext NOT NULL,
		`Student_ID` mediumint(11) NOT NULL,
		`School` varchar(255) NOT NULL,
		`Others` longtext NOT NULL,
		`Status` varchar(80) NOT NULL,
		PRIMARY KEY (id)
  ) $charset_collate;";

	// Create table for educare (plugins) settings
  $marks = $wpdb->prefix."educare_marks";

  $table4 = "CREATE TABLE $marks (
		`id` mediumint(11) NOT NULL AUTO_INCREMENT,
		`Class` varchar(80) NOT NULL,
		`Group` varchar(80) NOT NULL,
		`Exam` varchar(80) NOT NULL,
		`Year` varchar(80) NOT NULL,
		`Marks` longtext NOT NULL,
		`Details` longtext NOT NULL,
		`School` varchar(255) NOT NULL,
		`Others` longtext NOT NULL,
		`Status` varchar(80) NOT NULL,
		PRIMARY KEY (id)
  ) $charset_collate;";

	// Create table for educare (plugins) settings
  $attendance = $wpdb->prefix."educare_attendance";

  $educare_attendance = "CREATE TABLE $attendance (
		`id` mediumint(11) NOT NULL AUTO_INCREMENT,
		`Class` varchar(80) NOT NULL,
		`Subject` varchar(80) NOT NULL,
		`Date` varchar(80) NOT NULL,
		`Student_ID` mediumint(11) NOT NULL,
		`Details` longtext NOT NULL,
		`School` varchar(255) NOT NULL,
		`Others` longtext NOT NULL,
		`attendance` varchar(80) NOT NULL,
		PRIMARY KEY (id)
  ) $charset_collate;";

	// Create table for educare (plugins) settings
  $teachers = $wpdb->prefix."educare_teachers";

  $table5 = "CREATE TABLE $teachers (
		`id` mediumint(11) NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) NOT NULL,
		`user_login` varchar(60) NOT NULL,
		`user_email` varchar(100) NOT NULL,
		`user_pass` varchar(255) NOT NULL,
		`pin_status` varchar(100) NOT NULL,
		`Name` varchar(80) NOT NULL,
		-- `Roll_No` varchar(80) NOT NULL,
		-- `Regi_No` varchar(80) NOT NULL,
		-- `Class` varchar(80) NOT NULL,
		-- `Year` varchar(80) NOT NULL,
		-- `Group` varchar(80) NOT NULL,
		`Details` longtext NOT NULL,
		`Subject` longtext NOT NULL,
		-- `Student_ID` mediumint(11) NOT NULL,
		`School` varchar(255) NOT NULL,
		`Others` longtext NOT NULL,
		`Status` varchar(80) NOT NULL,
		PRIMARY KEY (id)
  ) $charset_collate;";

	// Create table for educare (plugins) settings
  $payment = $wpdb->prefix."educare_payment";

  $educare_payment = "CREATE TABLE $payment (
		`id` mediumint(11) NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) NOT NULL,
		`reference` varchar(255) NOT NULL,
		`method` longtext NOT NULL,
		`data` longtext NOT NULL,
		`School` varchar(255) NOT NULL,
		`status` varchar(80) NOT NULL,
		PRIMARY KEY (id)
  ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if ($db == 'educare_settings') {
		dbDelta($table1);
	} elseif ($db == 'educare_results') {
		dbDelta($table2);
	} elseif ($db == 'educare_students') {
		dbDelta($table3);
	} elseif ($db == 'educare_marks') {
		dbDelta($table4);
	} elseif ($db == 'educare_attendance') {
		dbDelta($educare_attendance);
	} elseif ($db == 'educare_teachers') {
		dbDelta($table5);
	} elseif ($db == 'educare_payment') {
		dbDelta($educare_payment);
	} else {
		if (educare_database_check('educare_settings')) {
			dbDelta($table1);
			dbDelta($table2);
			dbDelta($table3);
			dbDelta($table4);
			dbDelta($educare_attendance);
			dbDelta($table5);
			dbDelta($educare_payment);
		}
	}

	if (educare_database_check('educare_settings')) {
		// Set educare default settings
		educare_default_settings();
	}
}


/**
 * ### Clean Educare DB
 * 
 * Clean all (educare) data from database, when user remove/delete/uninstall educare from plugin list. If user uncheck Clear Data at educare settings, this action will be ignored.
 * 
 * @since 1.4.0
 * @last-update 1.4.0
 * 
 * @return void
 */

function educare_uninstall_action() {
	if (educare_check_status('clear_data') == 'checked') {
		global $wpdb;

		// Educare database
		$educare_db = array (
			'educare_settings',
			'educare_results',
			'educare_students',
			'educare_marks',
			'educare_teachers',
			'educare_payment',
			'educare_attendance'
		);

		// Drop/remove table from database
		foreach ($educare_db as $table_name) {
			$table = $wpdb->prefix.$table_name;
			$wpdb->query( "DROP TABLE IF EXISTS $table" );
		}

		// remove educare default students photos
		delete_option('educare_files_selector');

		// All clean!!!

	}
}

