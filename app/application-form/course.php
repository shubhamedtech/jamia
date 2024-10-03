<?php
  ini_set('display_errors', 1); 

if (isset($_GET['university_id']) && isset($_GET['session_id']) && isset($_GET['admission_type_id']) && isset($_GET['center'])) {
  require '../../includes/db-config.php';
  session_start();

  $ids = [];
  $university_id = intval($_GET['university_id']);
  $session_id = intval($_GET['session_id']);
  $admission_type_id = intval($_GET['admission_type_id']);
  $user_id = intval($_GET['center']);
  $courses = $conn->query("SELECT Courses.ID, Courses.Name, Courses.Short_Name FROM Courses WHERE University_ID = $university_id");
  if ($courses->num_rows == 0) {
    echo '<option value="">Please configure Academics</option>';
    exit();
  }
  while ($course = $courses->fetch_assoc()) {
    echo '<option value="' . $course['ID'] . '">' . $course['Name'] . '</option>';
  }
}
