<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/** 
 * ### Educare Grading Systems
 * Modified by Idris Bala Usman
 * usage => echo educare_grade_system("85");
 * Default grading system is
 * 
 * $grade_system = array(
    'current' => 'Default',
    'rules' => [
      'Default' => [
       '80-100' => [5, 'A', 'Excellence'],
						'70-79'  => [4, 'B', 'Very Good'],
						'60-69'  => [3.5, 'C', 'Good'],
						'50-59'  => [3, 'D', 'Average'],
						'40-49'  => [2, 'E', 'Fair'],
						'0-39'   => [1, 'F', 'Very Poor']
      ]
    ]
  );
 * 
 * @since 1.2.0
 * @last-update 1.2.0
 * 
 * @param $marks int/str  for grading system
 * @return str
 */

function educare_grade_system($marks) {
  $grade_system = educare_check_status('grade_system');
  $current = $grade_system->current;
  $grade_system = $grade_system->rules->$current;

  // check optional marks
  $optional_marks = substr(strstr($marks, ' '), 1);
  if ($optional_marks) {
    $marks = $optional_marks;
  }

  foreach ($grade_system as $rules => $grade) {
    if ($rules == 'failed' or $rules == 'success') break;
    // get first rules number to compare
    $rules1 = strtok($rules, '-');
    // get second rules number to compare
    $rules2 = substr(strstr($rules, '-'), 1);

    if ($marks >= $rules1 and $marks <= $rules2) {
      $marks = $grade;
    }
  }

  return $marks;
}



/**
 * ### Save Grading System
 * 
 * usage => echo educare_save_results_system();
 * 
 * @since 1.2.0
 * @last-update 1.2.0
 * 
 * @return void
 */

function educare_save_results_system() {
  global $wpdb;
  $table = $wpdb->prefix . "educare_settings";

  $search = $wpdb->get_row("SELECT * FROM $table WHERE list='Settings'");

  if ($search) {
    // if (isset($_POST['update_grade_rules'])) {
      $id = $search->id;
      $data = $search->data;
      $data = json_decode($data);
      
      $rules_name = sanitize_text_field($_POST['rules']);
      $rules1 = array_map( 'sanitize_text_field', $_POST['rules1'] );
      $rules2 = array_map( 'sanitize_text_field', $_POST['rules2'] );
      $grade = array_map( 'sanitize_text_field', $_POST['grade'] );
      $point = array_map( 'sanitize_text_field', $_POST['point'] );
      $description = array_map('sanitize_text_field', $_POST['description']); // New: Sanitize descriptions

      $count1 = $count2 = $count3 = $count4 = 0;
      $rules = array();
      foreach ($grade as $value) {
        $key = $rules1[$count1++] . '-' . $rules2[$count2++];
        $rules[$key][0] = $point[$count3++];
        $rules[$key][1] = $value;
        $rules[$key][2] = $description[$count4++]; // New: Add description to the rules array
      }

      $grade_system = educare_check_status('grade_system');
      $grade_system->rules->$rules_name = $rules;
      $data->grade_system = $grade_system;

      // now update desired data
      $wpdb->update(
        $table, //table
        array(  // data
                // we need to encode our data for store array/object into databases
          "data" => json_encode($data)
        ),
        
        array( //where
          'ID' => $id
        )
        
      );

      echo "<div class='notice notice-success is-dismissible'><p>Successfully updated " . wp_kses_post($rules_name) . " grading systems</p></div>";
    // }
  } else {
    echo educare_guide_for('db_error');
  }
}



/**
 * ### Showing Grading System
 * 
 * usage => echo educare_show_grade_rule();
 * 
 * @since 1.2.0
 * @last-update 1.2.0
 * 
 * @return void
 */

function educare_show_grade_rule() {
  $grade_system = educare_check_status('grade_system');
  $current = $grade_system->current;
  $grade_system = $grade_system->rules->$current;

  echo '<div class="table_container"><table class="grade_sheet grading-system">
  <thead>
    <tr>
      <th>'.__('Class interval', 'educare').'</th>
      <th style="text-align: center">'.__('Grade Points', 'educare').'</th>
      <th>'.__('Letter grade', 'educare').'</th>
      <th>'.__('Grade Remarks', 'educare').'</th>
    </tr>
    </thead>';

  foreach ($grade_system as $marks => $value) {
    echo '<tr>
      <td>'. esc_html($marks) .'</td>
      <td>'. esc_html($value[0]) .'</td>
      <td>'. esc_html($value[1]) .'</td>
      <td>'. esc_html($value[2]) .'</td>
    </tr>';
  }
  
  echo '</table></div>';
}



/**
 * ### Modify or update grading systems
 * 
 * @since 1.2.0
 * @last-update 1.2.0
 * 
 * @return proceess data
 */

