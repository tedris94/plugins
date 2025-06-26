<?php
/**
 * Settings page handler
 *
 * @package    Educare_Custom_Template
 * @subpackage Educare_Custom_Template/includes
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Settings page handler class
 */
class Educare_Custom_Template_Settings {

    /**
     * Initialize the settings page
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'), 20);
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('update_option_educare_custom_template_thumbnail', array($this, 'handle_thumbnail_update'), 10, 2);
    }

    /**
     * Add settings page to admin menu
     */
    public function add_settings_page() {
        // Add as a main menu item under Educare
        add_menu_page(
            __('Custom Template', 'educare-custom-template'),
            __('Custom Template', 'educare-custom-template'),
            'access_educare_settings',
            'educare-custom-template',
            array($this, 'render_settings_page'),
            'dashicons-welcome-write-blog',
            30
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('educare_custom_template_settings', 'educare_custom_template_thumbnail');

        add_settings_section(
            'educare_custom_template_section',
            __('Template Settings', 'educare-custom-template'),
            null,
            'educare-custom-template'
        );

        add_settings_field(
            'educare_custom_template_thumbnail',
            __('Template Thumbnail', 'educare-custom-template'),
            array($this, 'render_thumbnail_field'),
            'educare-custom-template',
            'educare_custom_template_section'
        );
    }

    /**
     * Handle thumbnail update
     */
    public function handle_thumbnail_update($old_value, $new_value) {
        if (!$new_value) {
            return;
        }

        // Get the uploaded file
        $file_path = get_attached_file($new_value);
        
        if (!$file_path || !file_exists($file_path)) {
            error_log('Educare Custom Template: File not found at ' . $file_path);
            return;
        }

        // Get file extension
        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
        if (!$file_extension) {
            $file_extension = 'jpg'; // Default to jpg if no extension found
        }

        // Create Educare template directory if it doesn't exist
        $educare_template_dir = WP_PLUGIN_DIR . '/educare/assets/img/';
        if (!file_exists($educare_template_dir)) {
            if (!wp_mkdir_p($educare_template_dir)) {
                error_log('Educare Custom Template: Failed to create directory ' . $educare_template_dir);
                return;
            }
        }

        // Copy the file to Educare's template directory with original extension
        $new_file_name = 'custom-template.' . $file_extension;
        $new_file_path = $educare_template_dir . $new_file_name;
        
        // Remove old custom template files
        $old_files = glob($educare_template_dir . 'custom-template.*');
        foreach ($old_files as $old_file) {
            unlink($old_file);
        }
        
        // Copy the new file
        if (!copy($file_path, $new_file_path)) {
            error_log('Educare Custom Template: Failed to copy file from ' . $file_path . ' to ' . $new_file_path);
            return;
        }

        // Verify the file was copied
        if (!file_exists($new_file_path)) {
            error_log('Educare Custom Template: File was not copied successfully to ' . $new_file_path);
            return;
        }

        // Update the option with the new filename
        update_option('educare_custom_template_thumbnail', $new_file_name);
        
        error_log('Educare Custom Template: Thumbnail successfully saved to ' . $new_file_path);
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook) {
        if ('toplevel_page_educare-custom-template' !== $hook) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'educare-custom-template-settings',
            EDUCARE_CUSTOM_TEMPLATE_URL . 'assets/js/settings.js',
            array('jquery'),
            EDUCARE_CUSTOM_TEMPLATE_VERSION,
            true
        );
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('access_educare_settings')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('educare_custom_template_settings');
                do_settings_sections('educare-custom-template');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render thumbnail field
     */
    public function render_thumbnail_field() {
        $thumbnail = get_option('educare_custom_template_thumbnail');
        $thumbnail_url = '';
        
        if ($thumbnail) {
            if (is_numeric($thumbnail)) {
                $thumbnail_url = wp_get_attachment_image_url($thumbnail, 'medium');
            } else {
                $thumbnail_url = EDUCARE_URL . 'assets/img/' . $thumbnail;
            }
        }
        ?>
        <div class="thumbnail-preview">
            <?php if ($thumbnail_url) : ?>
                <img src="<?php echo esc_url($thumbnail_url); ?>" alt="Template Thumbnail" style="max-width: 300px;">
            <?php endif; ?>
        </div>
        <input type="hidden" name="educare_custom_template_thumbnail" id="educare_custom_template_thumbnail" value="<?php echo esc_attr($thumbnail); ?>">
        <button type="button" class="button" id="upload_thumbnail"><?php _e('Upload Thumbnail', 'educare-custom-template'); ?></button>
        <button type="button" class="button" id="remove_thumbnail" <?php echo !$thumbnail ? 'style="display:none;"' : ''; ?>><?php _e('Remove Thumbnail', 'educare-custom-template'); ?></button>
        <p class="description"><?php _e('Upload a thumbnail image for your custom template. Recommended size: 300x400 pixels. The image will be saved as SVG in the Educare plugin directory.', 'educare-custom-template'); ?></p>
        <?php
    }
} 