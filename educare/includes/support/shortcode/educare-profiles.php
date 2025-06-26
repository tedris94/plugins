<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}



function educare_show_profiles($atts) {
  $msgs = $ajaxRequest = $search_form = $control_data = $control_msgs = null;
	// Get active search form template
	$active_template = educare_check_status('profiles_template');

	if (!$active_template) {
		if (current_user_can( 'manage_options' )) {
			_e('Educare <b>Settings</b> database not found.', 'educare');
		}
		return;
	}
	
	// Check if active search form has custome requred fields
	if (function_exists($active_template)) {
		$search_form = $active_template('', true);

		if (isset($search_form['control_data']) && !empty($search_form['control_data'])) {
			$control_data = true;
		}

		if (isset($search_form['control_msgs']) && !empty($search_form['control_msgs'])) {
			$control_msgs = true;
		}
	}

	// Search Form
	if ($control_data) {
		echo $active_template($msgs, false, $atts);
	} else {
    // Show msgs
    if (isset($msgs['msgs'])) {
      if (!$control_msgs) {
        echo educare_show_msg($msgs['msgs'], $msgs['status'], false);
      }
    }

    // Show form for search the result
    // Check if function exists or not to ingnore php error
    if (function_exists($active_template)) {
      echo $active_template($msgs, false, $atts);
    } else {
      // Show error msgs for admin
      if (current_user_can( 'manage_options' )) {
        return educare_show_msg('Sorry, Error to load search form template. Your active search form template ('.esc_html($active_template).') is missing. Please, go to educare <a href="'.esc_url( admin_url() ).'/admin.php?page=educare-settings&menu=Search_Form" target="_blank"><code>Educare > Settings > Search Form > Site Key</code></a> and change your current search form template.', false, false);
      } else {
        // Show error msgs for users
        return educare_show_msg('Sorry, Error to load data', false, false);
      }
    }
	}

	// if request by Ajax
	if ($ajaxRequest) {
		// Close or remove 0 from ajxa response
		die;
	}
}



function educare_profiles_shortcode($atts) {
	// Extract attributes and their default values
	$atts = shortcode_atts(array(
		'title' => 'true',
		'subtitle' => 'false',
	), $atts, 'educare_profiles');

	// start output buffering
	ob_start();
	// Main content
	echo '<div id="educareProfiles">';
	echo educare_show_profiles($atts);
	echo '</div>';
	// return the output buffer contents
	return ob_get_clean();
}

// Create shortcode for educare profile system
add_shortcode('educare_profiles', 'educare_profiles_shortcode' );


/**
 * Educare profiles function
 *
 * @since 1.2.5
 * @last-update 1.2.5
 * @return void
 */

