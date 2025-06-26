<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * ### Educare enqueue style & javascript
 * 
 * Load CSS, JS to specific pages from created menu and submenu ( only for Educare pages )
 * 
 * @since 1.0.0
 * @last-update 1.0.0
 * 
 * @param mixed $hook
 * @return void
 */
function educare_import_system_enqueue_styles( $hook) {
  // globalize educare main menu and sub menu '$educare_slug_hook'
  global $educare_main_menu, $educare_slug_hook;
  // add main (main menu) page
  array_push($educare_slug_hook, $educare_main_menu);

  // ignore educare style and script if not a educare page
  if ( !in_array($hook, $educare_slug_hook) ) {
    return;
  }

  // Educare stylesheet
  wp_enqueue_style('educare-import-system', EDUCARE_IMPORT_SYSTEM_URL.'assets/css/style.css');
  // JavaScript link
  wp_enqueue_script('educare-import-system', EDUCARE_IMPORT_SYSTEM_URL.'assets/js/educare-import-system.js', array('educare-admin'), '1.0', true);
}

add_action( 'admin_enqueue_scripts', 'educare_import_system_enqueue_styles');



/**
 * Exports student data into an Excel spreadsheet.
 *
 * @since 1.0.0
 *  @last-update 1.0.0
 * 
 * @param array $data  An array containing student, teachers or results data organized by categories (Student Info, Details, Subject, Rattings, remarks).
 * @param string $roles The roles associated with the exported data (e.g., student, teachers, results).
 *
 * @return void The function generates and saves an Excel spreadsheet with the provided student data.
 */
