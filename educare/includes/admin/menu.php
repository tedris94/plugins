<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * Add menu in admin dashboard
 * 
 * == Valited menu are ==
 * - All Students 					(@since 1.2.4)
 * - All Teachers 					(@since 1.2.5)
 * - All Results 						(@since 1.0.0)
 * - Attendance							(@since 1.2.4)
 * - Mark Sheet							(@since 1.2.4)
 * - Performance						(@since 1.4.0)
 * - Management							(@since 1.4.0)
 * - Settings 							(@since 1.0.0)
 * - About Us								(@since 1.0.0)
 * 
 * @since 1.0.0
 * @last-update 1.4.0
 *
 * @return void
 */

function educare_menu() {
	// make educare main menu and sub menu global for Enqueue style & script (educare_enqueue_styles)
	global $educare_main_menu, $educare_sub_menu, $educare_slug_hook;

	// main menu
	// $educare_main_menu = add_menu_page('View Results', 'Educare', 'manage_options','educare-view-results','view_results',plugins_url('/Educare/assets/img/icon.svg'), 2 /*56*/);
	
	// Register and render admin bar menu items.
	$educare_main_menu = add_menu_page(
		__('All Students', 'educare'),
		'Educare',
		'access_educare_all_students',
		'educare-all-students',
		'educare_admin_menu',
		'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300"><g fill="#033e44"><path d="M208.1 63.8q-.1 0-.2.1l-.3.1-75.2 35.2c-21.8 9.3-38.4 14.6-55 20q-.1 0-.2.1 0 0-.1.1 0 .1 0 .1 0 .1 0 .2 0 0 0 .1l47.6 177q0 0 0 .1.1 0 .1 0 0 .1.1.1 0 0 0 .1 0 0 .1 0 0 0 0 0 0 0 .1 0c2.4.6 4.6 1 6.4 1.3 1.3.2 2.4.3 3.3.3 1.7 0 2.9-.3 4-.6h.1l117.7-50.7q.3-.1.3-.4v-.1l-12.1-45.8q0 0-.1 0 0-.1 0-.1 0 0 0 0 0-.1-.1-.1 0 0 0 0-.1 0-.1-.1 0 0 0 0-.1 0-.1 0 0 0 0 0 0 0-.1 0 0 0 0 0 0 0 0 0-.1 0-.1 0 0 0 0 0l-80 36.8-4.8-18.2 80-37.8q0 0 .1-.1 0 0 .1-.1 0 0 0-.1 0 0 0-.1v-.1l-12.6-47q-.1-.1-.1-.1 0-.1 0-.1 0 0-.1 0 0-.1 0-.1-.1 0-.1 0-.1 0-.1 0-.1-.1-.1 0-.1 0-.1 0l-79.7 37.5-4.8-17.7 79.5-37.8q0 0 .1-.1 0 0 .1-.1 0 0 0-.1 0-.1 0-.2l-13-51.2q0 0-.1 0 0-.1 0-.1 0-.1-.1-.1 0 0-.1 0-.1-.1-.2-.1z"/><path d="M117.1 113.5c-11.7 3.4-24 6.9-35.3 6.9q-4.1 0-8.2-.7c-13.5-2.4-25-11.7-29.3-21-4.5-9.6-1-18.6 1.8-26l6 3.5c-4 5-7.3 9.2-8.4 14.2s0 11 1.3 18v-.2l39.1 147.3-.3-.7.1.1c4.9 10 9.3 19 16.2 24.7 6.8 5.6 16.3 8.1 26.9 10.7q.7.2 1.3.6.6.4.9 1 .4.6.5 1.3.1.7-.1 1.4-.2.7-.6 1.2-.4.6-1 1-.6.4-1.3.5-.7.1-1.4-.1c-10.2-2.6-21.4-5.3-29.7-12.1-8.3-6.9-13.4-17.5-18.1-27.1h-.1q-.1-.4-.2-.7L38.1 110q0-.1-.1-.3c-1.2-6.6-2.7-14.2-1.2-20.9 1.5-6.8 6-12.3 9.8-17.1.5-.6 1.3-1.1 2.1-1.3.9-.1 1.7 0 2.4.5a3.14 3.14 0 0 1 1.6 1.8c.3.8.3 1.7 0 2.5-2.9 7.8-5 14-2 20.5 3.3 7 12.7 14.9 24.2 17q3.3.6 6.9.6c10.1 0 21.4-3.2 33.3-6.6q.7-.2 1.4-.1.7.1 1.3.4.7.3 1.1.9.4.6.6 1.2.2.7.1 1.4-.1.8-.4 1.4-.3.6-.9 1-.6.5-1.2.6z"/></g><path d="M146.7 4.4q0 0 0 0L71.8 99.6q0 0 0 .1 0 0 .1 0L165.4 29q0-.1 0-.1 0 0 0 0L146.8 4.4q0 0-.1 0z" fill="#d10f0f"/><path fill-rule="evenodd" d="M191.7 25.1q0 0 0 0L82.8 106q0 0 0 0 0 .1 0 .1.1 0 .1 0L197.3 55v-.1l-5.6-29.8q0 0 0 0zM82.8 106z" fill="#f80000"/><path d="M93.4 1q-.1 0-.1 0L60.1 94.3q0 0 .1.1 0 0 0 0l50.2-62.3L93.4 1q0 0 0 0z" fill="#b40c0c"/></svg>' ),
		4
	);
	
	$educare_slug_hook = array();
	// Create submenu
	$educare_sub_menu = array(
		'All Students',
		'All Teachers',
		'All Results',
		'Attendance',
		'Mark Sheet',
		'Performance',
		'Management',
		'Settings',
		'About Us'
	);

	// Apply submenu
	foreach ($educare_sub_menu as $value) {
		$slug = 'educare-'.str_replace(' ', '-', strtolower($value));
		$cap = 'access_'. str_replace('-', '_', $slug);
		
		add_submenu_page(
			'educare-all-students',           // parent slug
			__($value, 'educare'),            // page title
			__($value, 'educare'),         		// menu title
			$cap,         										// capability
			$slug,		 												// slug
			'educare_admin_menu'        			// callback
		);
		
		array_push($educare_slug_hook, 'educare_page_'.$slug);
	}

}

