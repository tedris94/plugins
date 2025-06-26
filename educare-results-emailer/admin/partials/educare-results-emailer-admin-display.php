<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap" style="max-width: 100% !important; margin: 0 !important; padding: 20px !important; box-sizing: border-box;">
    <h1 style="margin-bottom: 30px;"><?php echo esc_html(get_admin_page_title()); ?></h1>

    <!-- PDF Generation Section -->
    <div class="card">
        <h2>📄 PDF Generation</h2>
        <p>Generate PDFs for students before sending emails. PDFs will be stored in the <code>/wp-content/uploads/educare-student-pdfs/</code> folder.</p>
        
        <div class="pdf-generation-controls">
            <h3>Generate PDFs</h3>
            <div class="form-group">
                <label for="pdf_class">Class:</label>
                <select id="pdf_class" name="pdf_class">
                    <option value="">Select Class</option>
                    <?php
                    global $wpdb;
                    $table = $wpdb->prefix . 'educare_results';
                    $classes = $wpdb->get_col("SELECT DISTINCT Class FROM $table ORDER BY Class");
                    foreach ($classes as $class) {
                        echo '<option value="' . esc_attr($class) . '">' . esc_html($class) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="pdf_year">Year:</label>
                <select id="pdf_year" name="pdf_year">
                    <option value="">Select Year</option>
                    <?php
                    $years = $wpdb->get_col("SELECT DISTINCT Year FROM $table ORDER BY Year DESC");
                    foreach ($years as $year) {
                        echo '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="pdf_exam">Exam:</label>
                <select id="pdf_exam" name="pdf_exam">
                    <option value="">Select Exam</option>
                    <?php
                    $exams = $wpdb->get_col("SELECT DISTINCT Exam FROM $table ORDER BY Exam");
                    foreach ($exams as $exam) {
                        echo '<option value="' . esc_attr($exam) . '">' . esc_html($exam) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="pdf_group">Group (Optional):</label>
                <select id="pdf_group" name="pdf_group">
                    <option value="">All Groups</option>
                    <?php
                    $groups = $wpdb->get_col("SELECT DISTINCT `Group` FROM $table WHERE `Group` IS NOT NULL AND `Group` != '' ORDER BY `Group`");
                    foreach ($groups as $group) {
                        echo '<option value="' . esc_attr($group) . '">' . esc_html($group) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <button type="button" id="load_pdf_students" class="button button-secondary">Load Students</button>
            <button type="button" id="generate_pdfs" class="button button-primary" style="display: none;">Generate PDFs</button>
        </div>
        
        <div id="pdf_students_list" style="display: none;">
            <h3>Students for PDF Generation</h3>
            <div id="pdf_students_table"></div>
        </div>
        
        <div id="pdf_status" style="display: none;">
            <h3>PDF Status</h3>
            <div id="pdf_status_content"></div>
        </div>
    </div>

    <!-- Single Email Section -->
    <div class="card">
        <h2>📧 Send Single Result Email</h2>
        <p>Send a result email to a single student's parent.</p>
        
        <div class="single-email-controls">
            <div class="form-group">
                <label for="single_student">Select Student:</label>
                <select id="single_student" name="single_student">
                    <option value="">Select Student</option>
                    <?php
                    $students = $wpdb->get_results("SELECT id, Name, Roll_No, Class, Year, Exam FROM $table ORDER BY Name");
                    foreach ($students as $student) {
                        $display_name = $student->Name . ' (' . $student->Roll_No . ') - ' . $student->Class . ' - ' . $student->Exam;
                        echo '<option value="' . esc_attr($student->id) . '">' . esc_html($display_name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="parent_email">Parent Email:</label>
                <input type="email" id="parent_email" name="parent_email" placeholder="Enter parent email address" />
            </div>
            
            <button type="button" id="send_single_email" class="button button-primary">Send Single Email</button>
            <button type="button" id="preview_result" class="button button-secondary">Preview Result</button>
        </div>
        
        <div id="single_email_status" style="display: none;">
            <h3>Email Status</h3>
            <div id="single_email_status_content"></div>
        </div>
    </div>
    
    <!-- Email Sending Section -->
    <div class="card">
        <h2>📧 Send Results via Email</h2>
        <p>Send result PDFs to parents via email. Make sure PDFs are generated first.</p>
        
        <div class="email-controls">
            <h3>Select Students for Email</h3>
            <div class="form-group">
                <label for="email_class">Class:</label>
                <select id="email_class" name="email_class">
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class) { ?>
                        <option value="<?php echo esc_attr($class); ?>"><?php echo esc_html($class); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="email_year">Year:</label>
                <select id="email_year" name="email_year">
                    <option value="">Select Year</option>
                    <?php foreach ($years as $year) { ?>
                        <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="email_exam">Exam:</label>
                <select id="email_exam" name="email_exam">
                    <option value="">Select Exam</option>
                    <?php foreach ($exams as $exam) { ?>
                        <option value="<?php echo esc_attr($exam); ?>"><?php echo esc_html($exam); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="email_group">Group (Optional):</label>
                <select id="email_group" name="email_group">
                    <option value="">All Groups</option>
                    <?php foreach ($groups as $group) { ?>
                        <option value="<?php echo esc_attr($group); ?>"><?php echo esc_html($group); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <button type="button" id="load_email_students" class="button button-secondary">Load Students</button>
            <button type="button" id="send_bulk_emails" class="button button-primary" style="display: none;">Send Bulk Emails</button>
        </div>
        
        <div id="email_students_list" style="display: none;">
            <h3>Students for Email</h3>
            <div id="email_students_table"></div>
        </div>
    </div>

    <!-- Logo Settings Section -->
    <div class="card">
        <h2>🖼️ Logo Settings</h2>
        <p>Upload and manage school logo for email templates.</p>
        
        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field('educare_logo_settings', 'logo_settings_nonce'); ?>
            
            <div class="form-group">
                <label for="school_logo">School Logo:</label>
                <input type="file" name="school_logo" id="school_logo" accept="image/*" />
                <p class="description">Upload a logo image (PNG, JPG, GIF). Recommended size: 200x100 pixels.</p>
            </div>
            
            <?php
            $current_logo = get_option('educare_email_school_logo');
            if ($current_logo) {
                echo '<div class="current-logo">';
                echo '<p><strong>Current Logo:</strong></p>';
                echo '<img src="' . esc_url($current_logo) . '" alt="Current Logo" style="max-width: 200px; max-height: 100px;" />';
                echo '</div>';
            }
            ?>
            
            <p class="submit">
                <input type="submit" name="save_logo_settings" class="button button-primary" value="Upload Logo" />
                <?php if ($current_logo) { ?>
                    <button type="submit" name="remove_logo" class="button button-secondary">Remove Logo</button>
                <?php } ?>
            </p>
        </form>
    </div>

    <!-- SMTP Settings Section - Super Admin and Full Access Roles Only -->
    <?php 
    // Check if user has SMTP access (full access only, not view-only)
    $user_has_smtp_access = false;
    if (is_super_admin()) {
        $user_has_smtp_access = true;
    } else {
        $allowed_roles = get_option('educare_allowed_roles', array());
        if (!empty($allowed_roles)) {
            $current_user = wp_get_current_user();
            if ($current_user && $current_user->exists()) {
                foreach ($allowed_roles as $role) {
                    if (in_array($role, $current_user->roles)) {
                        $user_has_smtp_access = true;
                        break;
                    }
                }
            }
        }
    }
    ?>
    <?php if ($user_has_smtp_access) : ?>
    <div class="card">
        <h2>⚙️ SMTP Settings</h2>
        <?php if (is_super_admin()) : ?>
            <p class="description" style="color: #d63638; font-weight: bold;">🔒 Super Admin: These settings control global email configuration.</p>
        <?php else : ?>
            <p class="description" style="color: #2271b1; font-weight: bold;">✅ Role Access: You have been granted access to modify SMTP settings.</p>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field('educare_smtp_settings', 'smtp_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable SMTP</th>
                    <td>
                        <input type="checkbox" name="smtp_enabled" value="1" <?php checked(get_option('educare_smtp_enabled')); ?> />
                        <p class="description">Enable custom SMTP settings</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">SMTP Host</th>
                    <td>
                        <input type="text" name="smtp_host" value="<?php echo esc_attr(get_option('educare_smtp_host', 'smtp-relay.brevo.com')); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">SMTP Port</th>
                    <td>
                        <input type="text" name="smtp_port" value="<?php echo esc_attr(get_option('educare_smtp_port', '587')); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">Encryption</th>
                    <td>
                        <select name="smtp_encryption">
                            <option value="tls" <?php selected(get_option('educare_smtp_encryption'), 'tls'); ?>>TLS</option>
                            <option value="ssl" <?php selected(get_option('educare_smtp_encryption'), 'ssl'); ?>>SSL</option>
                            <option value="" <?php selected(get_option('educare_smtp_encryption'), ''); ?>>None</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">SMTP Username</th>
                    <td>
                        <input type="text" name="smtp_username" value="<?php echo esc_attr(get_option('educare_smtp_username', '8f31f7001@smtp-brevo.com')); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">SMTP Password</th>
                    <td>
                        <input type="password" name="smtp_password" value="<?php echo esc_attr(get_option('educare_smtp_password')); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">From Email</th>
                    <td>
                        <input type="email" name="smtp_from_email" value="<?php echo esc_attr(get_option('educare_smtp_from_email', 'admin@bos.hyperiontechhub.com')); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">From Name</th>
                    <td>
                        <input type="text" name="smtp_from_name" value="<?php echo esc_attr(get_option('educare_smtp_from_name', 'Bright Olivelight Schools')); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="save_smtp_settings" class="button button-primary" value="Save SMTP Settings" />
                <button type="button" id="test_smtp" class="button button-secondary">Test SMTP Connection</button>
            </p>
        </form>
    </div>
    <?php else : ?>
    <div class="card">
        <h2>⚙️ SMTP Settings</h2>
        <div class="notice notice-info">
            <p><strong>🔒 Access Restricted:</strong> SMTP settings are only accessible to Super Administrators and users with <strong>Full Access</strong> roles to prevent unauthorized changes to email configuration.</p>
            <p>Current SMTP Status: <?php echo get_option('educare_smtp_enabled') ? '<span style="color: green;">✅ Enabled</span>' : '<span style="color: red;">❌ Disabled</span>'; ?></p>
            <?php
            $allowed_roles = get_option('educare_allowed_roles', array());
            $view_only_roles = get_option('educare_view_only_roles', array());
            $wp_roles = wp_roles();
            $all_roles = $wp_roles->get_names();
            
            if (!empty($allowed_roles)) {
                $role_names = array();
                foreach ($allowed_roles as $role) {
                    if (isset($all_roles[$role])) {
                        $role_names[] = $all_roles[$role];
                    }
                }
                if (!empty($role_names)) {
                    echo '<p><strong>Roles with Full Access:</strong> ' . implode(', ', $role_names) . '</p>';
                }
            }
            
            // Check if current user has view-only access
            $current_user = wp_get_current_user();
            $user_has_view_only = false;
            if ($current_user && $current_user->exists()) {
                foreach ($view_only_roles as $role) {
                    if (in_array($role, $current_user->roles)) {
                        $user_has_view_only = true;
                        break;
                    }
                }
            }
            
            if ($user_has_view_only) {
                echo '<p><strong>Your Access Level:</strong> <span style="color: blue;">👁️ View-Only Access</span> - You can send emails but cannot modify SMTP settings.</p>';
            }
            ?>
            <p>If you need to modify SMTP settings, please contact your system administrator to assign Full Access role.</p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Role Assignment Section - Super Admin Only -->
    <?php if (is_super_admin()) : ?>
    <div class="card">
        <h2>👥 Role Assignment & Access Control</h2>
        <p class="description" style="color: #d63638; font-weight: bold;">🔒 Super Admin Only: Configure role-based access to plugin features.</p>
        <form method="post" action="">
            <?php wp_nonce_field('educare_role_assignment', 'role_assignment_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">SMTP Full Access Roles</th>
                    <td>
                        <?php
                        $allowed_roles = get_option('educare_allowed_roles', array());
                        $wp_roles = wp_roles();
                        $all_roles = $wp_roles->get_names();
                        
                        // Remove super admin role as it's always allowed
                        unset($all_roles['super_admin']);
                        ?>
                        <fieldset>
                            <legend class="screen-reader-text">Select roles that can access SMTP settings</legend>
                            <?php foreach ($all_roles as $role_key => $role_name) : ?>
                                <label>
                                    <input type="checkbox" name="allowed_roles[]" value="<?php echo esc_attr($role_key); ?>" 
                                           <?php checked(in_array($role_key, $allowed_roles)); ?> />
                                    <?php echo esc_html($role_name); ?>
                                </label><br>
                            <?php endforeach; ?>
                            <p class="description">
                                <strong>Full Access:</strong> These roles can view and modify SMTP settings, send emails, and access all plugin features.
                                Super Administrator always has full access.
                            </p>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">View-Only Access Roles</th>
                    <td>
                        <?php
                        $view_only_roles = get_option('educare_view_only_roles', array());
                        ?>
                        <fieldset>
                            <legend class="screen-reader-text">Select roles that can view plugin but not SMTP settings</legend>
                            <?php foreach ($all_roles as $role_key => $role_name) : ?>
                                <label>
                                    <input type="checkbox" name="view_only_roles[]" value="<?php echo esc_attr($role_key); ?>" 
                                           <?php checked(in_array($role_key, $view_only_roles)); ?> />
                                    <?php echo esc_html($role_name); ?>
                                </label><br>
                            <?php endforeach; ?>
                            <p class="description">
                                <strong>View-Only Access:</strong> These roles can access the plugin page and send emails but cannot view or modify SMTP settings.
                                Perfect for teachers or staff who need to send results but shouldn't change email configuration.
                            </p>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Current Access Status</th>
                    <td>
                        <div class="role-access-status">
                            <h4>Roles with Access:</h4>
                            <ul>
                                <li><strong>Super Administrator</strong> - <span style="color: green;">✅ Full Access (Always)</span></li>
                                <?php if (!empty($allowed_roles)) : ?>
                                    <?php foreach ($allowed_roles as $role) : ?>
                                        <?php if (isset($all_roles[$role])) : ?>
                                            <li><strong><?php echo esc_html($all_roles[$role]); ?></strong> - <span style="color: green;">✅ Full Access (SMTP + Email)</span></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (!empty($view_only_roles)) : ?>
                                    <?php foreach ($view_only_roles as $role) : ?>
                                        <?php if (isset($all_roles[$role])) : ?>
                                            <li><strong><?php echo esc_html($all_roles[$role]); ?></strong> - <span style="color: blue;">👁️ View-Only Access (Email Only)</span></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (empty($allowed_roles) && empty($view_only_roles)) : ?>
                                    <li><em>No additional roles assigned</em></li>
                                <?php endif; ?>
                            </ul>
                            
                            <h4>Users with Current Access:</h4>
                            <div class="users-with-access">
                                <?php
                                $users_with_access = array();
                                
                                // Get super admins
                                if (is_multisite()) {
                                    $super_admins = get_super_admins();
                                    foreach ($super_admins as $super_admin) {
                                        $user = get_user_by('login', $super_admin);
                                        if ($user) {
                                            $users_with_access[] = array(
                                                'name' => $user->display_name,
                                                'email' => $user->user_email,
                                                'role' => 'Super Administrator'
                                            );
                                        }
                                    }
                                } else {
                                    // Single site - get administrators
                                    $admin_users = get_users(array('role' => 'administrator'));
                                    foreach ($admin_users as $admin) {
                                        $users_with_access[] = array(
                                            'name' => $admin->display_name,
                                            'email' => $admin->user_email,
                                            'role' => 'Administrator'
                                        );
                                    }
                                }
                                
                                // Get users with full access roles
                                if (!empty($allowed_roles)) {
                                    foreach ($allowed_roles as $role) {
                                        $role_users = get_users(array('role' => $role));
                                        foreach ($role_users as $user) {
                                            $users_with_access[] = array(
                                                'name' => $user->display_name,
                                                'email' => $user->user_email,
                                                'role' => $all_roles[$role] ?? $role,
                                                'access_type' => 'Full Access'
                                            );
                                        }
                                    }
                                }
                                
                                // Get users with view-only access roles
                                if (!empty($view_only_roles)) {
                                    foreach ($view_only_roles as $role) {
                                        $role_users = get_users(array('role' => $role));
                                        foreach ($role_users as $user) {
                                            $users_with_access[] = array(
                                                'name' => $user->display_name,
                                                'email' => $user->user_email,
                                                'role' => $all_roles[$role] ?? $role,
                                                'access_type' => 'View-Only'
                                            );
                                        }
                                    }
                                }
                                ?>
                                
                                <?php if (!empty($users_with_access)) : ?>
                                    <table class="wp-list-table widefat fixed striped" style="max-width: 700px;">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Access Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users_with_access as $user) : ?>
                                                <tr>
                                                    <td><?php echo esc_html($user['name']); ?></td>
                                                    <td><?php echo esc_html($user['email']); ?></td>
                                                    <td><?php echo esc_html($user['role']); ?></td>
                                                    <td>
                                                        <?php if (isset($user['access_type'])) : ?>
                                                            <span style="color: <?php echo $user['access_type'] === 'Full Access' ? 'green' : 'blue'; ?>;">
                                                                <?php echo $user['access_type'] === 'Full Access' ? '✅' : '👁️'; ?> <?php echo esc_html($user['access_type']); ?>
                                                            </span>
                                                        <?php else : ?>
                                                            <span style="color: green;">✅ Full Access</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else : ?>
                                    <p><em>No users found with access.</em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="save_role_assignment" class="button button-primary" value="Save Role Assignment" />
                <button type="button" class="button button-secondary" onclick="if(confirm('Are you sure you want to reset role assignments? This will remove all custom role access.')) { document.getElementById('reset_roles').value = '1'; this.form.submit(); }">Reset to Default</button>
                <input type="hidden" name="reset_roles" id="reset_roles" value="" />
            </p>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- Email Template Section -->
    <div class="card">
        <h2>📝 Email Template</h2>
        <p>Customize the email template that will be sent to parents. You can use HTML and the available placeholders.</p>
        
        <div class="email-template-container">
            <div class="template-editor-section">
                <h3>Template Editor</h3>
                <form method="post" action="">
                    <?php wp_nonce_field('educare_bulk_mail_template', 'bulk_mail_template_nonce'); ?>
                    <div class="form-group">
                        <label for="bulk_mail_template">Email Template:</label>
                        <div class="editor-toolbar">
                            <!-- Text Formatting -->
                            <div class="toolbar-group">
                                <button type="button" class="button button-small" onclick="formatText('bold')" title="Bold"><strong>B</strong></button>
                                <button type="button" class="button button-small" onclick="formatText('italic')" title="Italic"><em>I</em></button>
                                <button type="button" class="button button-small" onclick="formatText('underline')" title="Underline"><u>U</u></button>
                            </div>
                            
                            <!-- Text Alignment -->
                            <div class="toolbar-group">
                                <button type="button" class="button button-small" onclick="formatText('alignLeft')" title="Align Left">⫷</button>
                                <button type="button" class="button button-small" onclick="formatText('alignCenter')" title="Align Center">⫸</button>
                                <button type="button" class="button button-small" onclick="formatText('alignRight')" title="Align Right">⫹</button>
                            </div>
                            
                            <!-- Lists -->
                            <div class="toolbar-group">
                                <button type="button" class="button button-small" onclick="formatText('insertUnorderedList')" title="Bullet List">•</button>
                                <button type="button" class="button button-small" onclick="formatText('insertOrderedList')" title="Numbered List">1.</button>
                            </div>
                            
                            <!-- Headings -->
                            <div class="toolbar-group">
                                <select onchange="formatText('formatBlock', this.value)" class="heading-select">
                                    <option value="">Heading</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                    <option value="h4">Heading 4</option>
                                    <option value="p">Paragraph</option>
                                </select>
                            </div>
                            
                            <!-- Links -->
                            <div class="toolbar-group">
                                <button type="button" class="button button-small" onclick="insertLink()" title="Insert Link">🔗</button>
                            </div>
                            
                            <!-- Separator -->
                            <div class="toolbar-separator"></div>
                            
                            <!-- Placeholders -->
                            <div class="toolbar-group">
                                <span class="placeholder-label">Placeholders:</span>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('student_name')">Student Name</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('class')">Class</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('year')">Year</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('exam')">Exam</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('message')">Message</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('school_name')">School Name</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('school_logo')">School Logo</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('date')">Date</button>
                                <button type="button" class="button button-small" onclick="insertPlaceholder('parent_name')">Parent Name</button>
                            </div>
                        </div>
                        <div id="rich_editor" class="rich-editor" contenteditable="true"><?php echo wp_kses_post(get_option('educare_bulk_mail_template')); ?></div>
                        <textarea name="bulk_mail_template" id="bulk_mail_template" style="display: none;"><?php echo esc_textarea(get_option('educare_bulk_mail_template')); ?></textarea>
                    </div>
                    <div class="template-actions">
                        <button type="button" id="preview_template" class="button button-secondary">Preview Template</button>
                        <input type="submit" name="save_bulk_mail_template" class="button button-primary" value="Save Template" />
                        <button type="button" id="reset_template" class="button button-secondary">Reset to Default</button>
                    </div>
                </form>
                
                <div class="template-preview-section">
                    <h3>Template Preview</h3>
                    <div id="template_preview" class="preview-container">
                        <p class="preview-placeholder">Click "Preview Template" to see how your email will look.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="template-info-sections">
            <div class="info-section placeholders-info">
                <h3>📋 Available Placeholders</h3>
                <div class="placeholder-grid">
                    <div class="placeholder-item">
                        <code>{student_name}</code>
                        <span>Student's full name</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{class}</code>
                        <span>Student's class</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{year}</code>
                        <span>Academic year</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{exam}</code>
                        <span>Exam name/term</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{message}</code>
                        <span>Custom message content</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{school_name}</code>
                        <span>School name</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{school_logo}</code>
                        <span>School logo/header</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{date}</code>
                        <span>Current date</span>
                    </div>
                    <div class="placeholder-item">
                        <code>{parent_name}</code>
                        <span>Parent's name (if available)</span>
                    </div>
                </div>
            </div>
            
            <div class="info-section smtp-info">
                <h3>⚙️ SMTP Configuration</h3>
                <div class="smtp-status">
                    <?php
                    $smtp_enabled = get_option('educare_smtp_enabled');
                    $smtp_host = get_option('educare_smtp_host');
                    $smtp_port = get_option('educare_smtp_port');
                    $smtp_username = get_option('educare_smtp_username');
                    $smtp_from_email = get_option('educare_smtp_from_email');
                    $smtp_from_name = get_option('educare_smtp_from_name');
                    ?>
                    <div class="status-item">
                        <span class="status-label">SMTP Status:</span>
                        <span class="status-value <?php echo $smtp_enabled ? 'enabled' : 'disabled'; ?>">
                            <?php echo $smtp_enabled ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">SMTP Host:</span>
                        <span class="status-value"><?php echo esc_html($smtp_host ?: 'Not configured'); ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">SMTP Port:</span>
                        <span class="status-value"><?php echo esc_html($smtp_port ?: 'Not configured'); ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">From Email:</span>
                        <span class="status-value"><?php echo esc_html($smtp_from_email ?: 'Not configured'); ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">From Name:</span>
                        <span class="status-value"><?php echo esc_html($smtp_from_name ?: 'Not configured'); ?></span>
                    </div>
                </div>
                <p class="info-note">
                    <strong>Note:</strong> Make sure your SMTP settings are properly configured above before sending emails.
                </p>
            </div>
            
            <div class="info-section template-tips">
                <h3>💡 Template Tips</h3>
                <ul class="tips-list">
                    <li>Use HTML tags for formatting (e.g., &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;)</li>
                    <li>Keep the template professional and concise</li>
                    <li>Test your template with the preview function</li>
                    <li>Make sure to include a clear call-to-action</li>
                    <li>Use placeholders to personalize each email</li>
                    <li>Consider adding your school logo using the logo settings</li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        @media print {
            /* Hide UI elements when printing */
            .button, .close-modal, #wpadminbar, .wrap > h1, .educare-card, form {
                display: none !important;
            }
            
            /* Reset background and margins for printing */
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Make the preview modal full screen when printing */
            #result-preview-modal {
                position: static !important;
                width: 100% !important;
                height: auto !important;
                background: none !important;
            }
            
            #result-preview-modal > div {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            
            #preview-content {
                max-height: none !important;
                overflow: visible !important;
            }
            
            /* Ensure content is visible */
            * {
                visibility: visible !important;
            }
        }
        
        /* Main container - 100% width */
        .wrap {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
            box-sizing: border-box;
            width: 100% !important;
        }
        
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            width: 100% !important;
            box-sizing: border-box;
            max-width: none !important;
        }
        
        .form-group {
            margin-bottom: 15px;
            width: 100% !important;
        }
        
        .form-group label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }
        
        .form-group select, .form-group input {
            width: 200px;
        }
        
        #pdf_students_table, #email_students_table {
            margin-top: 15px;
            width: 100% !important;
        }
        
        .student-row {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            width: 100% !important;
        }
        
        .student-row:hover {
            background-color: #f9f9f9;
        }
        
        .student-checkbox {
            margin-right: 10px;
        }
        
        .student-info {
            flex-grow: 1;
        }
        
        .student-email {
            color: #666;
            font-size: 0.9em;
        }
        
        .pdf-status {
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
        
        .pdf-status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .pdf-status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .pdf-status.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Email Template Section Styles - 100% width */
        .email-template-container {
            width: 100% !important;
            margin-bottom: 30px;
        }
        
        .template-editor-section {
            width: 100% !important;
        }
        
        .template-editor-section textarea {
            width: 100% !important;
            min-height: 400px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            resize: vertical;
            box-sizing: border-box;
        }
        
        .editor-toolbar {
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        
        .toolbar-group {
            display: flex;
            gap: 2px;
            align-items: center;
            padding: 2px;
            border-right: 1px solid #ddd;
            padding-right: 10px;
        }
        
        .toolbar-group:last-child {
            border-right: none;
        }
        
        .toolbar-separator {
            width: 1px;
            height: 20px;
            background: #ddd;
            margin: 0 5px;
        }
        
        .placeholder-label {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            margin-right: 5px;
        }
        
        .editor-toolbar .button {
            margin: 0;
            font-size: 11px;
            padding: 4px 8px;
            height: auto;
            line-height: 1.2;
            min-width: 30px;
        }
        
        .heading-select {
            font-size: 11px;
            padding: 4px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: #fff;
        }
        
        .rich-editor {
            width: 100% !important;
            min-height: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            background: #fff;
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            overflow-y: auto;
            box-sizing: border-box;
            outline: none;
        }
        
        .rich-editor:focus {
            border-color: #0073aa;
            box-shadow: 0 0 0 1px #0073aa;
        }
        
        .rich-editor h1, .rich-editor h2, .rich-editor h3, .rich-editor h4 {
            margin: 10px 0 5px 0;
            color: #333;
        }
        
        .rich-editor h1 { font-size: 24px; }
        .rich-editor h2 { font-size: 20px; }
        .rich-editor h3 { font-size: 18px; }
        .rich-editor h4 { font-size: 16px; }
        
        .rich-editor p {
            margin: 0 0 10px 0;
        }
        
        .rich-editor ul, .rich-editor ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .rich-editor li {
            margin: 5px 0;
        }
        
        .rich-editor a {
            color: #0073aa;
            text-decoration: underline;
        }
        
        .rich-editor a:hover {
            color: #005a87;
        }
        
        .template-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            width: 100% !important;
        }
        
        .template-preview-section {
            margin-top: 30px;
            width: 100% !important;
        }
        
        .preview-container {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            min-height: 300px;
            background: #fff;
            overflow-y: auto;
            width: 100% !important;
            box-sizing: border-box;
        }
        
        .preview-placeholder {
            color: #666;
            font-style: italic;
            text-align: center;
            margin-top: 50px;
        }
        
        .template-info-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
            width: 100% !important;
        }
        
        .info-section {
            background: #f9f9f9;
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            padding: 20px;
            width: 100% !important;
            box-sizing: border-box;
        }
        
        .info-section h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 8px;
        }
        
        .placeholder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            width: 100% !important;
        }
        
        .placeholder-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100% !important;
            box-sizing: border-box;
        }
        
        .placeholder-item code {
            background: #f1f1f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: #d63384;
            font-weight: bold;
        }
        
        .placeholder-item span {
            font-size: 13px;
            color: #666;
        }
        
        .smtp-status {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            width: 100% !important;
            box-sizing: border-box;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            width: 100% !important;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-label {
            font-weight: bold;
            color: #333;
        }
        
        .status-value {
            font-family: monospace;
            font-size: 12px;
        }
        
        .status-value.enabled {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-value.disabled {
            color: #dc3545;
            font-weight: bold;
        }
        
        .info-note {
            margin-top: 15px;
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            color: #856404;
            font-size: 13px;
            width: 100% !important;
            box-sizing: border-box;
        }
        
        .tips-list {
            margin: 0;
            padding-left: 20px;
            width: 100% !important;
        }
        
        .tips-list li {
            margin-bottom: 8px;
            color: #555;
            line-height: 1.4;
        }
        
        /* Rich Text Editor Enhancements */
        .template-editor-section .form-group {
            margin-bottom: 20px;
            width: 100% !important;
        }
        
        .template-editor-section label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        /* Form table styles for 100% width */
        .form-table {
            width: 100% !important;
        }
        
        .form-table th {
            width: 200px;
        }
        
        .form-table td {
            width: calc(100% - 200px);
        }
        
        .form-table input[type="text"],
        .form-table input[type="email"],
        .form-table input[type="password"],
        .form-table select {
            width: 100% !important;
            max-width: 400px;
        }
        
        /* Override any WordPress admin styles that might limit width */
        .wp-admin .wrap {
            max-width: none !important;
            width: 100% !important;
        }
        
        .wp-admin .card {
            max-width: none !important;
            width: 100% !important;
        }
        
        /* Ensure all divs use full width */
        div {
            max-width: none !important;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .template-info-sections {
                grid-template-columns: 1fr;
            }
            
            .placeholder-grid {
                grid-template-columns: 1fr;
            }
            
            .form-table th,
            .form-table td {
                display: block;
                width: 100% !important;
            }
            
            .form-table th {
                padding-bottom: 5px;
            }
            
            .editor-toolbar {
                flex-direction: column;
            }
            
            .editor-toolbar .button {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>

    <script>
        // Function to insert placeholders into the rich editor
        function insertPlaceholder(placeholder) {
            var editor = document.getElementById('rich_editor');
            var placeholderText = '{' + placeholder + '}';
            
            // Insert placeholder at cursor position
            insertTextAtCursor(editor, placeholderText);
            
            // Sync with hidden textarea
            syncTextarea();
        }
        
        // Function to format text in rich editor
        function formatText(command, value) {
            var editor = document.getElementById('rich_editor');
            
            // Focus the editor first
            editor.focus();
            
            // Execute the command
            if (value) {
                document.execCommand(command, false, value);
            } else {
                document.execCommand(command, false, null);
            }
            
            // Sync with hidden textarea
            syncTextarea();
        }
        
        // Function to insert link
        function insertLink() {
            var url = prompt('Enter URL:', 'http://');
            if (url) {
                var editor = document.getElementById('rich_editor');
                editor.focus();
                document.execCommand('createLink', false, url);
                syncTextarea();
            }
        }
        
        // Function to insert text at cursor position
        function insertTextAtCursor(element, text) {
            element.focus();
            
            // For contentEditable elements
            if (element.contentEditable === 'true') {
                var selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    var range = selection.getRangeAt(0);
                    range.deleteContents();
                    var textNode = document.createTextNode(text);
                    range.insertNode(textNode);
                    range.setStartAfter(textNode);
                    range.setEndAfter(textNode);
                    selection.removeAllRanges();
                    selection.addRange(range);
                } else {
                    // Fallback: append to end
                    element.innerHTML += text;
                }
            } else if (document.selection) {
                // IE for textarea/input
                var sel = document.selection.createRange();
                sel.text = text;
            } else if (element.selectionStart !== undefined) {
                // Modern browsers for textarea/input
                var start = element.selectionStart;
                var end = element.selectionEnd;
                var content = element.value;
                element.value = content.substring(0, start) + text + content.substring(end);
                element.selectionStart = element.selectionEnd = start + text.length;
            } else {
                // Fallback
                if (element.tagName.toLowerCase() === 'textarea' || element.tagName.toLowerCase() === 'input') {
                    element.value += text;
                } else {
                    element.innerHTML += text;
                }
            }
        }
        
        // Function to sync rich editor content with hidden textarea
        function syncTextarea() {
            var editor = document.getElementById('rich_editor');
            var textarea = document.getElementById('bulk_mail_template');
            textarea.value = editor.innerHTML;
        }
        
        // Function to sync textarea with rich editor (for form submission)
        function syncEditor() {
            var editor = document.getElementById('rich_editor');
            var textarea = document.getElementById('bulk_mail_template');
            textarea.value = editor.innerHTML;
        }

        jQuery(document).ready(function($) {
            // PDF Generation
            $('#load_pdf_students').click(function() {
                var class_val = $('#pdf_class').val();
                var year_val = $('#pdf_year').val();
                var exam_val = $('#pdf_exam').val();
                var group_val = $('#pdf_group').val();
                
                if (!class_val || !year_val || !exam_val) {
                    alert('Please select Class, Year, and Exam');
                    return;
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_get_students',
                        class: class_val,
                        year: year_val,
                        exam: exam_val,
                        group: group_val,
                        nonce: '<?php echo wp_create_nonce('educare_get_students'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Students loaded successfully:', response.data);
                            displayPdfStudents(response.data);
                            $('#generate_pdfs').show();
                        } else {
                            console.error('Error loading students:', response.data);
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        console.error('Response:', xhr.responseText);
                        alert('Error loading students');
                    }
                });
            });
            
            $('#generate_pdfs').click(function() {
                var selectedStudents = [];
                $('.pdf-student-checkbox:checked').each(function() {
                    selectedStudents.push($(this).val());
                });
                
                if (selectedStudents.length === 0) {
                    alert('Please select at least one student');
                    return;
                }
                
                $(this).prop('disabled', true).text('Generating PDFs...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_generate_bulk_pdfs',
                        student_ids: selectedStudents,
                        nonce: '<?php echo wp_create_nonce('educare_generate_pdf'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            loadPdfStatus();
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error generating PDFs');
                    },
                    complete: function() {
                        $('#generate_pdfs').prop('disabled', false).text('Generate PDFs');
                    }
                });
            });
            
            // Email Sending
            $('#load_email_students').click(function() {
                var class_val = $('#email_class').val();
                var year_val = $('#email_year').val();
                var exam_val = $('#email_exam').val();
                var group_val = $('#email_group').val();
                
                if (!class_val || !year_val || !exam_val) {
                    alert('Please select Class, Year, and Exam');
                    return;
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_get_students',
                        class: class_val,
                        year: year_val,
                        exam: exam_val,
                        group: group_val,
                        nonce: '<?php echo wp_create_nonce('educare_get_students'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            displayEmailStudents(response.data);
                            $('#send_bulk_emails').show();
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error loading students');
                    }
                });
            });
            
            $('#send_bulk_emails').click(function() {
                var selectedStudents = [];
                $('.email-student-checkbox:checked').each(function() {
                    selectedStudents.push($(this).val());
                });
                
                if (selectedStudents.length === 0) {
                    alert('Please select at least one student');
                    return;
                }
                
                if (!confirm('Are you sure you want to send emails to ' + selectedStudents.length + ' students?')) {
                    return;
                }
                
                $(this).prop('disabled', true).text('Sending Emails...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_send_bulk_mail',
                        student_ids: selectedStudents,
                        nonce: '<?php echo wp_create_nonce('educare_bulk_mail'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error sending emails');
                    },
                    complete: function() {
                        $('#send_bulk_emails').prop('disabled', false).text('Send Bulk Emails');
                    }
                });
            });
            
            // SMTP Test
            $('#test_smtp').click(function() {
                var smtpData = {
                    action: 'educare_test_smtp',
                    smtp_host: $('input[name="smtp_host"]').val(),
                    smtp_port: $('input[name="smtp_port"]').val(),
                    smtp_encryption: $('select[name="smtp_encryption"]').val(),
                    smtp_username: $('input[name="smtp_username"]').val(),
                    smtp_password: $('input[name="smtp_password"]').val(),
                    smtp_from_email: $('input[name="smtp_from_email"]').val(),
                    smtp_from_name: $('input[name="smtp_from_name"]').val(),
                    nonce: '<?php echo wp_create_nonce('educare_test_smtp'); ?>'
                };
                
                $(this).prop('disabled', true).text('Testing...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: smtpData,
                    success: function(response) {
                        if (response.success) {
                            alert('SMTP test successful!');
                        } else {
                            alert('SMTP test failed: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error testing SMTP');
                    },
                    complete: function() {
                        $('#test_smtp').prop('disabled', false).text('Test SMTP Connection');
                    }
                });
            });
            
            // Load PDF status on page load
            loadPdfStatus();
            
            // Template Preview Functionality
            $('#preview_template').click(function() {
                // Sync rich editor content to textarea first
                syncEditor();
                
                var template = $('#bulk_mail_template').val();
                if (!template.trim()) {
                    alert('Please enter some template content first.');
                    return;
                }
                
                // Create sample data for preview
                var sampleData = {
                    student_name: 'John Doe',
                    class: 'Class 10',
                    year: '2024',
                    exam: 'Mid Term',
                    message: 'Please find attached your result.',
                    school_name: 'Bright Olivelight Schools',
                    date: new Date().toLocaleDateString(),
                    parent_name: 'Mr. & Mrs. Doe'
                };
                
                // Replace placeholders with sample data
                var previewContent = template;
                Object.keys(sampleData).forEach(function(key) {
                    var placeholder = '{' + key + '}';
                    previewContent = previewContent.replace(new RegExp(placeholder, 'g'), sampleData[key]);
                });
                
                // Display preview
                $('#template_preview').html(previewContent);
            });
            
            // Reset Template Functionality
            $('#reset_template').click(function() {
                if (confirm('Are you sure you want to reset the template to default? This will replace your current template.')) {
                    var defaultTemplate = '<!DOCTYPE html>\n<html>\n<head>\n    <meta charset="UTF-8">\n    <style>\n        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n        .header { background: #f8f9fa; padding: 20px; border-bottom: 2px solid #0073aa; }\n        .content { padding: 20px; }\n        .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }\n        .highlight { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }\n    </style>\n</head>\n<body>\n    <div class="header">\n        <h2>Academic Result Notification</h2>\n        <p><strong>School:</strong> {school_name}</p>\n        <p><strong>Date:</strong> {date}</p>\n    </div>\n    \n    <div class="content">\n        <p>Dear Parent/Guardian,</p>\n        \n        <p>We are pleased to inform you that the results for <strong>{student_name}</strong> ({class}) for the {exam} examination ({year}) are now available.</p>\n        \n        <div class="highlight">\n            <p><strong>Student Details:</strong></p>\n            <ul>\n                <li><strong>Name:</strong> {student_name}</li>\n                <li><strong>Class:</strong> {class}</li>\n                <li><strong>Academic Year:</strong> {year}</li>\n                <li><strong>Examination:</strong> {exam}</li>\n            </ul>\n        </div>\n        \n        <p>{message}</p>\n        \n        <p>The detailed result card is attached to this email for your reference.</p>\n        \n        <p>If you have any questions or concerns regarding the results, please do not hesitate to contact us.</p>\n        \n        <p>Best regards,<br>\n        <strong>{school_name}</strong></p>\n    </div>\n    \n    <div class="footer">\n        <p>This is an automated message. Please do not reply to this email.</p>\n        <p>For inquiries, please contact the school administration.</p>\n    </div>\n</body>\n</html>';
                    $('#rich_editor').html(defaultTemplate);
                    syncTextarea();
                }
            });
            
            // Add event listeners for rich editor
            $('#rich_editor').on('input keyup paste', function() {
                syncTextarea();
            });
            
            // Sync on form submission
            $('form').on('submit', function() {
                syncEditor();
            });
            
            // Single Email Functionality
            $('#send_single_email').click(function() {
                var studentId = $('#single_student').val();
                var parentEmail = $('#parent_email').val();
                
                if (!studentId) {
                    alert('Please select a student');
                    return;
                }
                
                if (!parentEmail) {
                    alert('Please enter parent email address');
                    return;
                }
                
                if (!confirm('Are you sure you want to send an email to ' + parentEmail + '?')) {
                    return;
                }
                
                $(this).prop('disabled', true).text('Sending...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_send_single_email',
                        student_id: studentId,
                        parent_email: parentEmail,
                        nonce: '<?php echo wp_create_nonce('educare_single_email'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#single_email_status_content').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                            $('#single_email_status').show();
                        } else {
                            $('#single_email_status_content').html('<div class="notice notice-error"><p>Error: ' + response.data + '</p></div>');
                            $('#single_email_status').show();
                        }
                    },
                    error: function() {
                        $('#single_email_status_content').html('<div class="notice notice-error"><p>Error sending email</p></div>');
                        $('#single_email_status').show();
                    },
                    complete: function() {
                        $('#send_single_email').prop('disabled', false).text('Send Single Email');
                    }
                });
            });
            
            $('#preview_result').click(function() {
                var studentId = $('#single_student').val();
                
                if (!studentId) {
                    alert('Please select a student');
                    return;
                }
                
                // Open preview in new window
                var previewUrl = '<?php echo admin_url('admin-ajax.php'); ?>?action=educare_preview_result&student_id=' + studentId + '&nonce=<?php echo wp_create_nonce('educare_preview_result'); ?>';
                window.open(previewUrl, '_blank', 'width=800,height=600');
            });
            
            // Update parent email when student is selected
            $('#single_student').change(function() {
                var studentId = $(this).val();
                if (studentId) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'educare_get_student_email',
                            student_id: studentId,
                            nonce: '<?php echo wp_create_nonce('educare_get_student_email'); ?>'
                        },
                        success: function(response) {
                            if (response.success && response.data.email) {
                                $('#parent_email').val(response.data.email);
                            } else {
                                // Clear the field if no email found
                                $('#parent_email').val('');
                                if (response.data) {
                                    console.log('No parent email found: ' + response.data);
                                }
                            }
                        },
                        error: function() {
                            // Clear the field on error
                            $('#parent_email').val('');
                        }
                    });
                }
            });
            
            function displayPdfStudents(students) {
                var html = '<table class="wp-list-table widefat fixed striped">';
                html += '<thead><tr><th><input type="checkbox" id="select_all_pdf"></th><th>Name</th><th>Roll No</th><th>Class</th><th>Group</th><th>Parent Email</th></tr></thead><tbody>';
                
                students.forEach(function(student) {
                    html += '<tr>';
                    html += '<td><input type="checkbox" class="pdf-student-checkbox" value="' + student.id + '"></td>';
                    html += '<td>' + student.Name + '</td>';
                    html += '<td>' + student.Roll_No + '</td>';
                    html += '<td>' + student.Class + '</td>';
                    html += '<td>' + (student.Group || '') + '</td>';
                    html += '<td>' + (student.parent_email || 'Not set') + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                $('#pdf_students_table').html(html);
                $('#pdf_students_list').show();
                
                // Select all functionality
                $('#select_all_pdf').change(function() {
                    $('.pdf-student-checkbox').prop('checked', $(this).is(':checked'));
                });
            }
            
            function displayEmailStudents(students) {
                var html = '<table class="wp-list-table widefat fixed striped">';
                html += '<thead><tr><th><input type="checkbox" id="select_all_email"></th><th>Name</th><th>Roll No</th><th>Class</th><th>Group</th><th>Parent Email</th><th>PDF Status</th></tr></thead><tbody>';
                
                students.forEach(function(student) {
                    var pdfStatus = 'Not Generated';
                    var pdfClass = 'error';
                    
                    // Check if PDF exists using the pdf_exists flag from server
                    if (student.pdf_exists) {
                        pdfStatus = 'Generated';
                        pdfClass = 'success';
                    }
                    
                    html += '<tr>';
                    html += '<td><input type="checkbox" class="email-student-checkbox" value="' + student.id + '"></td>';
                    html += '<td>' + student.Name + '</td>';
                    html += '<td>' + student.Roll_No + '</td>';
                    html += '<td>' + student.Class + '</td>';
                    html += '<td>' + (student.Group || '') + '</td>';
                    html += '<td>' + (student.parent_email || 'Not set') + '</td>';
                    html += '<td><span class="pdf-status ' + pdfClass + '">' + pdfStatus + '</span></td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                $('#email_students_table').html(html);
                $('#email_students_list').show();
                
                // Select all functionality
                $('#select_all_email').change(function() {
                    $('.email-student-checkbox').prop('checked', $(this).is(':checked'));
                });
            }
            
            function loadPdfStatus() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_get_pdf_status',
                        nonce: '<?php echo wp_create_nonce('educare_generate_pdf'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var html = '<p><strong>Total PDFs:</strong> ' + response.data.pdf_count + '</p>';
                            
                            if (response.data.pdf_files.length > 0) {
                                html += '<div style="margin-bottom: 10px;">';
                                html += '<button type="button" id="delete_all_pdfs" class="button button-danger">Delete All PDFs</button>';
                                html += '</div>';
                                
                                html += '<table class="wp-list-table widefat fixed striped">';
                                html += '<thead><tr><th>Filename</th><th>Size</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
                                
                                response.data.pdf_files.forEach(function(file) {
                                    html += '<tr>';
                                    html += '<td>' + file.filename + '</td>';
                                    html += '<td>' + (file.size / 1024).toFixed(1) + ' KB</td>';
                                    html += '<td>' + file.date + '</td>';
                                    html += '<td>';
                                    html += '<a href="' + file.url + '" target="_blank" class="button button-small">Download</a> ';
                                    html += '<button type="button" class="button button-small button-danger delete-pdf" data-filename="' + file.filename + '">Delete</button>';
                                    html += '</td>';
                                    html += '</tr>';
                                });
                                
                                html += '</tbody></table>';
                            }
                            
                            $('#pdf_status_content').html(html);
                            $('#pdf_status').show();
                            
                            // Add event listeners for delete buttons
                            $('.delete-pdf').click(function() {
                                var filename = $(this).data('filename');
                                if (confirm('Are you sure you want to delete "' + filename + '"?')) {
                                    deletePdf(filename);
                                }
                            });
                            
                            $('#delete_all_pdfs').click(function() {
                                if (confirm('Are you sure you want to delete ALL PDF files? This action cannot be undone.')) {
                                    deleteAllPdfs();
                                }
                            });
                        }
                    }
                });
            }
            
            function deletePdf(filename) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_delete_pdf',
                        filename: filename,
                        nonce: '<?php echo wp_create_nonce('educare_generate_pdf'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('PDF deleted successfully');
                            loadPdfStatus(); // Reload the status
                        } else {
                            alert('Error deleting PDF: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error deleting PDF');
                    }
                });
            }
            
            function deleteAllPdfs() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'educare_delete_all_pdfs',
                        nonce: '<?php echo wp_create_nonce('educare_generate_pdf'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Deleted ' + response.data.deleted_count + ' PDF files');
                            loadPdfStatus(); // Reload the status
                        } else {
                            alert('Error deleting PDFs: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error deleting PDFs');
                    }
                });
            }
        });
    </script>
</div> 