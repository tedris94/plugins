<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * Educare Event Calender
 * usage:
 * // Creat new event
 * $calendar = new educare_event_calendar(); // Create an event calendar
 * // Add event
 * $calendar->addEvent('2023-05-10', 'present'); // Add new event Event 1
 * // Set or change default month and year
 * $calendar->changeMonthYear('05', '2023');
 * // Display calender with event data
 * echo $calendar->displayCalendar();
 * 
 */
class educare_event_calendar {
  private $month;
  private $year;
  private $events = array();

  public function __construct() {
		// Set default month and year
    $this->month = date('m');
    $this->year = date('Y');
	}

  public function changeMonthYear($month, $year) {
    $this->month = $month;
    $this->year = $year;
  }

  public function addEvent($date, $event) {
    $this->events[$date] = $event;
  }

  public function getMonthYear($MonthYear) {
    if ($MonthYear == 'Y') {
      return $this->year;
    } else {
      return $this->month;
    }
  }

  public function displayCalendar() {
		$daysOfWeek = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		$firstDayOfMonth = date('w', strtotime($this->year . '-' . $this->month . '-01'));
		$daysInMonth = date('t', strtotime($this->year . '-' . $this->month . '-01'));
		$monthYear = date('F Y', strtotime($this->year . '-' . $this->month . '-01'));
		$currentYear = date("Y");
		$currentMonth = date("m");
		$currentDay = date("d");
		
		// Get the previous month and year
		$prevMonth = date('m', strtotime($this->year . '-' . $this->month . '-01 -1 month'));
		$prevYear = date('Y', strtotime($this->year . '-' . $this->month . '-01 -1 month'));

		// Get the next month and year
		$nextMonth = date('m', strtotime($this->year . '-' . $this->month . '-01 +1 month'));
		$nextYear = date('Y', strtotime($this->year . '-' . $this->month . '-01 +1 month'));

		$calendar = '<div id="educare-event-calendar">';
    $calendar .= '<div class="table-responsive">';
		$calendar .= '<table class="table table-bordered caption-top event-calendar">';
		// Go Previous Month
		$calendar .= '<caption class="event-header">';
    $calendar .= '<a class="prev-month change-event" href="?&month=' . $prevMonth . '&year=' . $prevYear . '" data="month=' . $prevMonth . '&year=' . $prevYear . '"><span class="dashicons dashicons-arrow-left-alt2"></span></a> ';
    $calendar .= $monthYear . ' ';
    $calendar .= '<a class="next-month change-event" href="?&month=' . $nextMonth . '&year=' . $nextYear . '" data="month=' . $prevMonth . '&year=' . $prevYear . '"><span class="dashicons dashicons-arrow-right-alt2"></span></a>';
    $calendar .= '</caption>';
		// Go Next Month
		$calendar .= '<thead class="bg-light">';
		$calendar .= '<tr>';

		foreach ($daysOfWeek as $day) {
			$calendar .= '<th>' . $day . '</th>';
		}

		$calendar .= '</tr>';
		$calendar .= '</thead>';
		$calendar .= '<tbody>';
		$dayCounter = 1;
		$calendar .= '<tr>';
		
		// Display the dates of the previous month
		for ($i = 0; $i < $firstDayOfMonth; $i++) {
			$prevMonthDays = date('t', strtotime($prevYear . '-' . $prevMonth . '-01'));
			$calendar .= '<td class="dissable prev-month">' . ($prevMonthDays - $firstDayOfMonth + $i + 1) . '</td>';
		}

		while ($dayCounter <= $daysInMonth) {
			if ($firstDayOfMonth == 7) {
				$calendar .= '</tr><tr>';
				$firstDayOfMonth = 0;
			}

			$currentDate = $this->year . '-' . $this->month . '-' . sprintf('%02d', $dayCounter);
			$event = isset($this->events[$currentDate]) ? $this->events[$currentDate] : '';
			
			if ($event) {
				$status = 'event-date';
			} else {
				$status = 'no-event';
			}

			// Current day
			if ($this->month == $currentMonth and $this->year == $currentYear and $dayCounter == $currentDay) {
				$active = 'active today';
			} else {
				$active = '';
			}

			$calendar .= '<td class="'.$status. ' ' .$active.'">';
			$calendar .= '<div class="status '.str_replace(' ', '-', $event).'">' . $dayCounter . '</div>';
			$calendar .= '</td>';
			$dayCounter++;
			$firstDayOfMonth++;
		}
		
		// Display the dates of the next month
		while ($firstDayOfMonth < 7) {
			$nextMonthDate = $firstDayOfMonth - $daysInMonth;
			$nextMonthDate = ($nextMonthDate > 0) ? $nextMonthDate : abs($nextMonthDate);
			$calendar .= '<td class="dissable next-month">' . $nextMonthDate . '</td>';
			$firstDayOfMonth++;
		}

		$calendar .= '</tr>';
		$calendar .= '</tbody>';
		$calendar .= '</table>';
    $calendar .= '</div>';
		$calendar .= '</div>';
		return $calendar;
  }

