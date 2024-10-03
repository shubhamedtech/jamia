<?php
  ini_set('display_errors', 1); 
  require '../../../../includes/db-config.php';
  if(isset($_POST['student_id']) && isset($_POST['assessment_id']) && isset($_POST['video_id']) && isset($_POST['suject_id']) && isset($_POST['unit_id'])){
    $student_id = intval($_POST['student_id']);
    $assessment_id = intval($_POST['assessment_id']);
    $video_id = intval($_POST['video_id']);
    $suject_id = intval($_POST['suject_id']);
    $unit_id = intval($_POST['unit_id']);
    
    $add = $conn->query("INSERT INTO `Students_Assessments`(`Assessment_ID`, `Student_ID`, `Video_ID`, `E_Book_ID`, `Subject_ID`, `Unit_ID`, `Status`) VALUES ( $assessment_id, $student_id, $video_id, null,$suject_id, $unit_id, 1)");

    if($add){
      echo json_encode(['status'=>200, 'message'=> "Added succefully"]);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
    }
  } else {
    echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
  }
