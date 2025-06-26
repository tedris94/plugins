<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * Adds a custom user role for Educare students.
 *
 * This function creates a custom user role named "Educare Students" with specific capabilities. It utilizes the WordPress
 * function `add_role` to add the role. By default, the function grants the new role minimal capabilities, but it's possible
 * to customize these capabilities according to your requirements.
 *
 * @return void
 */
function add_educare_roles() {
  // Define the role name and display name
  $role_name = 'educare_students';
  $display_name = 'Educare Students';

  // Add the Educare Students role
  add_role($role_name, $display_name);
}
add_action('init', 'add_educare_roles');


?>