function educare_export_data($data, $roles) {
  if ($data) {
    // Include the PHPExcel library
    // require_once(EDUCARE_INC.'support/Classes/PHPExcel.php');
    require_once(EDUCARE_IMPORT_SYSTEM.'support/PHPExcel/PHPExcel.php');
    $objPHPExcel = new PHPExcel();

    // Set the active sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();

    // Set border style and fill color
    $outsideBorder = array(
      'borders' => array(
          'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('rgb' => '000000'),
          ),
      ),
    );
    $borderStyle = array(
      'borders' => array(
          'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('rgb' => '333333'),
          ),
      ),
    );
    $fillColor = array(
      'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '333333'),
      ),
      'font' => array(
        'color' => array('rgb' => 'FFFFFF'),
      ),
    );
    $subFillColor = array(
      'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'CCCCCC'),
      ),
      'font' => array(
        'color' => array('rgb' => '333333'),
      ),
    );

    $row = $row_right = $i = 1;
    $total_data = count($data);

    // Loop through each student data
    foreach ($data as $student) {
      $student_no = $i++;
      $student_border = 'E';
      foreach ($student as $key => $details) {
        // Handle Student Info
        if ($key == 'Student Info') {
          // Set the student Student Info captions
          $sheet->setCellValue('A' . $row, 'Student Info')->getStyle('A' . $row)->applyFromArray($fillColor)->getFont()->setBold(true);
          // Merge cells for the caption
          $sheet->mergeCells('A' . $row . ':B' . $row);
          $student_begin = $row++;

          foreach ($details as $field => $value) {
            // Set demo value
            if ($field === 'Name') {
              $value = sanitize_text_field( 'Name ' . $student_no );
            }
            if ($field === 'Roll_No') {
              $value = sanitize_text_field( rand(1000, 2000) );
            }
            if ($field === 'Regi_No') {
              $value = sanitize_text_field( rand(3000, 400000) );
            }

            $sheet->setCellValue('A' . $row, $field)->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, $value);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($borderStyle);
            // Text align left
            $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $row++;
          }
        }

        // Handle Details
        if ($key == 'Details') {
          // Set the student Details captions
          $sheet->setCellValue('D' . $row_right, 'Details')->getStyle('D' . $row_right)->applyFromArray($fillColor)->getFont()->setBold(true);
          $sheet->mergeCells('D' . $row_right . ':E' . $row_right);
          $row_right++;
          
          foreach ($details as $field => $value) {
            $sheet->setCellValue('D' . $row_right, $field)->getStyle('D' . $row_right)->getFont()->setBold(true);
            $sheet->setCellValue('E' . $row_right, $value);
            $sheet->getStyle('D' . $row_right . ':E' . $row_right)->applyFromArray($borderStyle);
            $row_right++;
          }
        }

        // Handle Subject
        if ($key == 'Subject') {
          // Add spacing between Details and subjects
          if ($row++ < $row_right) {
            $row = 1 + $row_right;
          }

          // Set the grade sheet
          $int_columns = $columns = 'A';
          $sheet->setCellValue($int_columns . $row, 'Grade Sheet')->getStyle($int_columns . $row)->applyFromArray($fillColor)->getFont()->setBold(true);
          $head_row = $row;
          $row++;

          $sheet->setCellValue($columns . $row, 'Subject')->getStyle($columns . $row)->applyFromArray($subFillColor)->getFont()->setBold(true);

          // Dynamicaly get marks terms
          foreach ($details as $subject => $marks) {
            foreach ($marks as $label => $value) {
              $columns++;
              $sheet->setCellValue($columns . $row, $label)->getStyle($columns . $row)->applyFromArray($subFillColor)->getFont()->setBold(true);
            }

            $row++;
            break;
          }

          // Merge head (Grade Sheet)
          $student_border = $columns;
          $sheet->mergeCells($int_columns . $head_row . ':' .$columns . $head_row);

          // Set the subject data
          foreach ($details as $subject => $marks) {
            $sheet->setCellValue($int_columns . $row, $subject);
            $sheet->getStyle($int_columns . $row . ':' . $columns . $row)->applyFromArray($borderStyle);

            $col = $int_columns;
            $col++;
            foreach ($marks as $mark) {
              $sheet->setCellValue($col . $row, $mark);
              $col++;
            }

            $row++;
          }

          $row_right = 1 + $row;
        }
        
        if ($key == 'Rattings') {
          $row++; // Add spacing between Subjects and Rattings
          // Set the student Student Info captions
          $sheet->setCellValue('A' . $row, 'Rattings')->getStyle('A' . $row)->applyFromArray($fillColor)->getFont()->setBold(true);
          // Merge cells for the caption
          $sheet->mergeCells('A' . $row . ':B' . $row);
          $row++;

          foreach ($details as $rattings => $value) {
            $sheet->setCellValue('A' . $row, $rattings)->getStyle('A' . $row)->applyFromArray($fillColor)->applyFromArray($subFillColor)->getFont()->setBold(true);
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $row++;


            foreach ($value as $term => $scal) {
              $sheet->setCellValue('A' . $row, $term)->getStyle('A' . $row)->getFont()->setBold(true);
              $sheet->setCellValue('B' . $row, $scal);
              $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($borderStyle);
              $row++;
            }
            
          }

        }

        if ($key == 'remarks') {
          // Set the student Student Info captions
          $sheet->setCellValue('D' . $row_right, 'remarks')->getStyle('D' . $row_right)->applyFromArray($fillColor)->getFont()->setBold(true);
          // Merge cells for the caption
          $sheet->mergeCells('D' . $row_right . ':E' . $row_right);
          $row_right++;

          foreach ($details as $remarks => $value) {
            $sheet->setCellValue('D' . $row_right, $remarks)->getStyle('D' . $row_right)->applyFromArray($fillColor)->applyFromArray($subFillColor)->getFont()->setBold(true);
            $sheet->mergeCells('D' . $row_right . ':E' . $row_right);
            $row_right++;


            foreach ($value as $term => $scal) {
              $sheet->setCellValue('D' . $row_right, $term)->getStyle('D' . $row_right)->getFont()->setBold(true);
              $sheet->setCellValue('B' . $row_right, $scal);
              $sheet->getStyle('D' . $row_right . ':E' . $row_right)->applyFromArray($borderStyle);
              $row_right++;
            }
            
          }

          // Add spacing between Details and subjects
          if ($row < $row_right) {
            $row = $row_right;
          }
        }

      }

      if ($student_border < 'E') {
        $student_border = 'E';
      }

      // Add student border box
      $sheet->getStyle('A' . $student_begin . ':' . $student_border . $row-1)->applyFromArray($outsideBorder);

      // Add spacing between students
      $row += 3;
      $row_right = $row;
    }

    // Set the column widths
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(15);

    // Save the spreadsheet
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save(EDUCARE_IMPORT_SYSTEM.'assets/demo-files/demo-file-for-'.esc_attr($roles).'.xlsx');

    // Download the spreadsheet
    // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // header('Content-Disposition: attachment;filename="student_data1.xlsx"');
    // header('Cache-Control: max-age=0');
    // $objWriter->save('php://output');
    // exit;
  }
}



/**
 * Imports student data from an Excel spreadsheet.
 * 
 * @since 1.0.0
 * @last-update 1.0.0
 *
 * @param string $roles The roles associated with the imported data (e.g., student roles).
 *
 * @return void The function imports and processes student data from the uploaded Excel spreadsheet.
 */
