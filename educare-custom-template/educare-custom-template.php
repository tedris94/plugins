<?php
/**
 * @package     Educare Custom Template
 * @version     1.0.0
 * @author      Hyperion Tech Hub (Idris Bala Usman) <u.idris@hyperiontechhub.com>
 * @copyright   GPL-2.0+
 * @link        https://github.com/tedris94/plugins/tree/main/educare-custom-template
 * @license     https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Plugin Name:     Educare Custom Template
 * Plugin URI:      https://github.com/tedris94/plugins/tree/main/educare-custom-template
 * Description:     Custom report card template addon for Educare plugin with advanced grading system and professional layout design.
 * Version:         1.0.0
 * Author:          Hyperion Tech Hub (Idris Bala Usman)
 * Author URI:      https://github.com/tedris94
 * License:         GPL-2.0+
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     educare-custom-template
 * Domain Path:     /languages
 * Requires at least: 5.0
 * Tested up to:    6.4
 * Requires PHP:    7.2
 * Requires Plugins: educare
 * Network:         false
 * 
 * Educare Custom Template is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * Educare Custom Template is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Educare Custom Template. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 * 
 * For support and inquiries, contact:
 * Website: https://hyperiontechhub.com
 * Email: info@hyperiontechhub.com
 * Author: u.idris@hyperiontechhub.com
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