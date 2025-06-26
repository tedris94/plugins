<?php
/**
 * Plugin Name: Educare Results Emailer
 * Plugin URI: https://hyperiontechhub.com
 * Description: Addon plugin for Educare to send student results to parents via email
 * Version: 1.0.0
 * Author: Idris Bala Usman
 * Author URI: https://github.com/tedris94
 * Text Domain: educare-results-emailer
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
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

// Handle student selection
function educare_results_emailer_handle_student_selection() {
    if (!session_id()) {
        session_start();
    }

    if (isset($_POST['student_id'])) {
        $_SESSION['selected_student_id'] = intval($_POST['student_id']);
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
    // Clean up session data
    if (session_id()) {
        session_destroy();
    }
} 