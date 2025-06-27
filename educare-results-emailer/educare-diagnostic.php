<?php
/**
 * Educare Results Emailer - Diagnostic Tool
 * 
 * Run this file directly to diagnose issues on shared hosting
 * Usage: Upload this file to your WordPress root and visit it in browser
 */

// Basic WordPress environment check
if (!defined('ABSPATH')) {
    // Try to find WordPress
    $wp_load_paths = [
        __DIR__ . '/wp-load.php',
        __DIR__ . '/../wp-load.php',
        __DIR__ . '/../../wp-load.php',
        __DIR__ . '/../../../wp-load.php'
    ];
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('WordPress not found. Please place this file in your WordPress directory.');
    }
}

// Ensure we're in WordPress admin or have admin capabilities
if (!is_admin() && !current_user_can('manage_options')) {
    die('This diagnostic tool requires admin access.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Educare Results Emailer - Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status-ok { color: green; font-weight: bold; }
        .status-warning { color: orange; font-weight: bold; }
        .status-error { color: red; font-weight: bold; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .test-result { margin: 10px 0; padding: 5px; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Educare Results Emailer - Diagnostic Tool</h1>
    <p>This tool will help diagnose issues with the educare-results-emailer plugin on shared hosting.</p>
    
    <div class="section">
        <h2>1. WordPress Environment</h2>
        <?php
        echo "<div class='test-result'>";
        echo "<strong>WordPress Version:</strong> " . get_bloginfo('version') . "<br>";
        echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
        echo "<strong>Current User:</strong> " . wp_get_current_user()->user_login . "<br>";
        echo "<strong>Site URL:</strong> " . get_site_url() . "<br>";
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>2. Plugin Status</h2>
        <?php
        $plugin_file = 'educare-results-emailer/educare-results-emailer.php';
        $is_active = is_plugin_active($plugin_file);
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        
        echo "<div class='test-result'>";
        echo "<strong>Plugin Active:</strong> ";
        if ($is_active) {
            echo "<span class='status-ok'>YES</span><br>";
        } else {
            echo "<span class='status-error'>NO</span><br>";
        }
        
        echo "<strong>Plugin File Exists:</strong> ";
        if (file_exists($plugin_path)) {
            echo "<span class='status-ok'>YES</span><br>";
        } else {
            echo "<span class='status-error'>NO</span> - Path: $plugin_path<br>";
        }
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>3. File Permissions</h2>
        <?php
        $upload_dir = wp_upload_dir();
        $directories_to_check = [
            'WordPress Uploads Dir' => $upload_dir['basedir'],
            'Educare Logos Dir' => $upload_dir['basedir'] . '/educare-logos',
            'Plugin Directory' => WP_PLUGIN_DIR . '/educare-results-emailer'
        ];
        
        foreach ($directories_to_check as $name => $path) {
            echo "<div class='test-result'>";
            echo "<strong>$name:</strong> $path<br>";
            
            if (file_exists($path)) {
                echo "<span class='status-ok'>EXISTS</span> ";
                
                if (is_writable($path)) {
                    echo "<span class='status-ok'>WRITABLE</span>";
                } else {
                    echo "<span class='status-error'>NOT WRITABLE</span>";
                }
                
                echo " (Permissions: " . substr(sprintf('%o', fileperms($path)), -4) . ")";
            } else {
                echo "<span class='status-warning'>DOES NOT EXIST</span>";
                
                // Try to create it
                if (wp_mkdir_p($path)) {
                    echo " - <span class='status-ok'>CREATED SUCCESSFULLY</span>";
                } else {
                    echo " - <span class='status-error'>FAILED TO CREATE</span>";
                }
            }
            echo "<br></div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Database Options</h2>
        <?php
        $options_to_check = [
            'educare_email_school_logo',
            'educare_bulk_mail_template',
            'educare_smtp_enabled',
            'educare_smtp_host',
            'educare_smtp_port',
            'educare_smtp_from_email'
        ];
        
        echo "<div class='test-result'>";
        foreach ($options_to_check as $option) {
            $value = get_option($option);
            echo "<strong>$option:</strong> ";
            
            if ($value !== false && $value !== '') {
                echo "<span class='status-ok'>SET</span>";
                if (strlen($value) > 50) {
                    echo " (Length: " . strlen($value) . " chars)";
                } else {
                    echo " (" . esc_html($value) . ")";
                }
            } else {
                echo "<span class='status-warning'>NOT SET</span>";
            }
            echo "<br>";
        }
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>5. Test Database Write</h2>
        <?php
        $test_option = 'educare_diagnostic_test_' . time();
        $test_value = 'test_value_' . rand(1000, 9999);
        
        echo "<div class='test-result'>";
        
        // Test write
        $write_result = update_option($test_option, $test_value);
        echo "<strong>Database Write Test:</strong> ";
        if ($write_result) {
            echo "<span class='status-ok'>SUCCESS</span><br>";
        } else {
            echo "<span class='status-error'>FAILED</span><br>";
        }
        
        // Test read
        $read_value = get_option($test_option);
        echo "<strong>Database Read Test:</strong> ";
        if ($read_value === $test_value) {
            echo "<span class='status-ok'>SUCCESS</span><br>";
        } else {
            echo "<span class='status-error'>FAILED</span> (Expected: $test_value, Got: $read_value)<br>";
        }
        
        // Clean up
        delete_option($test_option);
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>6. File Upload Test</h2>
        <?php
        $upload_dir = wp_upload_dir();
        $test_dir = $upload_dir['basedir'] . '/educare-logos';
        $test_file = $test_dir . '/test_upload_' . time() . '.txt';
        
        echo "<div class='test-result'>";
        
        // Ensure directory exists
        if (!file_exists($test_dir)) {
            wp_mkdir_p($test_dir);
        }
        
        // Test file creation
        $test_content = 'Test file created at ' . date('Y-m-d H:i:s');
        $write_result = file_put_contents($test_file, $test_content);
        
        echo "<strong>File Write Test:</strong> ";
        if ($write_result !== false) {
            echo "<span class='status-ok'>SUCCESS</span><br>";
            
            // Test file read
            $read_content = file_get_contents($test_file);
            echo "<strong>File Read Test:</strong> ";
            if ($read_content === $test_content) {
                echo "<span class='status-ok'>SUCCESS</span><br>";
            } else {
                echo "<span class='status-error'>FAILED</span><br>";
            }
            
            // Clean up
            unlink($test_file);
        } else {
            echo "<span class='status-error'>FAILED</span><br>";
        }
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>7. Error Log Check</h2>
        <?php
        echo "<div class='test-result'>";
        echo "<strong>PHP Error Log:</strong> " . (ini_get('log_errors') ? 'ENABLED' : 'DISABLED') . "<br>";
        echo "<strong>Error Log File:</strong> " . (ini_get('error_log') ?: 'Default location') . "<br>";
        
        // Test error logging
        $test_message = 'Educare diagnostic test - ' . date('Y-m-d H:i:s');
        error_log($test_message);
        echo "<strong>Test Error Log:</strong> Message sent (check your error logs for: '$test_message')<br>";
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>8. Recommendations</h2>
        <?php
        $upload_dir = wp_upload_dir();
        $logos_dir = $upload_dir['basedir'] . '/educare-logos';
        
        echo "<div class='test-result'>";
        echo "<strong>For Logo Upload Issues:</strong><br>";
        echo "1. Ensure the uploads directory is writable: " . $upload_dir['basedir'] . "<br>";
        echo "2. Create the logos directory manually if needed: $logos_dir<br>";
        echo "3. Set directory permissions to 755 or 775<br><br>";
        
        echo "<strong>For Template Saving Issues:</strong><br>";
        echo "1. Check if your hosting provider limits database operations<br>";
        echo "2. Try saving shorter templates first to test<br>";
        echo "3. Contact your hosting provider about WordPress option storage limits<br><br>";
        
        echo "<strong>For General Debugging:</strong><br>";
        echo "1. Enable WordPress debug logging in wp-config.php<br>";
        echo "2. Check your hosting provider's error logs<br>";
        echo "3. Try deactivating and reactivating the plugin<br>";
        echo "</div>";
        ?>
    </div>
    
    <p><strong>Note:</strong> After running this diagnostic, you can delete this file for security.</p>
</body>
</html> 