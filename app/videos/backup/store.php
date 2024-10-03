<?php
  ini_set('display_errors', 1); 
  require '../../includes/db-config.php';

  $allowedExts = array("mp4");
  $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
  
  if(($_FILES["file"]["type"] == "video/mp4") && ($_FILES["file"]["size"] < 1073741824) && in_array($extension, $allowedExts) && ($_FILES["file"]["error"] == 0)) {
    // if ($_FILES["file"]["error"] > 0) {
    //   echo json_encode(['status'=>400, 'message'=> "Return Code: " . $_FILES["file"]["error"] . "<br />"]);
    // } else {
    $unit_id = intval($_POST['unit_id']);
    $syllabus_id = intval($_POST['syllabus_id']);
    $sem = intval($_POST['sem']);
      $path = "../../uploads/videos/" . $_FILES["file"]["name"];
      $path_save = "uploads/videos";
      $file_name = $_FILES["file"]["name"];
      // if (file_exists($path)) {
      //   echo json_encode(['status'=>400, 'message'=> $_FILES["file"]["name"] . " already exists. "]);
      // } else {
        move_uploaded_file($_FILES["file"]["tmp_name"],
        $path);

        $add = $conn->query("INSERT INTO `Videos`(`File_Name`, `Type`, `Path`, `Subject_ID`, `Unit_ID`, `Sem`) VALUES ('".$file_name."', '".$extension."', '".$path_save."', $syllabus_id, $unit_id, $sem ) ");

        if($add){
          echo json_encode(['status'=>200, 'message'=> "Stored in: " . "/uploads/store" . $_FILES["file"]["name"]]);
        }else{
          echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
        }
      // }
    // }
  } else {
    echo json_encode(['status'=>400, 'message'=>'Invalid file!!']);
  }