	// Need when change Prev and Next month using post or form
  public function previousMonth() {
		$prevMonth = date('m', strtotime($this->year . '-' . $this->month . '-01 -1 month'));
		$prevYear = date('Y', strtotime($this->year . '-' . $this->month . '-01 -1 month'));
		$this->month = $prevMonth;
		$this->year = $prevYear;
  }

  public function nextMonth() {
		$nextMonth = date('m', strtotime($this->year . '-' . $this->month . '-01 +1 month'));
		$nextYear = date('Y', strtotime($this->year . '-' . $this->month . '-01 +1 month'));
		$this->month = $nextMonth;
		$this->year = $nextYear;
  }
}



/**
 * Retrieve attendance records for a specific student, month, and year.
 *
 * @param int $id    The student ID.
 * @param int $month The month (numeric representation).
 * @param int $year  The year.
 *
 * @return array The attendance records for the specified criteria.
 */
function educare_get_attendance($id, $month, $year) {
	global $wpdb;
	$table = $wpdb->prefix . EDUCARE_PREFIX . 'attendance';
	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM $table WHERE `Student_ID` = %d AND MONTH(Date) = %d AND YEAR(Date) = %d",
			$id,
			$month,
			$year
		)
	);

	return $results;
}



/**
 * Displays a student's attendance using an event calendar.
 *
 * This function generates an event calendar to visualize a student's attendance data. It accepts a student identifier
 * and an optional attendance status type to focus on. The function interacts with the educare_event_calendar class to
 * display attendance records in a calendar format.
 *
 * @param int $id The identifier of the student for whom the attendance is being displayed.
 * @param string|null $status Optional. The type of attendance status to focus on (e.g., 'present', 'late', 'absent').
 * @return array|null An associative array containing the count of each attendance status if a specific status is provided,
 * otherwise, the function echoes the event calendar with attendance data.
 * 
 */
function educare_show_attendance($id, $status = null) {
	$calendar = new educare_event_calendar(); // Create an event calendar

	// Change month and year or response previous and next month button
	if (isset($_GET['month']) && isset($_GET['year'])) {
		$month = sanitize_text_field( $_GET['month'] );
		$year = sanitize_text_field( $_GET['year'] );
		$calendar->changeMonthYear($month, $year);
		$attendance = educare_get_attendance($id, $month, $year);
	} else {
		$attendance = educare_get_attendance($id, date('m'), date('Y'));
	}
	
	// Count the attendance for a specific value, such as present, late or absent
	if ($status) {
		$present = $late = $absent = 0;
		$getStatus = array (
			'present' => 0,
			'late' => 0,
			'absent' => 0,
		);

		foreach ($attendance as $student_attendance) {
			if ($student_attendance->attendance === 'present') {
				$getStatus['present'] = ++$present;
			}
			if ($student_attendance->attendance === 'late') {
				$getStatus['late'] = ++$late;
			}
			if ($student_attendance->attendance === 'absent') {
				$getStatus['absent'] = ++$absent;
			}
		}

		// echo "Attendance count for '$status': $getStatus['present']";
		return $getStatus;
	}

	if ($attendance) {
		foreach ($attendance as $key => $value) {
			$calendar->addEvent($value->Date, $value->attendance);
		}
	}

	// Display calender with attendance data
	echo $calendar->displayCalendar();
	
}

?>