add_action('wp_ajax_educare_proccess_grade_system', 'educare_proccess_grade_system');

function educare_proccess_grade_system() {
  educare_verify_nonce('edit_grade_system');
  
	$rules = sanitize_text_field($_POST['class']);

	function educare_add_grade_system($rules = null, $point = null, $grade = null, $description = null) {
    // get first rules (less den) number to compare
    $rules1 = strtok($rules, '-');
    // get second rules (greater den) number to compare
    $rules2 =substr(strstr($rules, '-'), 1);

		if (!$rules1) {
			$rules1 = 0;
		}
		if (!$rules2) {
			$rules2 = 0;
		}
		if (!$point) {
			$point = 0;
		}
    if (!$description) {
      $description = ''; // Default empty description
  }
    ?>
		<tr class="cloneField">
			<td><input type="number" name="rules1[]" value="<?php echo esc_attr($rules1)?>" placeholder="<?php echo esc_attr($rules1)?>" step="any"></td>
			<td><input type="number" name="rules2[]" value="<?php echo esc_attr($rules2)?>" placeholder="<?php echo esc_attr($rules2)?>" step="any"></td>
			<td><input type="number" name="point[]" value="<?php echo esc_attr($point)?>" placeholder="<?php echo esc_attr($point)?>" step="any"></td>
			<td><input class="bold" type="text" name="grade[]" value="<?php echo esc_attr($grade)?>" placeholder="<?php echo esc_attr($grade)?>"/></td>
			<td><input type="text" name="description[]" value="<?php echo esc_attr($description); ?>" placeholder="Description" /></td>
      <td><a href="<?php echo esc_js( 'javascript:void(0);' );?>" class="remove_button"><i class="dashicons dashicons-no"></i></a></td>
		</tr>
    <?php
  }
  
	$grade_system = educare_check_status('grade_system');
	$grade_system = $grade_system->rules->$rules;

  ?>
	<div class="notice notice-success is-dismissible"><p>
		<form id='addForm' action="" method="post">
			<div class='fixbd_cloneField'>
				<h2>Edit Rules</h2>
				<p id='status' class='warning sticky'></p>
				
				<p>Rules Name:</p>
				<input type="text" name="rules" value="<?php echo esc_attr($rules)?>" placeholder=""/ disabled>
        <br>
				<input type="hidden" name="rules" value="<?php echo esc_attr($rules)?>">
				
        <div class="table_container">
        <table class="grade_sheet" id='cloneBody'>
            <thead>
              <tr>
                <th>Less Mark</th>
                <th>Greater Mark</th>
                <th>Grade point</th>
                <th>Letter grade</th>
                <th>Grade Remarks</th>
                <th>Close</th>
              </tr>
            </thead>
            <tbody id='cloneBody'>
              <?php
              // $count1 = $count2 = 0;
              foreach ( $grade_system as $rules => $grade ) {
                $description = isset($grade[2]) ? $grade[2] : ''; // Ensure compatibility with older records
                educare_add_grade_system($rules, $grade[0], $grade[1], $description);
              }
              ?>
            </tbody>
          </table>
        </div>

        <?php
        // Security nonce for this form.
        $nonce = wp_create_nonce( 'update_grade_rules' );
        echo '<input type="hidden" name="nonce" value="'.esc_attr($nonce).'">';
        ?>
				
				<div class="button-container">
				<a href='<?php echo esc_js( 'javascript:void(0);' );?>' class='addButton educare_button' title='Add more field'><i class='dashicons dashicons-plus-alt'></i></a>
				<button id='save_addForm' class="educare_button" name="update_grade_rules"><i class='dashicons dashicons-yes'></i></button>
				</div>
				
			</div>
		</form>
		
		<div id='cloneWrapper' style='display: none;'>
			<?php educare_add_grade_system();?>
		</div>
	</p><button class="notice-dismiss"></button></div>

  <script type="text/javascript"><?php echo esc_js( 'cloneField()' );?></script>
	<?php

	die;
}



/**
 * ### Save grading fields data
 * 
 * 
 * @since 1.2.0
 * @last-update 1.2.0
 * 
 * @return void
 */

add_action('wp_ajax_educare_save_grade_system', 'educare_save_grade_system');

function educare_save_grade_system() {
  // Remove the backslash
	$_POST['form_data'] = stripslashes($_POST['form_data']);
  // parses query strings and sets the parsed values into the $_POST array.
  wp_parse_str($_POST['form_data'], $_POST);

  // Verify the nonce to ensure the request originated from the expected source
  educare_verify_nonce('update_grade_rules');

  // Save data
  educare_save_results_system();
  // Show updated data
  educare_show_grade_rule();

  // Ignore (0) and stop ajax response
  die;
}


// Dont't close