function educare_import_data($roles = 'results') {
  // Include the PHPExcel library
  require_once(EDUCARE_IMPORT_SYSTEM.'support/PHPExcel/PHPExcel.php');
  
  if(isset($_POST['import_data'])) {
    // Verify the nonce to ensure the request originated from the expected source
    educare_verify_nonce('educare_import_data');

    // Check if a file is chosen
    if ($_FILES['xlsx_file']['name']) {
      $extension = pathinfo($_FILES['xlsx_file']['name'], PATHINFO_EXTENSION);

      // check file extension
      if ($extension == 'xlsx' or $extension == 'xlsm' or $extension == 'xls') {
        // Check if a file is uploaded
        if(isset($_FILES['xlsx_file']) && $_FILES['xlsx_file']['error'] === UPLOAD_ERR_OK) {
          $tmpFilePath = $_FILES['xlsx_file']['tmp_name'];

          // Load the Excel file
          $objPHPExcel = PHPExcel_IOFactory::load($tmpFilePath);

          // Get the active sheet
          $sheet = $objPHPExcel->getActiveSheet();

          // Initialize the student data array
          $data = array();
          $student_id = 0;

          $row = 1;
          $row_right = 1;

          // Loop through each row of the sheet
          while ($sheet->getCell('A' . $row)->getValue() !== null) {
            // Check if it's a "Student Info" section
            if ($sheet->getCell('A' . $row)->getValue() == 'Student Info') {
              $student_info = array();

              // Get the student info data
              $row++;
              // $sheet->getCell('A' . $row)->getValue() !== 'Grade Sheet'
              while (!$sheet->getCell('A' . $row)->isInMergeRange()) {
                $field = $sheet->getCell('A' . $row)->getValue();
                $value = $sheet->getCell('B' . $row)->getValue();

                if ($field) {
                  $student_info[$field] = $value;
                }

                $row++;
              }

              // Add the student info to the student data array
              $data[$student_id]['default'] = $student_info;
            }

            // Check if it's a "Details" section
            if ($sheet->getCell('D' . $row_right)->getValue() == 'Details') {
              $details = array();

              // Get the details data
              $row_right++;

              // while ($sheet->getCell('D' . $row_right)->getValue() !== null && $sheet->getCell('D' . $row_right)->getValue() !== 'Grade Sheet') {
              while (!$sheet->getCell('A' . $row_right)->isInMergeRange()) {
                $field = $sheet->getCell('D' . $row_right)->getValue();
                $value = $sheet->getCell('E' . $row_right)->getValue();

                if ($field) {
                  $details[$field] = $value;
                }
                
                $row_right++;
              }

              // Add the details to the student data array
              $data[$student_id]['Details'] = $details;
            }

            // Get spacing between sections
            $row++;

            // Check if it's a "Subject" section
            if ($sheet->getCell('A' . $row)->getValue() == 'Subject') {
              $int_row = $row;
              
              while ($sheet->getCell('A' . $row)->getValue() !== null) {
                $row++;
                $int_columns = $columns = 'A'; $columns++;
                $sub = $sheet->getCell($int_columns . $row)->getValue();

                if ($sub) {
                  while ($sheet->getCell($columns . $int_row)->getValue() !== null) {
                    $col = $columns++;
                    // $terms[$sub][] = $sheet->getCell($col . $row)->getValue();
                    $term_key = $sheet->getCell($col . $int_row)->getValue();
                    $data[$student_id]['Subject'][0][$sub][$term_key] = $sheet->getCell($col . $row)->getValue();
                  }
                }
                
              }

              // // Add spacing between students
              // $row += 3;
              // $row_right = $row;
              // // Move to the next student
              // $student_id++;
              $row_right = $row;
            }

            if ($roles == 'results' && educare_check_status('rattings_system') == 'checked') {
              $Rattings = educare_check_settings('Rattings');

              if ($Rattings) {
                // Get spacing between sections
                $row++;

                if ($sheet->getCell('A' . $row)->getValue() == 'Rattings') {
                  $row++;
                  
                  while ($sheet->getCell('A' . $row)->getValue() !== null) {
                    if ($sheet->getCell('A' . $row)->isInMergeRange()) {
                      $key = $sheet->getCell('A' . $row)->getValue();
                    } else {
                      $field = $sheet->getCell('A' . $row)->getValue();
                      $value = $sheet->getCell('B' . $row)->getValue();

                      $data[$student_id]['Others']['Rattings'][$key][$field] = $value;
                    }

                    $row++;
                  }
                }

              }
            }


            if ($roles == 'results' && educare_check_status('remarks') == 'checked') {
              $row_right++;

              // Check if it's a "remarks" section
              if ($sheet->getCell('D' . $row_right)->getValue() == 'remarks') {
                $remarks = array();

                // Get the remarks data
                $row_right++;

                while ($sheet->getCell('D' . $row_right)->getValue() !== null) {
                  if ($sheet->getCell('D' . $row_right)->isInMergeRange()) {
                    $key = $sheet->getCell('D' . $row_right)->getValue();
                  } else {
                    $field = $sheet->getCell('D' . $row_right)->getValue();
                    $value = $sheet->getCell('E' . $row_right)->getValue();

                    $data[$student_id]['Others']['remarks'][$key][$field] = $value;
                  }
                  
                  $row_right++;
                }

              }

              if ($row < $row_right) {
                $row = $row_right;
              }
            }

            // Add spacing between students
            $row += 3;
            $row_right = $row;
            // Move to the next student
            $student_id++;

          }

          // Print the converted student data array
          // print_r($data);
          // echo '<pre>';
          // print_r($data);
          // echo '</pre>';

          if ($data) {
            $status = array (
              'total' => count($data),
              'success' => 0,
              'exist' => 0,
              'error' => 0,
              'empty' => 0,
              'error_creating_user' => 0,
            );
            $i = 1;

            foreach ($data as $data_key => $import) {
              $i++;
              // Clean Post data
              $_POST = array();
              $_POST['roles'] = $roles;

              foreach ($import as $key => $value) {
                $_POST[$key] = $value;
                // Remove faced data to improve speed!
                unset($import[$key]);
              }

              $_POST['Others']['Photos'] = '';
              $_POST['crud'] = 'crud';

              $import_data = educare_crud_data_new(true);
              
              if (key_exists($import_data, $status)) {
                $status[$import_data] = $status[$import_data] + 1;
              }
              // Remove faced data to improve speed!
              unset($data[$data_key]);
            }

            $user_can_login = educare_check_status('user_profiles');
            
            if ($roles == 'results' || $user_can_login != 'checked') {
              unset($status['error_creating_user']);
            }

            $msgs = '';

            foreach ($status as $key => $value) {
              if ($key == 'total') {
                $class = 'total';
              } elseif ($key == 'success') {
                $class = 'success';
              } else {
                if ($value) {
                  $class = 'error';
                } else {
                  $class = '';
                }
                
              }

              $msgs .= '<p class="'.esc_attr($class).'">'.esc_html(str_replace('_', ' ', ucfirst($key))).': <b>'.esc_html($value).'</b></p>';
            }
            
            echo educare_show_msg($msgs);

            if (isset($status['empty']) && $status['empty'] > 0 ) {
              $required_fields = educare_required_fields($roles);

              $empty_msgs = '<b>Error to import '. esc_html($status['empty']) . ' ' . $roles . ':</b> Because, one or more of their required fields ('.esc_html(implode(', ', $required_fields)).') are empty';

              if (key_exists('error_creating_user', $status)) {
                $empty_msgs .= ' Currently Educare settings allow user login (profiles) system. And you are tyring to import ' . $roles . ' without - User login (name), Email and Password. Please note, when you try to import students and teachers from the demo file, you will need to input these fields manually. Otherwise, you can disable <a href="/wp-admin/admin.php?page=educare-settings&menu=Security" target="_blank">User Profiles</a> from Educare settings to resolve this issue.';
              }
              
              echo educare_show_msg($empty_msgs, false);
            }
          }
        } else {
          // notify users if empty files or invalid extension
          echo educare_show_msg('File not upload yet');
        }
      } else {
        echo educare_show_msg('The file extension is not valid. To import the data, you need to use Excel files with either <b>.xlsx</b>, <b>.xlsm</b> or <b>.xls</b> extension. Please select a file with one of these extensions.', false);
      }

    } else {
      echo educare_show_msg('No file chosen! Please select a files', false);
    }
  }

  echo educare_guide_for("Notes: Please carefully fill out all the details of your import files. If you miss one, you may have problems to import the data. So, verify the student's admission form well and then give all the details in your import files. So, don't miss all of this required or default field! (Sometimes it may be different based on Educare settings.)");

  ?>
  <form class="add_results" method="post" action="<?php esc_url($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
    <div class="content">
      <!-- <input type="file" name="xlsx_file"><br> -->
      <label for="file_selector_box" class="drop-container" id="dropcontainer">
        <span class="drop-title">Drop files here</span>
        <p>or</p>
        <input type="file" id="file_selector_box" name="xlsx_file" accept=".xlsx, .xlsm, .xls">
        <p>Files must be an Excel files with the extensions <b>.xlsx</b>, <b>.xlsm</b> or <b>.xls</b></p>
      </label>

      <button class="educare_button" type="submit" name="import_data"><i class="dashicons dashicons-database-import"></i> Import</button>

      <?php
      // Security nonce for this form.
      $nonce = wp_create_nonce( 'educare_import_data' );
      echo '<input type="hidden" name="nonce" value="'.esc_attr($nonce).'">';
      ?>

    </div>
  </form>

  <div class='demo'>
  <?php 
    echo educare_guide_for("If you don't know, how to create an import file. Don't worry! Educare will help you do this. Create and download the demo file by clicking the button below.");

    echo '<div style="display: none !important;">';
    educare_get_forms($roles);
    echo '</div>';

    // Security nonce for this form.
    $demo_nonce = wp_create_nonce( 'educare_demo_data' );
    ?>

    <!-- Default value -->
    <div class="educare_data_field">
      <div class="educareImportDemo_demo_nonce" data-value="<?php echo esc_attr($demo_nonce);?>"></div>
    </div>
    
    <div class="select add-subject">
      <div>
        <p>Total <?php echo esc_html($roles)?>:</p>
        <select id="total_demo" name="total_demo" class="form-control">
          <?php 
          for ($i=0; $i < 105; $i+=5) {
            if ($i == 0) {
              // echo '<option value="'.esc_attr( $i ).'">Head only</option>';
              continue;
            }

            echo '<option value="'.esc_attr( $i ).'">'.esc_html( $i ).'</option>';
          }
          ?>
        </select>
      </div>
    </div>
    
    <div id="demo_data"></div>
      <p><button id="download_demo" class='educare_button' title='Download Import Demo.csv Error'><i class='dashicons dashicons-download'></i> Create Demo File</button></p>
  </div>
  <?php
}