add_action("admin_menu", "educare_menu");


/**
 * ### Function for menu callback
 *
 * Load (admin) page for specific page
 *
 * @since 1.2.4
 * @last-update 1.2.4
 *
 * @return void
 */

function educare_admin_menu() {
	global $educare_sub_menu;
	$educare_sub_menu = array_map('strtolower', $educare_sub_menu);
	$slug = '';

	if (isset($_GET['page'])) {
		$slug = sanitize_text_field( $_GET['page'] );
	}

	foreach ($educare_sub_menu as $page) {
		// $value = 'access_educare_'. strtolower(str_replace(' ', '_', $page));
		$page = str_replace(' ', '-', $page);
		if ("educare-$page" == $slug) {
			echo '<div id="educare-loading"><div class="educare-spinner"></div></div>';
			
			if (educare_database_check('educare_settings')) {
				educare_database_error_notice(true, 'educare_settings');
			} else {
				// Auto fix if anythink wrong in educare database
				echo educare_ai_fix();
				// include page data for specific page
				include (EDUCARE_ADMIN."menu/$page.php");
			}
		}
	}
}



/**
 * Educare roles and capabilities
 * @since 1.4.2
 * @last-update 1.4.2
 *
 * @return void
 */

 function educare_roles_caps() {
	// remove_role('educare_teachers');
	add_role(
		'educare_admin',
		'Educare Admin',
		array(
			// Dashboard
			'read' => true,
			'upload_files' => true,
			'edit_files' => true,
			'delete_posts' => true,
		)
	);

	add_role(
		'educare_class_admin',
		'Educare Class Admin',
		array(
			// Dashboard
			'read' => true,
			'upload_files' => true,
			'edit_files' => true,
			'delete_posts' => true,
		)
	);

	add_role(
		'educare_teachers',
		'Educare Teachers',
		array(
			// Dashboard
			'read' => true,
			'upload_files' => true,
			'edit_files' => true,
			// 'delete_posts' => true,

			// Custome capability
			'access_educare_attendance' => true,
			'access_educare_mark_sheet' => true,
		)
	);

	$cap = array (
		'All Students',
		'All Teachers',
		'All Results',
		'Attendance',
		'Mark Sheet',
		'Performance',
		'Management',
		'Settings',
		'About Us'
	);

	// Gets the educare_rules_role role object.
	$administrator = get_role( 'administrator' );
	$educare_admin = get_role( 'educare_admin' );
	$educare_teachers = get_role( 'educare_teachers' );

	$custom_post = array(
		'educare_quiz',
		'study_material'
	);

	$user_role = array(
		'administrator',
		'educare_admin',
		'educare_teachers'
	);

	foreach ($custom_post as $typeSingle) {
		$typePlural = $typeSingle.'s';

		foreach ($user_role as $role) {
			$$role->add_cap('edit_'. $typeSingle);
			$$role->add_cap('read_'. $typeSingle);
			$$role->add_cap('delete_'. $typeSingle);
			$$role->add_cap('edit_'. $typePlural);
			$$role->add_cap('publish_'. $typePlural);
			$$role->add_cap('delete_'. $typePlural);
			$$role->add_cap('delete_published_'. $typePlural);
			$$role->add_cap('edit_published_'. $typePlural);

			if ($role !== 'educare_teachers') {
				$$role->add_cap('edit_others_'. $typePlural);
				$$role->add_cap('read_private_'. $typePlural);
				$$role->add_cap('delete_private_'. $typePlural);
				$$role->add_cap('delete_others_'. $typePlural);
				$$role->add_cap('edit_private_'. $typePlural);
			}
		}
	}
	
	$administrator->add_cap('manage_academia_taxonomy', true);
	$educare_teachers->add_cap('manage_academia_taxonomy', true);
	$educare_admin->add_cap('manage_academia_taxonomy', true);

	foreach ($cap as $value) {
		$value = 'access_educare_'. strtolower(str_replace(' ', '_', $value));
		
		// Add educare capability into admin roles.
		$administrator->add_cap( $value, true );
		$educare_admin->add_cap( $value, true );
	}


	// add educare class admin
	// Add educare_admin capabilities into educare_class_admin role.
	$educare_admin_capabilities = $educare_admin->capabilities;
	$educare_class_admin = get_role('educare_class_admin');

	foreach ($educare_admin_capabilities as $capability => $value) {
		// Exclude certain capabilities from 'Performance', 'Management', 'Settings'
		if (!in_array($capability, array('access_educare_performance', 'access_educare_management', 'access_educare_settings'))) {
			$educare_class_admin->add_cap($capability, $value);
		}
	}
	
}

