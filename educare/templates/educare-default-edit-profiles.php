<?php
function eduacre_default_edit_profiles($attr) {
  if (has_action('educare_custom_edit_profiles')) {
		return do_action('educare_custom_edit_profiles', $attr);
	} else {
    ob_start();
    ?>
    <section id="post-<?php the_ID(); ?>">
      <div class="container">
        <?php
        if ('demo' == 'no') {
          echo '<div class="bg-warning p-3 mb-5 rounded">' . __('Sorry, You can not update your profile in demo mode!', 'academia') . '</div>';
        } else {
          if (!is_user_logged_in()) {
          ?>
            <p class="warning">
              <?php echo '<div class="bg-danger text-light p-3 mb-5 rounded">' . __('You must be logged in to access your profile.', 'academia') . '</div>'; ?>
            </p><!-- .warning -->
          <?php
          } else {
            global $current_user, $wp_roles;
            // get_currentuserinfo(); //deprecated since 3.1
            // Load the registration file.
            //require_once( ABSPATH . WPINC . '/registration.php' ); //deprecated since 3.1

            $error = array();
            /* If profile was saved, update profile. */
            if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == 'update-user') {

              /* Update user password. */
              if (!empty($_POST['pass1']) && empty($_POST['pass2'])) {
                $error[] = __('<div class="bg-danger text-light p-3 mb-5 rounded">Please fill repeat password</div>', 'academia');
              } else {
                if ($_POST['pass1'] == $_POST['pass2']) {
                  wp_update_user(array('ID' => $current_user->ID, 'user_pass' => esc_attr($_POST['pass1'])));
                } else {
                  $error[] = __('<div class="bg-danger text-light p-3 mb-5 rounded">The passwords you entered do not match.  Your password was not updated.</div>', 'academia');
                }
              }

              /* Update user information. */
              if (!empty($_POST['url'])) {
                wp_update_user(array('ID' => $current_user->ID, 'user_url' => esc_url($_POST['url'])));
              }


              /*
              // Email
              if (!empty($_POST['email'])) {
                if (!is_email(esc_attr($_POST['email'])))
                  $error[] = __('<div class="bg-danger text-light p-3 mb-5 rounded">The Email you entered is not valid.  please try again.</div>', 'academia');
                elseif (email_exists(esc_attr($_POST['email'])) != $current_user->ID)
                  $error[] = __('<div class="bg-danger text-light p-3 mb-5 rounded">This email is already used by another user.  try a different one.</div>', 'academia');
                else {
                  wp_update_user(array('ID' => $current_user->ID, 'user_email' => esc_attr($_POST['email'])));
                }
              }

              // Name
              if (!empty($_POST['first-name'])) {
                // update_user_meta($current_user->ID, 'first_name', esc_attr($_POST['first-name']));
              }
              if (!empty($_POST['last-name'])) {
                // update_user_meta($current_user->ID, 'last_name', esc_attr($_POST['last-name']));
              }
              */

              update_user_meta($current_user->ID, 'description', esc_attr($_POST['description']));

              /* Redirect or show msgs so the page will show updated info.*/
              if (!$error) {
                echo '<div class="bg-success text-light p-3 mb-5 rounded">' . __('Sucessfully update your profiles', 'academia') . '</div>';
              }
            }

            ?>
            <div class="wow fadeInUp card border-0 shadow" data-wow-duration="1s" data-wow-delay=".1s">
              <div class="card-header bg-primary text-white fs-5"><a href="javascript:history.back()" class="float-start text-white me-2"><i class="fa fa-angle-left"></i></a> <?php _e('Edit Profiles', 'academia'); ?></div>

              <div class="card-body">
                <div class="entry-content">
                  <?php if (count($error) > 0) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                  <form method="post" id="updateUser" action="<?php the_permalink(); ?>">
                    <!-- <div class="row mb-3">
                      <label for="first-name" class="col-sm-2 col-form-label"><?php _e('First Name', 'academia'); ?></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="first-name" name="first-name" value="<?php the_author_meta('first_name', $current_user->ID); ?>" disabled>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="last-name" class="col-sm-2 col-form-label"><?php _e('Last Name', 'academia'); ?></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="last-name" name="last-name" value="<?php the_author_meta('last_name', $current_user->ID); ?>" disabled>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="email" class="col-sm-2 col-form-label"><?php _e('E-mail *', 'academia'); ?></label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" value="<?php the_author_meta('user_email', $current_user->ID); ?>">
                      </div>
                    </div> -->

                    <div class="row mb-3">
                      <label for="pass1" class="col-sm-2 col-form-label"><?php _e('Password *', 'academia'); ?></label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" id="pass1" name="pass1">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="pass2" class="col-sm-2 col-form-label"><?php _e('Repeat Password *', 'academia'); ?></label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" id="pass2" name="pass2">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="description" class="col-sm-2 col-form-label"><?php _e('Biographical Information', 'academia') ?></label>
                      <div class="col-sm-10">
                        <textarea class="form-control" name="description" id="description" rows="3"><?php the_author_meta('description', $current_user->ID); ?></textarea>
                      </div>
                    </div>

                    <?php
                    //action hook for plugin and extra fields
                    do_action('edit_user_profile', $current_user);
                    ?>

                    <input name="action" type="hidden" id="action" value="update-user" />
                    <?php wp_nonce_field('update-user') ?>

                    <button type="submit" class="btn btn-primary" name="updateuser"><?php _e('Update', 'academia'); ?></button>

                  </form><!-- #updateUser -->
                </div><!-- .entry-content -->
              </div>
            </div>
            <?php
          }
        }
        ?>
      </div>
    </section>
    <?php

    return ob_get_clean();
  }
}

add_shortcode('educare_edit_profiles', 'eduacre_default_edit_profiles');
?>
