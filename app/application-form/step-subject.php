<?php
  ini_set('display_errors', 1); 

function customExit($msg = ""){
    echo $msg;
    exit();
}

if (isset($_POST['inserted_id']) && isset($_POST['subjects'])) {
    require '../../includes/db-config.php';
    include '../../includes/helpers.php';
    session_start();
    try{
        $inserted_id = intval($_POST['inserted_id']);
        if (empty($inserted_id)) {
            customExit(json_encode(['status' => 400, 'message' => 'ID is required.']));
        }
      
    	$optional_subject = $_POST['subjects_optional'] ?? [];
      	$elsective_subjects = $_POST['subjects_elective'] ?? [];
      	$language_subjects = $_POST['language_subjects'] ?? [];
      	if(count($language_subjects) != 3){
            echo json_encode(['status' => 400, 'message' => 'Language Subjects can not be Greater OR Less than three!']);
  			exit();
        }
      
        $subjects_arrars = array_merge($elsective_subjects, $optional_subject, $language_subjects, $_POST["subjects"]);
      	$subjects = $subjects_arrars ?? [];
        if(empty($subjects)){
            customExit(json_encode(['status' => 400, 'message' => 'Subjects are required.']));
        }
        
        if(count($_POST["subjects"])<>3){
            exit(json_encode(['status' => 400, 'message' => 'Other Subjects can not be Greater OR Less than three!']));
        }
    
        $values = "";
        foreach($subjects as $subject_id){
          	if(!$subject_id){
              continue;
            }else{
              $values .= "($inserted_id, $subject_id),";
              
            }
            
        }
    
        $values = substr($values, 0, -1,);
    
        // Delete Old Data
        $sql_query = "DELETE FROM Student_Subjects WHERE Student_Id = $inserted_id";
      
        $res = $conn->query($sql_query);
        if(!$res){
            customExit(json_encode(['status' => false, 'message' => 'Something went wrong while updating subjects.']));
        }
        // Insert New/Updated Subjects
        $sql_query = "INSERT INTO Student_Subjects (Student_Id, Subject_Id) VALUES $values";
        $result = $conn->query($sql_query);
        if($result){
            generateStudentLedger($conn, $inserted_id);
            customExit(json_encode(['status' => 200, 'message' => 'Subjects added successfully']));
        }else{
            customExit(json_encode(['status' => false, 'message' => 'Something went wrong']));
        }

    }catch(Exception $e){
        customExit(json_encode(['status' => false, 'message' => 'Something went wrong', 'exception' => $e->getMessage()]));
    }
} else {
  echo json_encode(['status' => 400, 'message' => 'Please add subjects!']);
  exit();
}