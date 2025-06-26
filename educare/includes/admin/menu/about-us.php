<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * ### About Educare
 * 
 * Educare is a powerful online School/College students & results management system dev by FixBD. This plugin allows you to manage and publish students results. This is a School/College students & results management plugin that was created to make WordPress a more powerful CMS.
 * 
 * @since 1.0.0
 * @last-update 1.4.0
 */

//  $user_id = get_current_user_id(); // Replace with the user ID of the user from whom you want to remove capabilities.
	
// 	// remove settings and management from dashbord if current user is addmin for specific school
// 	$user = new WP_User($user_id);
// 	$get_school = get_user_meta($user_id, 'School', true);
  

//   echo '<pre>';	
//   print_r($user);	
//   echo '</pre>';

?>


<div class="educare-container ">
  <div class="educare_post">
    <div class="educare_post_content about">

      <div class="logo">
        <img src="<?php echo esc_url(EDUCARE_URL . 'assets/img/educare.svg'); ?>" alt="Educare" /><br>
        <?php echo esc_html('v' . EDUCARE_VERSION); ?>
      </div>

      <br>

      <h4 style="font-size: 22px; line-height: 1.4"><?php echo sprintf(__('Educare is a powerful online School, College, students & results management system dev by %s. This plugin allows you to manage and publish students results. This is a school, college, students & results management plugin that was created to easily manage institute, academy or student results at online.', 'educare'), '<a href="http://fixbd.net" target="_blank"><img src="' . esc_url(EDUCARE_URL . 'assets/img/fixbd.svg') . '" width="50px" alt="fixbd" /></a>') ?></h4>

      <p><?php _e('Educare help you to easily control over your institute students at online. You can easily Add/Edit/Delete Teachers, Students, Results, Class, Group, Exam, Rating Scale, Year, Extra Field, Custom Result Rules, Auto result calculations and much more… Also you can add marks, promote or import & export unlimited students and results just one click!', 'educare'); ?></p>

      <hr>

      <div class="select">
        <div class="logo">
          <img src="<?php echo esc_url(EDUCARE_URL . 'assets/img/marks.svg'); ?>" alt="Vision" />
        </div>

        <div>
          <h4><?php _e('Our Vision', 'educare'); ?></h4>
          <p><?php _e('We are committed to adjust your results system with Educare. Because, we believe in freedom and understand the value of your project. So, get in touch and help us deliver your project!', 'educare'); ?></p>
        </div>
      </div>

      <div class="select">
        <div class="logo">
          <img src="<?php echo esc_url(EDUCARE_URL . 'assets/img/achivement.svg'); ?>" alt="Vision" />
        </div>

        <div>
          <h4><?php _e('Our Mission', 'educare'); ?></h4>
          <p><?php _e('Our mission is to build a great software that will reform education. Our future plan is to make Educare a fully virtual school.', 'educare'); ?></p>
        </div>
      </div>

      <br>
      <hr>
      <br>

      <p>
        <b>Name:</b> Educare <br>
        <b>Version:</b> <?php echo esc_html(EDUCARE_VERSION); ?> (Premium)<br>
        <b>Settings Version:</b> <?php echo esc_html(EDUCARE_SETTINGS_VERSION); ?> <br>
        <b>Results Version:</b> <?php echo esc_html(EDUCARE_RESULTS_VERSION); ?> <br>
        <b>Changelog:</b> The change log is located in the <strong>`changelog.md`</strong> file in the plugin folder. You may also <a href="https://github.com/FixBD/Educare/blob/FixBD/changelog.md" target="_blank">View The Change Logs</a> at online.
      </p>

      <p>If you're a theme author, plugin author, or just a code hobbyist, you can follow this <a href="http://github.com/fixbd/educare" target="_blank">DEVELOPMENT INTRODUCTIONS</a> on GitHub Repositories.
        For more info, you can visit FixBD on GitHub</p>

      <p>If you have face any problems and need our support (Totally Free!), Please contact us with this email:<br>
        <a href="mailto:fixbd.org@gmail.com">fixbd.org@gmail.com</a>
      </p>

      <p>The educare plugin is a massive project with lot’s of code to maintain. A major update can take weeks or months of work. We don’t make any money from free version users, We glad to say that, lot's of Educare (PREMIUM) features is completely free of charge!. So, no money will be required to install or update free vesrion of Educare. Please share your experience (feedback) while using educare to improve Educare.</p>

      <p>Educare support forum: <br>
        <a href="https://wordpress.org/support/plugin/educare" target="_blank">https://wordpress.org/support/plugin/educare</a> <br>
        Also, You can send your feedback here:<br>
        <a href="https://wordpress.org/plugins/educare/#reviews" target="_blank">https://wordpress.org/plugins/educare/#reviews</a>
      </p>

      <p>
        <a href="http://github.com/fixbd" target="_blank"><img src="<?php echo esc_url(EDUCARE_URL . 'assets/img/fixbd.svg'); ?>" width="100px" alt="fixbd" /></a>
      </p>
    </div>
  </div>
</div>