// Add educare_rules_role capabilities, priority must be after the initial role definition.
// educare_roles_caps();




function educare_posts_for_current_author($query) {
  global $user_level;

  if($query->is_admin && $user_level <= 7) { // Valid for all users except the administrators
    global $user_ID;
    $query->set('author', $user_ID);
    unset($user_ID);
  }
  unset($user_level);

  return $query;
}
// add_filter('pre_get_posts', 'educare_posts_for_current_author');




function educare_apply_school_admin() {
	$user_id = get_current_user_id(); // Replace with the user ID of the user from whom you want to remove capabilities.
	
	// remove settings and management from dashbord if current user is addmin for specific school
	$user = new WP_User($user_id);
	$get_school = get_user_meta($user_id, 'School', true);
	
	if ($get_school) {
		// Remove the specific capability from the user.
		$user->add_cap('access_educare_settings', false);
		$user->add_cap('access_educare_management', false);
	} else {
		if (current_user_can( 'educare_admin' )) {
			$user->add_cap('access_educare_settings', true);
			$user->add_cap('access_educare_management', true);
		}
	}

	// $user->remove_cap('access_educare_settings');
	// $user->remove_cap('access_educare_management');
}

// under constructions
add_action( 'init', 'educare_apply_school_admin');



function educare_hide_menu_items() {
	// Specify the capability required to see the menu
	$required_capability = 'access_educare_sttings'; // Replace with your desired capability

	// Remove menu if the user doesn't have the required capability
	if (!current_user_can($required_capability)) {
		$menu_slug = 'educare-all-students';
		// Specify the parent menu slug you want to hide
		// remove_menu_page($menu_slug);

		// Remove the submenus of the parent menu
		remove_submenu_page($menu_slug, 'educare-all-students');
		remove_submenu_page($menu_slug, 'educare-all-teachers');
		remove_submenu_page($menu_slug, 'educare-all-results');
		remove_submenu_page($menu_slug, 'educare-performance');
		remove_submenu_page($menu_slug, 'educare-management');
		remove_submenu_page($menu_slug, 'educare-settings');
		remove_submenu_page($menu_slug, 'educare-about-us');
		
		// Add more submenu pages to remove if needed
	}
}

// add_action('admin_menu', 'educare_hide_menu_items');




/**
 * ### Educare enqueue style & javascript
 * 
 * Load CSS, JS to specific pages from created menu and submenu ( only for Educare pages )
 * 
 * @since 1.0.0
 * @last-update 1.2.4
 * 
 * @param mixed $hook
 * @return void
 */

function educare_enqueue_styles( $hook) {
	// globalize educare main menu and sub menu '$educare_slug_hook'
	global $educare_main_menu, $educare_slug_hook;
	// add main (main menu) page
	array_push($educare_slug_hook, $educare_main_menu);

	// ignore educare style and script if not a educare page
	if ( !in_array($hook, $educare_slug_hook) ) {
		return;
	}

	// Educare stylesheet
	wp_enqueue_style('educare', EDUCARE_URL.'assets/css/educare.css');
	wp_enqueue_style('clone-field', EDUCARE_URL.'assets/css/clone-field.css');
	// for educare file selector
	wp_enqueue_media();
	
	// JavaScript link
	wp_enqueue_script('jquery'); // That's men script now place at the bottom
	wp_enqueue_script('educare-admin', EDUCARE_URL.'assets/js/educare.js', array('jquery'), '1.0', true);
	wp_enqueue_script('e-pagination', EDUCARE_URL.'assets/js/paginate.js');
	wp_enqueue_script('cloneField', EDUCARE_URL.'assets/js/clone-field-1.0.js');
	wp_enqueue_script('unsaved-changes-script', EDUCARE_URL.'assets/js/unsaved-changes.js', array('jquery'), '1.0', true);

	wp_localize_script( 'educare-admin', 'educareSettings', array(
		'advance'   => educare_esc_str(educare_check_status('advance')),
		'confirmation' => educare_check_status('confirmation'),
		'db_error' => educare_guide_for('db_error', '', false),
		'photos' => educare_check_status('photos'),
		'group_subject' => educare_check_status('group_subject'),
	) );

	wp_localize_script( 'educare-admin', 'educareNonce', array(
		'edit_grade_system' => wp_create_nonce( 'edit_grade_system' ),
	) );
	
}

add_action( 'admin_enqueue_scripts', 'educare_enqueue_styles');



?>