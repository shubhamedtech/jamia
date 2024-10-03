<?php
ini_set('display_errors', 1); 
require '../../includes/db-config.php';

$course_id = intval($_POST['course_id']);

$studentSubjects = "SELECT Subjects.`ID` as subject_id,Subjects.Name from Subjects WHERE Subjects.Program_Grade_ID = $course_id ";
$Subjects = mysqli_query($conn, $studentSubjects);

$html='<option value="">Select</option>';
while ($row = mysqli_fetch_assoc($Subjects)) {
  $html = $html.'<option value="'.$row['subject_id'].'">'.$row['Name'].'</option>';
}

echo $html; die;

?>