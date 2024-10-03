<?php
  ini_set('display_errors', 1); 

require '../../includes/db-config.php';
if (isset($_POST['grade']) && isset($_POST['subject_catagory']) && isset($_POST['subject_type']) && isset($_POST['name']) && isset($_POST['subject_mode'])) {
	// Recieved variables
	$grade = intval($_POST["grade"]);
	$subject_catagory = trim(mysqli_real_escape_string($conn, $_POST["subject_catagory"]));
  	$subject_type = trim(mysqli_real_escape_string($conn, $_POST["subject_type"]));
	$subject_name = trim(mysqli_real_escape_string($conn, $_POST["name"]));
	$subject_mode = trim(mysqli_real_escape_string($conn, $_POST["subject_mode"]));
	$subject_fee = intval($_POST["subject_fee"]);
	//$exam_fee = intval($_POST["exam_fee"]);
	$toc_fee = intval($_POST["toc_fee"]);
	$practical_fee = intval($_POST["practical_fee"]);
  	//$registration_fee = intval($_POST["registration_fee"]);

	//handle Default values
	$subject_fee = $subject_fee ? $subject_fee : 0;
	$exam_fee = 0;
	$toc_fee = $toc_fee ? $toc_fee : 0;
	$practical_fee = $practical_fee ? $practical_fee : 0;
  	$subject_type = ($subject_type == "Default") ? 1 : 0;
  	$registration_fee = 0;

	$total_fee = $subject_fee + $exam_fee + $toc_fee + $practical_fee + $registration_fee;
	if (!$subject_name && $subject_type != "") {
		echo json_encode(['status' => 400, 'message' => 'Subject Name and Category is required']);
		exit();
	}
  if($subject_type == 1){
	$subjects_check = $conn->query("SELECT * FROM Subjects WHERE Program_Grade_ID = $grade AND Category = 'Language' AND Type = 1");
  	if($subjects_check->num_rows >= 3){
      echo json_encode(['status' => 400, 'message' => 'Language subjects can not be grater than three by Default']);
      exit();
    }
    
   }
   
	$add = $conn->query("INSERT INTO `Subjects`(`Name`, `Program_Grade_ID`, `Mode`,`Type`, `Category`, `Subject_Fee`, `Exam_Fee`, `Toc_Fee`, `Practical_Fee`, `Registration_Fee`, `Total_Fee`, `Created_At`, `Updated_At`) VALUES ('$subject_name', $grade, '$subject_mode', $subject_type,'$subject_catagory', $subject_fee, $exam_fee, $toc_fee, $practical_fee, $registration_fee, $total_fee, now(), now()) ");
	if ($add) {
		echo json_encode(['status' => 200, 'message' => 'Subject added successfully']);
	} else {
		echo json_encode(['status' => 400, 'message' => 'Something went wrong ']);
	}
  
} else {
	echo json_encode(['status' => 400, 'message' => 'Please fill all required data']);
}
