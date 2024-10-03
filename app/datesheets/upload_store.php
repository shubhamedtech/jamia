<?php
ini_set('display_errors', 1);
if (isset($_FILES['file'])) {
  require '../../includes/db-config.php';
  session_start();

  $delimiter = ",";
  $filename = "Date-Sheet Status.csv";


  // Create a file pointer 
  $f = fopen('php://memory', 'w');

  $fields = array('Exam Session', 'Course Name', 'Paper Name', 'Exam Date (dd-mm-yyyy)', 'Start Time', 'End Time', 'Remark');
  fputcsv($f, $fields, $delimiter);

  $allowed_extensions = array(
    'text/x-comma-separated-values',
    'text/comma-separated-values',
    'application/octet-stream',
    'application/vnd.ms-excel',
    'application/x-csv',
    'text/x-csv',
    'text/csv',
    'application/csv',
    'application/excel',
    'application/vnd.msexcel',
    'text/plain'
  );

  if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $allowed_extensions)) {
    // Open uploaded CSV file with read-only mode
    $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

    // Skip the first line
    fgetcsv($csvFile);

    // Parse data from CSV file line by line
    // Parse data from CSV file line by line
    while (($row = fgetcsv($csvFile, 10000, ",")) !== FALSE) {
      // Get row data
      $exam_session = $row[0];
      $course_name = $row[1];
      $paper_name = $row[2];
      $exam_date = $row[3];
      $start_time = $row[4];
      $end_time = $row[5];

      $exam_session = $conn->query("SELECT ID FROM Exam_Sessions WHERE Name = '$exam_session' AND University_ID = " . $_SESSION['university_id'] . "");
      if ($exam_session->num_rows == 0) {
        $row = array_merge($row, ['Exam Session not found!']);
        continue;
      }

      $exam_session = $exam_session->fetch_assoc();
      $exam_session = $exam_session['ID'];

      $subject_code = $conn->query("SELECT `id` from `Subjects` WHERE `Name`='$paper_name' and `Program_Grade_ID` = (SELECT `id` from `Courses` WHERE `Name` = '$course_name')");

      if ($subject_code->num_rows == 0) {
        $row = array_merge($row, ['Subject code not found']);
        continue;
      }

      $subject_code = $subject_code->fetch_assoc();
      $subject_code = $subject_code['id'];

      $exam_date = date("Y-m-d", strtotime($exam_date));

      $check = $conn->query("SELECT ID FROM Date_Sheets WHERE Exam_Session_ID = $exam_session AND Syllabus_ID = $subject_code");
      if ($check->num_rows == 0) {
        $conn->query('SET foreign_key_checks = 0');
        $add = $conn->query("INSERT INTO Date_Sheets (`Exam_Session_ID`, `Syllabus_ID`, `University_ID`, `Exam_Date`, `Start_Time`, `End_Time`) VALUES ($exam_session, $subject_code, " . $_SESSION['university_id'] . ", '$exam_date', '$start_time', '$end_time')");
        $conn->query('SET foreign_key_checks = 1');
        if ($add) {
          $row = array_merge($row, ['Date Sheet added successfully!']);
        } else {
          $row = array_merge($row, ['Something went wrong!']);
        }
      } else {
        $row = array_merge($row, ['Date Sheet already exists!']);
      }

      fputcsv($f, $row, $delimiter);
    }

    // Close opened CSV file
    fclose($csvFile);
  } else {
    $row['Inavlid File'];
    fputcsv($f, $row, $delimiter);
  }

  fseek($f, 0);
  // Set headers to download file rather than displayed 
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '";');


  //output all remaining data on a file pointer 
  fpassthru($f);
}
