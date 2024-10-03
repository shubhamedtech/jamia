<?php
  if(isset($_POST['syllabi_id']) && isset($_POST['unit_name'])){
    require '../../../includes/db-config.php';

    $syllabi_id = intval($_POST['syllabi_id']);
    $units = is_array($_POST['unit_name']) ? $_POST['unit_name'] : [];
    if(count($units) > 0){
      foreach($units as $i=>$unit){
        if(empty($units[$i])){
          echo json_encode(['status'=>400, 'message'=>'Please add unit name !!']);
          exit();
        }
      }
    }

    $syllabus = $conn->query("SELECT * FROM Syllabi WHERE ID = $syllabi_id");
    $syllabus = mysqli_fetch_assoc($syllabus);
    foreach($units as $i=>$unit){
      if(!empty($units[$i])){
        $add = $conn->query("INSERT INTO `Units`(`Syllabi_ID`, `Course_ID`, `Sub_Course_ID`, `Code`, `Unit_Name`, `Type`) VALUES (".$syllabus['ID'].", ".$syllabus['Course_ID'].", ".$syllabus['Sub_Course_ID'].", '".$syllabus['Code']."', '".$unit."', 'Video')");
      }
    }
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Unit added succesfully!!']);
    }
  }else{
    echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
  }
?>
