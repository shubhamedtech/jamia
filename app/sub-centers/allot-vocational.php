<?php 
  ini_set('display_errors', 1);

  // if(isset($_POST['university_id']) && isset($_POST['id']) && isset($_POST['counsellor']) && isset($_POST['fee']) && isset($_POST['course_type'])){
   //ini_set('display_errors', 1);
    if(isset($_POST['university_id']) && isset($_POST['id']) && isset($_POST['course_type'])){
    require '../../includes/db-config.php';
    session_start();

    $university_id = intval($_POST['university_id']);
    $counsellor = intval($_POST['counsellor']);
    $sub_counsellor = intval($_POST['sub_counsellor']);
    $id = intval($_POST['id']);
    $course_types = json_encode($_POST['course_type']);
    //$course_types = is_array($_POST['course_type']) ? $_POST['course_type'] : [];
    // $fees = is_array($_POST['fee']) ? $_POST['fee'] : [];
    // $fees = array_filter($fees);

    if(empty($_POST['course_type']) || empty($university_id) || empty($id) || empty($course_types)){
      echo json_encode(['status'=>403, 'message'=>'Missing required fields!']);
      exit();
    }
    $allot = '';
    $check = $conn->query("SELECT ID FROM Alloted_Center_To_Counsellor WHERE Code = $id AND University_ID = $university_id");
    //print_r($check);die;
    if($check->num_rows>0){
     // Assuming $course_types is an array
      //$course_types_string = implode(',', $course_types);
      $update_allot_counsellor = $conn->query("UPDATE Alloted_Center_To_Counsellor 
                                               SET Course_type = '$course_types'
                                         WHERE Code = $id AND University_ID = $university_id");
      $allot = 1;
    }else{
      
      $update_allot_counsellor = $conn->query("INSERT INTO Alloted_Center_To_Counsellor (`Counsellor_ID`,`Course_type`, `Code`, `University_ID`) VALUES ($counsellor,'$course_types', $id, $university_id)");
      $allot = 1;
    }


    // $conn->query("DELETE FROM Sub_Center_Course_Types WHERE `User_ID` = $id AND University_ID = $university_id");
    // foreach($course_types as $course_type){
    //   $conn->query("INSERT INTO Sub_Center_Course_Types (`User_ID`, `Course_Type_ID`, `University_ID`) VALUES ($id, $course_type, $university_id)");
    // }

    // $conn->query("DELETE FROM Sub_Center_Sub_Courses WHERE `User_ID` = $id AND University_ID = $university_id");
    // foreach ($fees as $sub_course_id=>$fee){
    //   $course_id = $conn->query("SELECT Course_ID FROM Sub_Courses WHERE ID = $sub_course_id AND University_ID = $university_id");
    //   $course_id = $course_id->fetch_assoc();
    //   $course_id = $course_id['Course_ID'];

      // $allot = $conn->query("INSERT INTO Center_SubCenter (`Center`, `Sub_Center`,) VALUES ($fee, $id, $course_id, $sub_course_id, $university_id)");
    // }

    if($allot){
     echo json_encode(['status'=>200, 'message'=>'University alloted successfully!']);
    }else{
      echo json_encode(['status'=>403, 'message'=>'Unable to allot university!']);
    }
  }
