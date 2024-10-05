<?php
 session_start();
require '../../../includes/db-config.php';

if($_POST['id']!='')
{
    $student_id = $_POST['id'];
    $correction_data = json_encode($_POST['remark']);
    $created_by = $_SESSION['ID'];
    $checkData = "SELECT * FROM `correction_form` WHERE `students_id`=". $student_id ;
    $existCount = $conn->query($checkData);
    if($existCount->num_rows>0)
    {
        $insertQuery = "UPDATE `correction_form` SET `correction_data`='".$correction_data."', `created_by`= $created_by WHERE `students_id`= $student_id";
    }
    else
    {
        $insertQuery = "INSERT INTO `correction_form` (`students_id`,`correction_data`,`created_by`) value ($student_id,'$correction_data',$created_by)";
    }
    $return = $conn->query($insertQuery);
    if($return)
    {
        echo json_encode(['status'=>200, 'message'=>'Application Correction store successfully!']);
    }else{
        echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
}


?>