function educare_get_profiles() {
  // $current_user_id = $current_user->ID;
  $current_user = wp_get_current_user();
  $user_id = get_current_user_id();
  $educare_user_id = get_user_meta($user_id, 'user_id', true);

  $biography = get_the_author_meta('description', $user_id);

  if (!$biography) {
    $biography = __('No biographical information available.', 'educare');
  }

  ob_start();

  if (!$educare_user_id && !current_user_can( 'manage_options' ) && !current_user_can( 'educare_admin' )) {
    ?>
    <section>
      <div class="container">
        <div class="card h-100">
          <div class="card-body">
            <?php _e('Please login to access your profile.', 'educare');?>
          </div>
        </div>
      </div>
    </section>
    <?php
    
    return;
  }

  // educare_show_student_profiles($educare_user_id, true);
  // $user_data = educare_get_users_data($educare_user_id);
  // $user_details = json_decode($user_data->Details);

  // echo '<pre>';
  // print_r($user_data);
  // echo '</pre>';

  // the_author_meta('description');
  // the_author_meta( 'first_name', $user );
  // the_author_meta( 'last_name', $user );
  // echo do_shortcode("[wpforms_display_user_entries]");

  if (current_user_can('educare_students')) {
    // Student profiles
    ?>
    <section>

      <?php
      $user_data = educare_get_users_data($educare_user_id);
      $user_details = json_decode($user_data->Details);
      $user_others = json_decode($user_data->Others);
      $student = new StudentResult($user_data);
      $results = $student->getStudents();
      ?>

      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                  <img src="<?php echo esc_url(educare_get_attachment($user_others->Photos));?>" alt="<?php echo esc_attr($user_data->Name);?>" class="rounded-circle" width="150">
                  <div class="mt-3">
                    <h4><?php echo esc_html($user_data->Name);?></h4>
                    <p class="text-secondary mb-1"><?php _e('Students Profiles', 'educare');?></p>
                    <p class="text-muted font-size-sm"><?php echo wp_kses_post( $biography );?></p>
                    <a href="<?php bloginfo( 'url' ); ?>/edit-profiles"><button class="btn btn-primary"><?php _e('Edit Profiles', 'educare');?></button></a>
                    <a href="<?php echo wp_logout_url(); ?>"><button class="btn btn-outline-primary"><?php _e('Logout', 'educare');?></button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                  <?php
                  // Check requred fields data
                  $roles = 'students';
                  $requred = educare_check_status('display');
                  $requred_title = educare_requred_data($requred, true);
                  $requred_title = educare_roles_wise_filed(array('roles' => $roles, 'fields' => $requred_title));
                  $requred_count = count($requred_title);
                  $i = 1;

                  foreach ($requred_title as $key => $value) {
                    if (property_exists($user_data, $key)) {
                      $val = sanitize_text_field( $user_data->$key );

                      if ($key == 'user_pin') {
                        $val = educare_decrypt_data($val);

                        if ($user_data->pin_status == 'valid') {
                          $val .= ' <small class="text-success">('.esc_html(ucfirst($user_data->pin_status)).')</small></span>';
                        } else {
                          $val .= ' <small class="text-danger">('.esc_html(ucfirst($user_data->pin_status)).')</small></span>';
                        }
                        
                      }

                      ?>
                      <div class="row">
                        <div class="col-sm-3">
                          <h6 class="mb-0"><?php echo esc_html__( $value, 'educare' );?></h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                          <?php echo wp_kses_post($val);?>
                        </div>
                      </div>
                      <?php

                      if ($i < $requred_count) {
                        echo '<hr>';
                      }

                      $i++;
                    }
                  }
                  ?>
                </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#Attendance"><?php _e('Attendance', 'educare');?></a></li>

          <?php
          if (function_exists('educare_quiz_post_type')) {
            echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Quiz">'.__('Quiz', 'educare').'</a></li>';
          }
          ?>

          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Results"><?php _e('Results', 'educare');?></a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Details"><?php _e('Details', 'educare');?></a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Subject"><?php _e('Subject', 'educare');?></a></li>
          
          <?php
          if (function_exists('educare_get_analysis')) {
            echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Analysis">'.__('Analysis', 'educare').'</a></li>';
          }
          if (function_exists('educare_get_payment')) {
            echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">'.__('Payment', 'educare').'</a></li>';
          }
          ?>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">

          <div id="Attendance" class="container tab-pane active">

            <?php
            $getStatus = educare_show_attendance($educare_user_id, true);
            
            $currentMonth = date('m'); // Get the current month as a two-digit number
            $currentYear = date('Y'); // Get the current year
            $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

            foreach ($getStatus as $key => $value) {
              $$key = ($value / $numberOfDays) * 100;
            }

            ?>

            <div class="row">
              <div class="col-md-6 p-2">
                <div class="bg-dark text-light p-5 h-100 rounded">
                  <h2><?php _e('Attendance Details', 'educare');?></h2><hr>
                  <small><?php _e('Present', 'educare');?>: <?php echo esc_html($getStatus['present']);?></small>
                  <div class="progress mb-3" style="height:14px">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo esc_attr($present);?>%" aria-valuenow="<?php echo esc_attr($present);?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($numberOfDays);?>"></div>
                  </div>
                  <small><?php _e('Late', 'educare');?>: <?php echo esc_html($getStatus['late']);?></small>
                  <div class="progress mb-3" style="height:14px">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo esc_attr($late);?>%" aria-valuenow="<?php echo esc_attr($late);?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($numberOfDays);?>"></div>
                  </div>
                  <small><?php _e('Absent', 'educare');?>: <?php echo esc_html($getStatus['absent']);?></small>
                  <div class="progress mb-3" style="height:14px">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo esc_attr($absent);?>%" aria-valuenow="<?php echo esc_attr($absent);?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($numberOfDays);?>"></div>
                  </div>
                </div>
              </div>

              <div class="col-md-6 p-2">
                <div class="bg-light rounded">
                  <?php echo educare_show_attendance($educare_user_id);?>
                </div>
              </div>
            </div>
            
          </div>

          <?php
          if (function_exists('educare_quiz_post_type')) {
            ?>
            <div id="Quiz" class="container tab-pane fade">
              <?php
              echo educare_get_quiz_archive();

              echo '<a href="/archives/educare-quiz"><button class="btn btn-primary">'.__('View All', 'educare').'</button></a>';
              ?>
            </div>
            <?php
          }
          ?>

          <div id="Results" class="container tab-pane fade">
            <div class="row">
              <?php
              echo '<table class="table w-100">';
              echo '<thead class="bg-light">';
              echo '<tr>';
              echo '
              <th>'.__('No.', 'educare').'</th>
              <th>'.__('Class', 'educare').'</th>
              <th>'.__('Exam', 'educare').'</th>
              <th>'.__('Year', 'educare').'</th>
              <th>'.__('View', 'educare').'</th>
              ';
              echo '</tr>';
              echo '</thead>';
              echo '<tbody>';

              if ( $results) {
                $no = 1;
                // Check requred fields data
                $roles = 'results';
                $requred = educare_check_status('display');
                $requred_title = educare_requred_data($requred, true);
                $requred_title = educare_roles_wise_filed(array('roles' => $roles, 'fields' => $requred_title));
                unset($requred_title['Name']);

                foreach ($results as  $result) {
                  echo '<tr>
                  <td>'.esc_html($no++).'</td>
                  <td>'.esc_html($result->Class).'</td>
                  <td>'.esc_html($result->Exam).'</td>
                  <td>'.esc_html($result->Year).'</td>
                  <td>';
                  echo '<form action="/'.esc_attr(educare_check_status("results_page")).'" method="post">';
                  $nonce = wp_create_nonce( 'educare_form_nonce' );
									echo '<input type="hidden" name="nonce" value="'.esc_attr($nonce).'">';
                  $valid = true;
                  
                  foreach ($requred_title as $field => $title) {
                    if (property_exists($result, $field)) {
                      $result_key = sanitize_text_field( $result->$field );

                      if ($field == 'user_pin') {
                        if ($user_data->pin_status == 'valid') {
                          $result_key = educare_decrypt_data($user_data->$field);
                        } else {
                          $result_key = '';
                          $valid = false;
                        }
                      }

                      echo '<input type="hidden" name="'.esc_attr($field).'" value="'.esc_attr($result_key).'">';
                    }
                  }

                  if ($valid) {
                    echo '<button id="results_btn" class="results_button button" name="educare_results" type="submit" formtarget="_blank">'.__('View Results', 'educare').'</button>';
                  } else {
                    echo '<button id="results_btn" class="results_button bg-danger button" name="educare_results" type="submit" title="Expire Pin" formtarget="_blank" disabled>'.__('View Results', 'educare').'</button>';
                  }
                  
                  echo'</form>
                  </td>
                  </tr>';
                }
              } else {
                echo '
                <tr>
                <td colspan="5">
                <h3 class="p-5 text-center text-danger">'.__('Results not available or published yet.', 'educare').'</h3>
                </td>
                </tr>';
              }
              
              echo '</tbody>';
              echo '</table>';
              ?>
            </div>
          </div>

          <div id="Details" class="container tab-pane fade">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead class="bg-light">
                  <tr>
                    <th colspan="4"><?php _e('Student Info', 'educare');?></th>
                  </tr>
                </thead>
                <?php
                if ($user_data->Details) {
                  $count = 1; // for add specific tags (div/tr/ul) in every 4 foreach loop
                  $details = json_decode($user_data->Details);

                  foreach ($details as $key => $value) {
                    if ($count%2 == 1) {  
                      echo "<tr>";
                    }
                      
                    echo "<td>".esc_html__(str_replace('_', ' ', $key), 'educare')."</td><td>".esc_html($value)."</td>";
                    
                    if ($count%2 == 0) {
                      echo "</tr>";
                    }

                    $count++;

                  }
                }
                ?>
              </table>
            </div>
          </div>

          <div id="Subject" class="container tab-pane fade">
            <div class="bg-dark text-light p-5 h-100 rounded">
              <h2><?php _e('Subject List', 'educare');?></h2><hr>
              
              <?php
              if ($user_data->Subject) {
                $subject = json_decode($user_data->Subject);
                $optional_sub = educare_check_status('optional_sybmbol');
                $optional_sybmbol = '';
                echo '<ol>';
                
                foreach ($subject as $sub => $optional) {
                  if ($optional->optional) {
                    $optional_sybmbol = $optional_sub;
                  }
                  echo '<li>'.esc_html($sub).' '.esc_html($optional_sybmbol).'</li>';
                }

                echo '</ol>';
              }
              ?>
            </div>
          </div>

          <?php
          if (function_exists('educare_get_analysis')) {
            ?>
            <div id="Analysis" class="container tab-pane fade">
              <?php echo do_shortcode( '[educare_analysis_system]' );?>
            </div>
            <?php
          }
          ?>

          <?php
          if (function_exists('educare_get_payment')) {
            ?>
            <div id="payment" class="container tab-pane fade">
              <?php echo do_shortcode( '[educare_payment_system]' );?>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </section>
    <?php
  } elseif (current_user_can('educare_teachers')) {
    // Teachers profiles
    // add_filter('show_admin_bar', '__return_false');

    $user_data = educare_get_users_data($educare_user_id, 'teachers');
    $user_details = json_decode($user_data->Details);
    $user_others = json_decode($user_data->Others);
    $user_subject = json_decode($user_data->Subject);
    ?>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                  <img src="<?php echo esc_url(educare_get_attachment($user_others->Photos));?>" alt="Admin" class="rounded-circle" width="150">
                  <div class="mt-3">
                    <h4><?php echo esc_html($user_data->Name);?></h4>
                    <p class="text-secondary mb-1"><?php _e('Teachers Profiles', 'educare');?></p>
                    <p class="text-muted font-size-sm"><?php echo wp_kses_post( $biography );?></p>
                    <a href="/admin"><button class="btn btn-primary"><?php _e('Dashboard', 'educare');?></button></a>
                    <a href="<?php echo wp_logout_url(); ?>"><button class="btn btn-outline-primary"><?php _e('Logout', 'educare');?></button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Name', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_data->Name);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Institute', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Institute);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Type', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Type);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Mobile No', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Mobile_No);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Email', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Email);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-12">
                        <a href="<?php bloginfo( 'url' ); ?>/edit-profiles"><button class="btn btn-primary"><?php _e('Edit Profiles', 'educare');?></button></a>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <div class="bg-dark text-light p-5 h-100 rounded">
              <h2><?php _e('Subject List (can access)', 'educare');?></h2><hr>
              
              <?php
              if ($user_subject) {
                foreach ($user_subject as $class => $sub) {
                  // echo '<h5>Class - '.esc_html($class).'</h5>';
                  if ($sub) {
                    echo '<ol>';
                    foreach ($sub as $subject) {
                      echo '<li>'.esc_html($subject).'</li>';
                    }
                    echo '</ol>';
                  }
                }
              }
              ?>
            </div>
          </div>
        </div>

      </div>
    </section>
    <?php
  } else {
    // Admin profiles
    ?>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                  <img src="<?php echo esc_attr(get_avatar_url($user_id));?>" alt="Admin" class="rounded-circle" width="150">
                  <div class="mt-3">
                    <h4><?php echo esc_html($current_user->display_name);?></h4>
                    <p class="text-secondary mb-1"><?php _e('Admin Profiles', 'educare');?></p>
                    <p class="text-muted font-size-sm"><?php echo wp_kses_post( $biography );?></p>
                    <a href="/admin"><button class="btn btn-primary"><?php _e('Dashboard', 'educare');?></button></a>
                    <a href="<?php echo wp_logout_url(); ?>"><button class="btn btn-outline-primary"><?php _e('Logout', 'educare');?></button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Name', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($current_user->display_name);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Roles', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php
                        $user_roles = $current_user->roles;
                        if (!empty($user_roles)) {
                          $role_titles = array_map('ucwords', $user_roles); // Retrieves translated role titles if necessary
                          $roles_string = implode(', ', $role_titles);
                          echo $roles_string;
                        } else {
                          _e('No roles found for the user.', 'educare');
                        }
                        ?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Email', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($current_user->user_email);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Others info', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0"><?php _e('Registered', 'educare');?></h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($current_user->user_registered);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-12">
                        <a href="<?php bloginfo( 'url' ); ?>/edit-profiles"><button class="btn btn-primary"><?php _e('Edit Profiles', 'educare');?></button></a>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
  }

  return ob_get_clean();

}

// Create shortcode for educare profile system
// add_shortcode('educare_profiles', 'educare_get_profiles' );

?>