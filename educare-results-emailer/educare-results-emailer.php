<?php
/**
 * @package     Educare Results Emailer
 * @version     1.0.0
 * @author      Hyperion Tech Hub (Idris Bala Usman) <u.idris@hyperiontechhub.com>
 * @copyright   GPL-2.0+
 * @link        https://github.com/tedris94/plugins/tree/main/educare-results-emailer
 * @license     https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Plugin Name:     Educare Results Emailer
 * Plugin URI:      https://github.com/tedris94/plugins/tree/main/educare-results-emailer
 * Description:     Professional email automation addon for Educare plugin. Send student results to parents via email with PDF attachments, bulk email capabilities, and customizable templates with Elementor integration.
 * Version:         1.0.0
 * Author:          Hyperion Tech Hub (Idris Bala Usman)
 * Author URI:      https://github.com/tedris94
 * License:         GPL-2.0+
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     educare-results-emailer
 * Domain Path:     /languages
 * Requires at least: 5.0
 * Tested up to:    6.4
 * Requires PHP:    7.2
 * Requires Plugins: educare
 * Network:         false
 * 
 * Educare Results Emailer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * Educare Results Emailer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Educare Results Emailer. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 * 
 * For support and inquiries, contact:
 * Website: https://hyperiontechhub.com
 * Email: info@hyperiontechhub.com
 * Author: u.idris@hyperiontechhub.com
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('EDUCARE_RESULTS_EMAILER_VERSION', '1.0.0');
define('EDUCARE_RESULTS_EMAILER_FILE', __FILE__);
define('EDUCARE_RESULTS_EMAILER_PATH', plugin_dir_path(__FILE__));
define('EDUCARE_RESULTS_EMAILER_URL', plugin_dir_url(__FILE__));
define('EDUCARE_RESULTS_EMAILER_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Check if Educare is active
function educare_results_emailer_check_dependencies() {
    // Check if Educare plugin is active
    if (!in_array('educare/educare.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('admin_notices', 'educare_results_emailer_missing_dependency_notice');
        return false;
    }
    return true;
}

function educare_results_emailer_missing_dependency_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e('Educare Results Emailer requires the Educare plugin to be installed and activated.', 'educare-results-emailer'); ?></p>
    </div>
    <?php
}

// Initialize the plugin
function educare_results_emailer_init() {
    if (!educare_results_emailer_check_dependencies()) {
        return;
    }
    
    // Include required files
    require_once EDUCARE_RESULTS_EMAILER_PATH . 'includes/class-educare-results-emailer.php';
    require_once EDUCARE_RESULTS_EMAILER_PATH . 'includes/class-educare-results-emailer-admin.php';
    
    // Initialize the plugin
    $plugin = new Educare_Results_Emailer();
    $plugin->run();

    // Make admin class globally accessible
    global $educare_results_emailer_admin;
    $educare_results_emailer_admin = new Educare_Results_Emailer_Admin('educare-results-emailer', EDUCARE_RESULTS_EMAILER_VERSION);

    // Check if Elementor is installed and activated
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'educare_results_emailer_elementor_missing_notice');
        return;
    }

    // Initialize Elementor integration
    require_once EDUCARE_RESULTS_EMAILER_PATH . 'includes/class-educare-results-emailer-elementor.php';
    Educare_Results_Emailer_Elementor::get_instance();

    // Enqueue styles
    add_action('wp_enqueue_scripts', 'educare_results_emailer_enqueue_styles');
    add_action('elementor/editor/after_enqueue_styles', 'educare_results_emailer_enqueue_editor_styles');
}
add_action('plugins_loaded', 'educare_results_emailer_init');

// Admin notice for missing Elementor
function educare_results_emailer_elementor_missing_notice() {
    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }

    $message = sprintf(
        __('Educare Results Emailer requires %1$s to be installed and activated.', 'educare-results-emailer'),
        '<strong>Elementor</strong>'
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
}

// Enqueue frontend styles
function educare_results_emailer_enqueue_styles() {
    wp_enqueue_style(
        'educare-results-emailer',
        EDUCARE_RESULTS_EMAILER_URL . 'assets/css/elementor-widgets.css',
        [],
        EDUCARE_RESULTS_EMAILER_VERSION
    );
}

// Enqueue editor styles
function educare_results_emailer_enqueue_editor_styles() {
    wp_enqueue_style(
        'educare-results-emailer-editor',
        EDUCARE_RESULTS_EMAILER_URL . 'assets/css/elementor-widgets.css',
        [],
        EDUCARE_RESULTS_EMAILER_VERSION
    );
}

// Add student_id query var
function educare_results_emailer_add_query_vars($vars) {
    $vars[] = 'student_id';
    return $vars;
}
add_filter('query_vars', 'educare_results_emailer_add_query_vars');

// Handle student selection (WordPress.org compliant - using user meta instead of sessions)
function educare_results_emailer_handle_student_selection() {
    if (isset($_POST['student_id']) && wp_verify_nonce($_POST['_wpnonce'], 'student_selection')) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $student_id = absint($_POST['student_id']);
            update_user_meta($user_id, 'selected_student_id', $student_id);
        }
    }
}
add_action('init', 'educare_results_emailer_handle_student_selection');

// Register activation hook
register_activation_hook(__FILE__, 'educare_results_emailer_activate');
function educare_results_emailer_activate() {
    if (!educare_results_emailer_check_dependencies()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('Educare Results Emailer requires the Educare plugin to be installed and activated.', 'educare-results-emailer'));
    }
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'educare_results_emailer_deactivate');
function educare_results_emailer_deactivate() {
    // Clean up user meta data for selected students
    delete_metadata('user', 0, 'selected_student_id', '', true);
} 