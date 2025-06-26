<?php
/**
 * Plugin Name: Educare Custom Template
 * Plugin URI: https://hyperiontechhub.com
 * Description: Custom template addon for Educare plugin
 * Version: 1.0.0
 * Author: Idris Bala Usman
 * Text Domain: educare-custom-template
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('EDUCARE_CUSTOM_TEMPLATE_VERSION', '1.0.0');
define('EDUCARE_CUSTOM_TEMPLATE_PATH', plugin_dir_path(__FILE__));
define('EDUCARE_CUSTOM_TEMPLATE_URL', plugin_dir_url(__FILE__));

/**
 * Initialize the plugin
 */
function educare_custom_template_init() {
    // Check if Educare plugin is active
    if (!function_exists('educare_check_status')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . __('Educare Custom Template requires the Educare plugin to be installed and activated.', 'educare-custom-template') . '</p></div>';
        });
        return;
    }

    // Include required files
    require_once EDUCARE_CUSTOM_TEMPLATE_PATH . 'includes/class-educare-custom-template.php';
    require_once EDUCARE_CUSTOM_TEMPLATE_PATH . 'includes/class-educare-custom-template-settings.php';
    require_once EDUCARE_CUSTOM_TEMPLATE_PATH . 'templates/report-card-template.php';

    // Initialize classes
    new Educare_Custom_Template();
    new Educare_Custom_Template_Settings();
    
    // Debug: Add a simple test
    add_action('wp_footer', function() {
        echo '<!-- DEBUG: Educare Custom Template plugin loaded -->';
    });
}

// Hook into WordPress
add_action('plugins_loaded', 'educare_custom_template_init');

// Activation hook to register grading key setting
register_activation_hook(__FILE__, 'educare_custom_template_activate');

function educare_custom_template_activate() {
    // Add grading_key setting to Educare settings
    if (function_exists('educare_check_status')) {
        $settings = get_option('educare_settings', array());
        if (!isset($settings['grading_key'])) {
            $settings['grading_key'] = 'unchecked';
            update_option('educare_settings', $settings);
        }
    }
} 