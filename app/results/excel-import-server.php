<?php
//if (isset($_FILES['file'])) {
  require '../../includes/db-config.php';
  require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
  require('../../extras/vendor/nuovo/spreadsheet-reader/php-excel-reader/excel_reader2.php');
  require('../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');

  session_start();

  $export_data = array();

if(isset($_FILES["file"]["name"]) && $_FILES["file"]["name"]!=''){
   
  // Header
  $header = array('Unique ID','Student Name', 'Course', 'Subject Name', 'Subject Code', 'Obtained mark Internal','Obtained mark Externel', 'Obtained mark total', 'Status', 'Remarks');
  
  $export_data[] = $header;

  $mimes = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

  if (in_array($_FILES["file"]["type"], $mimes)) {
    // Upload File
    $uploadFilePath = basename($_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

    // Read File
    $reader = new SpreadsheetReader($uploadFilePath);
    $i=0;
    $result_id=0;
    foreach ($reader as $row) {
       // Data
       if($i>0 && isset($row[0]) && $row[0]!=" "){
          $uniqueId = (isset($row[0])) ? mysqli_real_escape_string($conn, $row[0]): " ";
          $studentName = (isset($row[1])) ? mysqli_real_escape_string($conn, $row[1]) :" ";
          $course = (isset($row[2])) ? mysqli_real_escape_string($conn, $row[2]) :" ";
          $subject_name = (isset($row[3])) ? mysqli_real_escape_string($conn,$row[3]) :" ";
          $subject_code = (isset($row[4])) ? $row[4] :" ";
          $obt_marks_int = (isset($row[5])) ? intval($row[5]) :" ";
          $obt_marks_ext = (isset($row[6])) ? intval($row[6]) :" ";
          $obt_mark = (isset($row[7])) ? intval($row[7]) :" ";
          $status = (isset($row[8])) ? intval($row[8]) :" ";
          $remark = (isset($row[9])) ? mysqli_real_escape_string($conn,$row[9]) :" ";

          $created_at = date("Y-m-d:H:i:s");
          $creator=$_SESSION['ID'];
          $student_id=0;
          $course_id=0;
          $subject_id=0;


          $student = $conn->query("SELECT ID,Course_ID FROM Students WHERE Unique_ID = '".$uniqueId."'  "); //AND concat_ws(' ',First_Name,Middle_Name,Last_Name ) LIKE '" . $studentName . "'
          if ($student->num_rows == 0) {
            $export_data[] = array_merge($row, ['Student not found !']);
            continue;
          }

          $student = $student->fetch_assoc();
          $student_id = $student['ID'];

          

           // $course = $conn->query("SELECT ID FROM Courses WHERE  (Name LIKE '".$course."' OR Short_Name LIKE '".$course."')");
          // if ($course->num_rows == 0) {
          //   $export_data[] = array_merge($row, ['Course not found!']);
          //   continue;
          // }
          // $course = $course->fetch_assoc();
          // if ($course['ID'] !=$student['Course_ID']) {
          //   $export_data[] = array_merge($row, ['Course does not matched matched!']);
          //   continue;
          // }
          // $course_id = $course['ID'];

          $course_id=$student['Course_ID'];

          $get_subject_query = $conn->query("SELECT s.ID, s.Name as subject_name FROM Student_Subjects left join Subjects AS s on Student_Subjects.Subject_id=s.ID  WHERE Student_Subjects.Student_id='".$student_id."' ");
          $subjects=array();
          if ($get_subject_query->num_rows > 0) {
            while ($row1 = $get_subject_query->fetch_assoc()) {
              $subjects[]=$row1;
            }
          }

         
          if(!in_array($subject_name, array_column($subjects, 'subject_name'))){
            $export_data[] = array_merge($row, ['Subject not found !']);
            continue;
          }else{
            $key = array_search($subject_name, array_column($subjects, 'subject_name'));
            $subject_id=$subjects[$key]['ID'];
          }

          $exists = $conn->query("SELECT id FROM results WHERE student_id = '".$student_id."' ");
          if($exists->num_rows>0){
            $exists = $exists->fetch_assoc();
            $result_id = $exists['id'];
          }else{
            $add = $conn->query("INSERT INTO `results`(`student_id`, `course_id`, `status`, `published_on`,`published_by`,`remark`) VALUES ('" . $student_id . "', '" . $course_id . "', '" . $status . "', '" . $created_at . "','" . $creator . "', '' )");
            $result_id = $conn->insert_id;
          }
          
          $existsSubject = $conn->query("SELECT id FROM result_marks WHERE result_id = '".$result_id."' AND subject_id = $subject_id");
          if($existsSubject->num_rows>0){
            $export_data[] = array_merge($row, ['Duplicate result !']);
          }else{
            $result1 = $conn->query("INSERT INTO `result_marks`(`result_id`, `subject_id`, `obt_marks_ext`, `obt_marks_int`,`obt_marks`,`status`,`remarks`, `created_at`) VALUES ('" . $result_id . "', '" . $subject_id . "', '" . $obt_marks_ext . "', '" . $obt_marks_int . "','" . $obt_mark . "', '" . $status . "', '" . $remark . "', '" . $created_at . "' )");
            if ($result1) {
              $export_data[] = array_merge($row, ['Result added successfully!']);
            } else {
              $export_data[] = array_merge($row, ['Something went wrong!']);
            }
          }
          
       }

      $i++;
    }

    unlink($uploadFilePath);
    $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('Result_status ' . date('H:i:s') . '.xlsx');

    

    // if(count($export_data)>1){
    //   echo json_encode(['status'=>502, 'data'=>$export_data, 'message'=>'some data are duplicate or not matched.']);
    //   exit();
    // }else{
    //   echo json_encode(['status'=>200, 'data'=>array(), 'message'=>'Result imported succesully.']);
    //   exit();
    // }
    
  }
}else{
  echo json_encode(['status'=>403, 'data'=>array(), 'message'=>'File is mandatory.']);
  exit();
}

//}


?>