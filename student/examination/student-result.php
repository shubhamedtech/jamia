

 
<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
require '../../includes/helpers.php';

session_start();
$username = $_GET['user_id'];
$password  = $_GET['password'];
$url = "https://board.juaonline.in/";
$passFail = "PASS";

$student = $conn->query("SELECT Students.ID,Students.OA_Number,CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name) as Name, Students.Father_Name, Students.Enrollment_No,Students.DOB,Students.Duration, Courses.Name as Course, Sub_Courses.Name as Sub_Course, Course_Types.Name as Course_Type, Admission_Sessions.Name as Admission_Session, Admission_Types.Name as Admission_Type, CONCAT(Courses.Short_Name, ' (',Sub_Courses.Name,')') as Course_Sub_Course, TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS Age FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.Unique_ID LIKE '$username' AND UPPER(DATE_FORMAT(Students.DOB, '%d%b%Y')) = '$password' AND Students.Step = 4 AND Students.Status = 1 ");
if ($student->num_rows > 0) {
  $student = $student->fetch_assoc();
  $get_result_sql = $conn->query("SELECT s.Name,s.min_marks,s.max_marks, rm.obt_marks_ext,rm.obt_marks_int,rm.obt_marks, rm.remarks,rm.status FROM result_marks AS rm LEFT JOIN results AS r ON rm.result_id = r.id LEFT JOIN Subjects AS s ON rm.subject_id=s.id WHERE r.student_id = '" . $student['ID'] . "' ORDER BY s.ID ASC");
  if($get_result_sql->num_rows==0){
    echo json_encode(array("message" => "Result Not Published Yet.", "status" => 0));die;
  }
  $obt_total_ext_marks = array();
  $student_results = array();
  $grand_theory_marks = array();
  $grand_practical_marks = array();
  $max_marks = array();
  $remarks = array();
  $get_obt_total_ext_marks = NULL;
  $grand_max_marks = array();

  while ($row = $get_result_sql->fetch_assoc()) {
    $obt_total_ext_marks[] = $row['obt_marks_ext'] + $row['obt_marks_int'];
    $student_results[] = $row;
    $grand_max_marks[] = $row['max_marks'];
    $grand_theory_marks[] = $row['obt_marks_ext'];
    $grand_practical_marks[] = $row['obt_marks_int'];
    if ($row['obt_marks'] > $row['min_marks']) {
      $remark = "Pass";
    } else {
      $remark = "Fail";
    }
    $remarks[] = $remark;
  }
  $failIndex = array_search("Fail", $remarks);

  if ($failIndex !== false) {
    $student['remark'] = $remarks[$failIndex]; // Output: Fail
  } else {
    $student['sub_remark'] = "Pass";
  }

  $student['student_results'] = $student_results;
  $student['grand_total'] = array_sum($obt_total_ext_marks);
  $student['grand_practical_marks'] = array_sum($grand_practical_marks);
  $student['grand_theory_marks'] = array_sum($grand_theory_marks);
  $student['max_marks'] = 100;
  $student['grand_max_marks'] = array_sum($grand_max_marks);

  if ($student['grand_total'] != 0) {
    $student['persentage'] = number_format($student['grand_total'] * 100 / $student['grand_max_marks'], 2);
  } else {
    $student['persentage'] = 0;
  }

  $percentage = $student['persentage'];

  if ($percentage < 33) {
    $division = "Fail";
  } elseif ($percentage == 33 || $percentage > 33 && $percentage < 45) {
    $division = "IIIrd Division";
  } elseif ($percentage == 45 || $percentage > 45 && $percentage < 60) {
    $division = "IInd Division";
  } elseif ($percentage == 60 || $percentage > 60 && $percentage < 75) {
    $division = "Ist Division";
  } else {
    $division = "Distinction";
  }
  $student['division'] = $division;
  $student['status'] = "200";

//echo "<pre>";
 // print_r($student);die;

  echo json_encode($student['data'] = $student);
} else {
  echo json_encode(array("message" => "Invalid username or password", "status" => 1));
}


$conn->close();
?>