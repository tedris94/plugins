<?php
/**
 * Plugin Name: Educare Import System
 * Plugin URI: https://fixbd.net/educare/add-ons/import-system
 * Description: Educare Import System allow user to import data from Excel files, with the extensions of (.xlsx, .xlsm or .xls). Also, it will help to create demo file for import system.
 * Version: 1.0.0
 * Author: FixBD
 * Author URI: http://fixbd.net
 * License: Commercial License (Regular and Extended)
 * License URI: https://codecanyon.net/licenses/standard
 * Text Domain: educare-import-system
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Check if the main plugin is active
if (!function_exists('educare_is_active')) {
  function educare_is_active() {
    // Load the necessary WordPress file
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');

    // Replace 'myplugin/myplugin.php' with the main plugin's plugin folder and main plugin file name
    if (is_plugin_active('educare/educare.php')) {
      // Main plugin is active
      return true;
    } else {
      // Main plugin is not active
      return false;
    }
  }
}

// is main plugin active
if (educare_is_active()) {
  // Main plugin is active, perform addon-specific actions
	define('EDUCARE_IMPORT_SYSTEM', plugin_dir_path(__FILE__));
	define('EDUCARE_IMPORT_SYSTEM_FOLDER', basename(dirname(__FILE__)));
	define('EDUCARE_IMPORT_SYSTEM_URL', plugin_dir_url(EDUCARE_IMPORT_SYSTEM_FOLDER).EDUCARE_IMPORT_SYSTEM_FOLDER.'/');

	// Import system functionality
	require_once(EDUCARE_IMPORT_SYSTEM.'inc/functions.php');

}

?>