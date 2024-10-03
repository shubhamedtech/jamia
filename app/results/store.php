<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
session_start();

// echo "<pre>";print_r($_POST);die;
//list($student_id, $duration, $university_id, $enrollment_no) = explode('|', $_POST['student_id']);
//$updated_enrollment_no = isset($_POST['enrollment_no']) ? $_POST['enrollment_no'] : '';

$created_at = date("Y-m-d:H:i:s");
$obt_marks = array_filter($_POST['obt_marks']);
$subject_ids = array_filter($_POST['subject_id']);
$max_marks_ints = array_filter($_POST['max_marks_int']);
$max_marks_exts = array_filter($_POST['max_marks_ext']);
$remarks = array_filter($_POST['remarks']);
$creator=$_SESSION['ID'];
$student_id=$_POST['student_id'];
$course_id=$_POST['course_id'];


$res = $conn->query("SELECT * FROM `results` WHERE student_id=$student_id AND course_id=$course_id");
if($res->num_rows>0){
    echo json_encode(['status' => 302, 'message' => "Result already exist!"]);
    die;
}

$status=0;
$result = $conn->query("INSERT INTO `results`(`student_id`, `course_id`, `status`, `published_on`,`published_by`,`remark`) VALUES ('" . $student_id . "', '" . $course_id . "', '" . $status . "', '" . $created_at . "','" . $creator . "', '' )");
$result_id=0;
if($result){
    $result_id = $conn->insert_id;
}

$inserted_count = 0;
$updated_count = 0;

foreach ($obt_marks as $i => $obt_mark) {
  $subject_id = isset($subject_ids[$i]) ? $subject_ids[$i] : '';
  $obt_marks_int = isset($max_marks_ints[$i]) ? $max_marks_ints[$i] : 0;
  $obt_marks_ext = isset($max_marks_exts[$i]) ? $max_marks_exts[$i] : 0;
  $remark =isset($remarks[$i]) ? $remarks[$i] : '';

//   if ($updated_enrollment_no) {
//     $result = $conn->query("UPDATE `marksheets` SET `max_marks_ext` = '" . $max_marks_ext . "', `max_marks_int` = '" . $max_marks_int . "',  `obt_marks` = '" . $obt_mark . "', `created_at` = '" . $created_at . "' WHERE `enrollment_no` = '" . $updated_enrollment_no . "' AND `subject_id` = '" . $subject_id . "'");
//     if ($result) {
//       $updated_count++;
//     }
//   } else {
   
    $result1 = $conn->query("INSERT INTO `result_marks`(`result_id`, `subject_id`, `obt_marks_ext`, `obt_marks_int`,`obt_marks`,`status`,`remarks`, `created_at`) VALUES ('" . $result_id . "', '" . $subject_id . "', '" . $obt_marks_ext . "', '" . $obt_marks_int . "','" . $obt_mark . "', '1', '" . $remark . "', '" . $created_at . "' )");
    if ($result1) {
      $inserted_count++;
    }
  }
//}

if ($inserted_count > 0 || $updated_count > 0) {
  echo json_encode(['status' => 200, 'message' => "Result added succefully!!"]);
} else {
  echo json_encode(['status' => 400, 'message' => 'Something went to wrong!!']);
}
