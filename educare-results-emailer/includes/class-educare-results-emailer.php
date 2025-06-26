<?php

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Educare_Results_Emailer {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'educare-results-emailer';
        $this->version = EDUCARE_RESULTS_EMAILER_VERSION;
        
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        require_once EDUCARE_RESULTS_EMAILER_PLUGIN_DIR . 'includes/class-educare-results-emailer-loader.php';
        $this->loader = new Educare_Results_Emailer_Loader();
    }

    private function define_admin_hooks() {
        $admin = new Educare_Results_Emailer_Admin($this->get_plugin_name(), $this->get_version());
        
        // Add menu items
        $this->loader->add_action('admin_menu', $admin, 'add_plugin_admin_menu');
        
        // Add AJAX handlers
        $this->loader->add_action('wp_ajax_send_single_result_email', $admin, 'send_single_result_email');
        $this->loader->add_action('wp_ajax_send_bulk_result_emails', $admin, 'send_bulk_result_emails');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
} 