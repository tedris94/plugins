<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}




/**
 * Generates the default profiles layout and displays a profiles for retrieving student results using the default profiles template.
 *
 * This function is responsible for generating and displaying the default profiles layout for retrieving student results. It takes
 * various parameters, including a student data object, template details, and settings. If template details are provided, it returns
 * the default template information. If a student data object is provided, it processes the form data, displays the profiles
 * layout with input fields and controls, and handles form submission for searching student results or generating certificates. The
 * function also handles reCAPTCHA integration if enabled. The generated layout is based on the default profiles template and can
 * be customized using the provided template information and settings.
 *
 * @since 1.4.2
 * @last-update 3.4.8
 * 
 * @param object|null $print The student data object containing details like marks, subjects, and remarks. Not used in this function.
 * @param bool $template_details Optional. If true, returns the default template information. Default is false.
 * @param array $settings Optional. An array of settings for controlling the behavior of the profiles. Default is false.
 * @return array|null The default template information if $template_details is true, otherwise null.
 */
function educare_default_profiles($print = null, $template_details = false, $settings = false) {
  // Define customizable fields (default value).
  $fields = array(
    'results_button' => [                     // Fields name
      'title' => 'Results Button',            // Fields title
      'subtitle' => 'Results button title',   // Messages || guide for this fields
      'value' => 'View Results'               // Fields value
    ],
    'certificate_button' => [
      'title' => 'Certificate Button',
      'subtitle' => 'Certificate button title',
      'value' => 'Get Certificate'
    ]
  );

  // Get cutomized fields
  $fields = educare_get_template_settings($fields, __FUNCTION__, 'profiles_template');

  // Set template details (title or thumbnail)
	if ($template_details) {
    $requred_fields = array(
      // 'Roll_No',
      // 'Regi_No',
      // 'Class',
      // 'Exam',
      // 'Year',
    );

		$template_info = array(
			'title' => 'Default Profiles',
			'thumbnail' => EDUCARE_URL . 'assets/img/default-profiles.jpg', // for default use EDUCARE_TEMPLATE_THUMBNAIL
      'fields' => $fields,                                // Default text value
      // 'requred_fields' => $requred_fields,             // Set custom fields
			'control_data' => false,                            // Ingnore results template
			'control_msgs' => true,                             // To show custome error||succse mesgs
		);
		// return info
		return $template_info;
	} else {
		// $current_user_id = $current_user->ID;
    // $current_user = wp_get_current_user();
    $user_id = get_current_user_id();
    $educare_user_id = get_user_meta($user_id, 'user_id', true);

    if (current_user_can('educare_students')) {
      // Students profiles
      educare_default_profiles_layout($educare_user_id, 'students');
    } elseif (current_user_can('educare_teachers')) {
      // Teachers profiles
      educare_default_profiles_layout($educare_user_id, 'teachers');
    } elseif (current_user_can( 'manage_options' ) or current_user_can( 'educare_admin' )) {
      // Admin profiles
      $profiles_id = $profiles_for = '';

      if (isset($_GET['profiles_id'])) {
        $profiles_id = sanitize_text_field($_GET['profiles_id']);
      }

      if (isset($_GET['profiles_for'])) {
        $profiles_for = sanitize_text_field($_GET['profiles_for']);
      }

      if ($profiles_id and $profiles_for) {
        // 
        ?>
        <section>
          <div class="container">
            <div class="card h-100">
              <div class="card-body">
                <?php _e('Admin Notice: Now you are viewing the '.esc_html($profiles_for).' account.', 'educare-dashboard-profiles');?>
              </div>
            </div>
          </div>
        </section>
        <?php
        
        educare_default_profiles_layout($profiles_id, $profiles_for);
      } else {
        $profiles_page = educare_check_status("edit_profiles_page");
        $current_user = wp_get_current_user();
        $biography = get_the_author_meta('description', $user_id);
    
        if (!$biography) {
          $biography = __('No biographical information available.', 'educare-dashboard-profiles');
        }
        
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
                        <p class="text-secondary mb-1"><?php _e('Admin Profiles', 'educare-dashboard-profiles');?></p>
                        <p class="text-muted font-size-sm"><?php echo wp_kses_post( $biography );?></p>
                        <a href="/admin"><button class="btn btn-primary"><?php _e('Dashboard', 'educare-dashboard-profiles');?></button></a>
                        <a href="<?php echo wp_logout_url(); ?>"><button class="btn btn-outline-primary"><?php _e('Logout', 'educare-dashboard-profiles');?></button></a>
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
                            <h6 class="mb-0"><?php _e('Name', 'educare-dashboard-profiles');?></h6>
                          </div>
                          <div class="col-sm-9 text-secondary">
                            <?php echo esc_html($current_user->display_name);?>
                          </div>
                      </div>
                      <hr>
                      <div class="row">
                          <div class="col-sm-3">
                            <h6 class="mb-0"><?php _e('Rules', 'educare-dashboard-profiles');?></h6>
                          </div>
                          <div class="col-sm-9 text-secondary">
                            <?php
                            $user_roles = $current_user->roles;
                            if (!empty($user_roles)) {
                              $role_titles = array_map('ucwords', $user_roles); // Retrieves translated role titles if necessary
                              $roles_string = implode(', ', $role_titles);
                              echo $roles_string;
                            } else {
                              _e('No roles found for the user.', 'educare-dashboard-profiles');
                            }
                            ?>
                          </div>
                      </div>
                      <hr>
                      <div class="row">
                          <div class="col-sm-3">
                            <h6 class="mb-0"><?php _e('Email', 'educare-dashboard-profiles');?></h6>
                          </div>
                          <div class="col-sm-9 text-secondary">
                            <?php echo esc_html($current_user->user_email);?>
                          </div>
                      </div>
                      <hr>
                      <div class="row">
                          <div class="col-sm-3">
                            <h6 class="mb-0"><?php _e('Others info', 'educare-dashboard-profiles');?></h6>
                          </div>
                          <div class="col-sm-9 text-secondary">
                            
                          </div>
                      </div>
                      <hr>
                      <div class="row">
                          <div class="col-sm-3">
                            <h6 class="mb-0"><?php _e('Registered', 'educare-dashboard-profiles');?></h6>
                          </div>
                          <div class="col-sm-9 text-secondary">
                            <?php echo esc_html($current_user->user_registered);?>
                          </div>
                      </div>
                      <hr>
                      <div class="row">
                          <div class="col-sm-12">
                            <a href="<?php echo bloginfo( 'url' ).'/'.esc_attr($profiles_page); ?>"><button class="btn btn-primary"><?php _e('Edit Profiles', 'educare-dashboard-profiles');?></button></a>
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
    } else {
      // do it!
    }
	}
}

// Apply or Install template
// Hook the function to the educare_profiles_template action
add_action( 'educare_profiles_template', 'educare_default_profiles' );





function educare_default_profiles_layout($id = null, $roles = 'students') {
  $user_data = educare_get_users_data($id, $roles);

  if ($user_data) {
    $user_details = json_decode($user_data->Details);
    $user_others = json_decode($user_data->Others);
    $user_id = $user_data->user_id;
    $biography = get_the_author_meta('description', $user_id);

    $front_dashboard = educare_check_status("front_dashboard");
    $active_menu = isset($_GET[$front_dashboard]) ? $_GET[$front_dashboard] : null;
    $profiles_page = educare_check_status("edit_profiles_page");

    if ($roles == 'students') {
      // Student profiles
      $student = new StudentResult($user_data);
      $results = $student->getStudents(array(
        'Roll_No',
        'Regi_No'
      ));
      
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
                      <a href="<?php echo bloginfo( 'url' ).'/'.esc_attr($profiles_page); ?>"><button class="btn btn-primary"><?php _e('Edit Profiles', 'educare');?></button></a>
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

            <?php 
            if (function_exists('educare_dynamic_template')) {
              ?>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Summative-Report"><?php _e('Summative Report', 'educare');?></a></li>

              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Progress-Report"><?php _e('Progress Report', 'educare');?></a></li>
              <?php
            }
            ?>

            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Subject"><?php _e('Subject', 'educare');?></a></li>
            
            <?php
            if (function_exists('educare_get_analysis')) {
              echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Analysis">'.__('Analysis', 'educare').'</a></li>';
            }
            if (function_exists('educare_get_payment')) {
              echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">'.__('Payment', 'educare').'</a></li>';
            }
            ?>

            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Details"><?php _e('Details', 'educare');?></a></li>
          </ul>
  
          <!-- Tab panes -->
          <div class="tab-content pt-2">
  
            <div id="Attendance" class="container tab-pane active">
  
              <?php
              $getStatus = educare_show_attendance($id, true);
              
              $currentMonth = date('m'); // Get the current month as a two-digit number
              $currentYear = date('Y'); // Get the current year
              $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
  
              foreach ($getStatus as $key => $value) {
                $$key = ($value / $numberOfDays) * 100;
              }
  
              ?>
  
              <div class="row">
                <div class="col-md-6 mb-2">
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
  
                <div class="col-md-6 mb-2">
                  <div class="bg-light overflow-hidden rounded">
                    <?php echo educare_show_attendance($id);?>
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

            <?php
            if (function_exists('educare_dynamic_template')) {
              ?>
              <!-- Summative-Report -->
              <div id="Summative-Report" class="container tab-pane fade">
                <div class="wow fadeInUp card border-0 shadow" data-wow-delay=".1s">
                  <div class="card-header bg-primary text-white fs-5">
                    <?php _e('Summative Report', 'educare-dynamic-profiles'); ?>
                  </div>
                  <div class="card-body">
                    <?php
                    if ($results) {
                      echo '<div class="scrollable-row exam-overview">';
                      $exam_count = 1;

                      foreach ($results as $print) {
                        $student_reults = educare_dynamic_template($print, true);
                        $exam = $student_reults->Exam;
                        $subject = $student_reults->Subject;

                        if ($exam_count === 1) {
                          echo '<div class="scrollable-col m-0">
                          <table class="table">
                          <tr><th class="text-center">Lerning Area</th></tr>';

                          if ($subject) {
                            echo '<tr><th>Subject</th></tr>';
                            foreach ($subject as $sub_name => $sub_data) {
                              echo '<tr><td>'.esc_html( $sub_name ).'</td></tr>';
                            }
                          }

                          echo '</table>
                          </div>';
                        } 

                        echo '<div class="scrollable-col m-0">
                        <table class="table exam-'.esc_attr($exam_count).' '.esc_attr(str_replace(' ', '-', $exam)).'">
                        <tr><th colspan="4" class="text-center">'.esc_html( $exam ).'</th></tr>
                        <tr>';
                        echo '<th>Score</th>
                        <th>Rubric</th>
                        <th>Rubric Points</th>
                        </tr>';

                        if ($subject) {
                          foreach ($subject as $sub_name => $sub_data) {
                            echo '<tr>';
                            echo '<td>'.esc_html( $sub_data->combine ).'%</td>
                            <td>'.wp_kses_post( $sub_data->grade ).'</td>
                            <td>'.esc_html( $sub_data->gpa ).'</td>
                            </tr>';
                          }
                        }

                        echo '</table>
                        </div>';

                        $exam_count++;
                        // end exam
                      }

                      echo '</div>';
                    }
                    ?>
                  </div>
                </div>
              </div>

              <!-- Pathways-Mapping -->
              <div id="Progress-Report" class="container fade">
                <div class="wow fadeInUp card border-0 shadow" data-wow-delay=".1s">
                  <div class="card-header bg-primary text-white fs-5"><?php _e('Progress Report', 'educare-dynamic-profiles'); ?></div>
                  <div class="card-body analytics-bg">
                    <div class="h-100">
                      <?php
                      if (function_exists('educare_get_dynamic_chart_data')) {
                        educare_get_specialization_pathways($results, $user_data);
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
  
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

          </div>
        </div>

      <?php
    } else {
      // Teachers profiles
      if (property_exists($user_data, 'Subject')) {
        $user_subject = json_decode($user_data->Subject);
      } else {
        return;
      }
      

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
                    <?php
                    if ($user_details) {
                      foreach ($user_details as $key => $value) {
                        echo '<div class="row">';
                        echo '<div class="col-sm-3">';
                        echo '<h6 class="mb-0">'._e(str_replace('_', ' ', $key), 'educare').'</h6>';
                        echo '</div>';
                        echo '<div class="col-sm-9 text-secondary">';
                        echo esc_html($value);
                        echo '</div>';
                        echo '</div><hr>';
                      }
                    }
                    ?>
                    
                    <div class="row">
                      <div class="col-sm-12">
                        <a href="<?php echo bloginfo( 'url' ).'/'.esc_attr($profiles_page); ?>"><button class="btn btn-primary"><?php _e('Edit Profiles', 'educare');?></button></a>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
  
          <div class="row">
            <div class="col">
              <div class="bg-dark text-light p-5 h-100 rounded">
                <h2 class="text-light"><?php _e('Subject List (can access)', 'educare');?></h2><hr>
                
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
    }
  } else {
    if (!current_user_can( 'manage_options' ) or !current_user_can( 'educare_admin' )) {
      $messege = 'Sorry error to load user data!';
    } else {
      $messege = 'Connections lost!';
    }
    
    ?>
    <section>
      <div class="container">
        <div class="card h-100">
          <div class="card-body">
            <?php _e($messege, 'educare-dashboard-profiles');?>
          </div>
        </div>
      </div>
    </section>
    <?php
  }

}

?>