/**
 * Generates and serves a demo Excel file for importing student data.
 *
 * @since 1.0.0
 * @last-update 1.0.0
 * 
 * @return void Generates a demo Excel file and provides a download link for it.
 */
function educare_import_demo() {
  // Verify the nonce to ensure the request originated from the expected source
  educare_verify_nonce('educare_demo_data');

  // Get and sanitize total demo count
  $total_demo = sanitize_text_field($_POST['total_demo']);

  // Parse POST data into $_POST
  wp_parse_str($_POST['form_data'], $_POST);
  $roles = sanitize_text_field($_POST['roles']);
  unset($_POST['roles'], $_POST['educare_attachment_url']);

  // Create an array to hold demo data
  $demo_data = array();

  // Populate demo data sections
  if (isset($_POST['default'])) {
    $demo_data['Student Info'] = $_POST['default'];
  }
  if (isset($_POST['Details'])) {
    $demo_data['Details'] = $_POST['Details'];
  }
  if (isset($_POST['Subject'][0])) {
    $demo_data['Subject'] = $_POST['Subject'][0];
  }
  if (isset($_POST['Others']['Rattings'])) {
    $demo_data['Rattings'] = $_POST['Others']['Rattings'];
  }
  if (isset($_POST['Others']['remarks'])) {
    $demo_data['remarks'] = $_POST['Others']['remarks'];
  }

  // Create an array of demo data for each iteration
  $demo_demo_data = array();

  for ($i = 0; $i < $total_demo; $i++) {
    $demo_demo_data[$i] = $demo_data;
  }

  // Generate and export the demo Excel file
  educare_export_data($demo_demo_data, $roles);

  // Display success message and download link
  echo educare_show_msg('Successfully generated demo file for ' . $roles . '. Total ' . $total_demo . ' ' . $roles . ' have been added to this file. You can modify or follow this file structure to create your own import files.<br><strong>Notes:</strong> This file is created based on your current settings (class templates, additional fields...). If you make any changes to the Educare (plugin) settings, this demo file may not work. For this, you need to modify or re-create this file.');

  // Escaping URL and concatenating safely
  $demoFileURL = EDUCARE_IMPORT_SYSTEM_URL . 'assets/demo-files/demo-file-for-' . esc_attr($roles) . '.xlsx';

  // Display download link
  echo '<a class="button" href="' . esc_url($demoFileURL) . '" download>Download Demo File</a>';
  die;
}

// Hook the function to an AJAX action
add_action('wp_ajax_educare_import_demo', 'educare_import_demo');

?>
