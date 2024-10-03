<?php
  ini_set('display_errors', 1); 
  require '../../../../includes/db-config.php';
  if(isset($_POST['student_id']) && isset($_POST['assessment_id']) && isset($_POST['e_book_id']) && isset($_POST['suject_id']) && isset($_POST['unit_id'])){
    $student_id = intval($_POST['student_id']);
    $assessment_id = intval($_POST['assessment_id']);
    $e_book_id = intval($_POST['e_book_id']);
    $suject_id = intval($_POST['suject_id']);
    $unit_id = intval($_POST['unit_id']);
    $check_Students_Assessments = $conn->query("SELECT ID FROM Students_Assessments WHERE Assessment_ID = $assessment_id  AND Student_ID = $student_id");
    if($check_Students_Assessments->num_rows > 0){
      $updated = $conn->query("UPDATE Students_Assessments SET E_Book_ID = $e_book_id WHERE Assessment_ID = $assessment_id  AND Student_ID = $student_id");
    }else{
      $add = $conn->query("INSERT INTO `Students_Assessments`(`Assessment_ID`, `Student_ID`, `Video_ID`, `E_Book_ID`, `Subject_ID`, `Unit_ID`, `Status`) VALUES ( $assessment_id, $student_id, null, $e_book_id, $suject_id, $unit_id, 1)");
    }
    

    if($add || $updated){
      echo json_encode(['status'=>200, 'message'=> "Added succefully"]);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
    }
  } else {
    echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
  }
