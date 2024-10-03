<?php
  if(isset($_POST['subject_id']) && isset($_POST['name']) && isset($_POST['subject_catagory']) && isset($_POST['grade']) || isset($_POST['subject_mode']) && isset($_POST['subject_fee']) && isset($_POST['exam_fee']) ){
    require '../../includes/db-config.php';
    session_start();
    
    $grade = intval($_POST["grade"]);
    $subject_catagory = trim(mysqli_real_escape_string($conn, $_POST["subject_catagory"]));
  	$subject_type = trim(mysqli_real_escape_string($conn, $_POST["subject_type"]));
    $subject_type = ($subject_type == "Default") ? 1 : 0;
    $subject_name = trim(mysqli_real_escape_string($conn, $_POST["name"]));
    $subject_mode = trim(mysqli_real_escape_string($conn, $_POST["subject_mode"]));
    $subject_fee = intval($_POST["subject_fee"]);
    //$exam_fee = intval($_POST["exam_fee"]);
    $exam_fee = 0;
    $toc_fee = intval($_POST["toc_fee"]);
    $practical_fee = intval($_POST["practical_fee"]);
    //$registration_fee = intval($_POST["registration_fee"]);
    $registration_fee = 0;
    $subject_id = intval($_POST['subject_id']);

    $subject_fee = $subject_fee ? $subject_fee : 0;
    $exam_fee = $exam_fee ? $exam_fee : 0;
    $toc_fee = $toc_fee ? $toc_fee : 0;
    $registration_fee =  $registration_fee ?  $registration_fee : 0;
    $practical_fee = $practical_fee ? $practical_fee : 0;

    $total_fee = $subject_fee + $exam_fee + $toc_fee + $practical_fee + $registration_fee;

    $update = $conn->query("UPDATE `Subjects` SET `Name` = '$subject_name', `Program_Grade_ID` = '$grade', `Mode` = '$subject_mode', `Type` = $subject_type,  `Category` = '$subject_catagory', `Subject_Fee` = $subject_fee, `Exam_Fee` = $exam_fee, `Toc_Fee` = $toc_fee, `Practical_Fee` = $practical_fee,  `Registration_Fee` = $registration_fee, `Total_Fee` = $total_fee, `Updated_At` = now() WHERE ID = $subject_id");

    if($update){
        echo json_encode(['status'=>200, 'message'=>$subject_name.' updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }else{
    echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
  }
