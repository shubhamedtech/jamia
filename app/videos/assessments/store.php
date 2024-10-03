<?php
  ini_set('display_errors', 1); 
  session_start();
  require '../../../includes/db-config.php';
  require('../../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $allowedExts = array("csv", "text/csv", "jpg", "jpeg", "gif", "png", "mp3", "mp4", "wma");
  $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (($_FILES["file"]["type"] == "text/csv") && ($_FILES["file"]["size"] < 1073741824) && in_array($extension, $allowedExts)) {
  if ($_FILES["file"]["error"] > 0) {
    echo json_encode(['status'=>400, 'message'=> "Return Code: " . $_FILES["file"]["error"] . "<br />"]);
  } else {
    $unit_id = intval($_POST['unit_id']);
    $syllabus_id = intval($_POST['syllabus_id']);
    $video_id = intval($_POST['video_id']);
    $path = "../../../uploads/assessment/" . $_FILES["file"]["name"];
    $path_save = "uploads/assessment";
    $file_name = $_FILES["file"]["name"];

    $add = $conn->query("INSERT INTO `Video_Assessments`(`Video_id`, `Subject_ID`, `Unit_ID`, `File_Name`, `Type`, `Path`, `Created_at`) VALUES ($video_id, $syllabus_id, $unit_id, '".$file_name."', '".$extension."', '".$path_save."', now() ) ");

    $export_data = array();

    $fields = array('Question', 'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Answer', 'Marks', 'Remark');
    $export_data[] = $fields;

    $mimes = array('text/csv');

    if (in_array($_FILES["file"]["type"], $mimes)) {
      // Upload File
      $file_data = fopen($_FILES['file']['tmp_name'], 'r');
      fgetcsv($file_data);
      $counter = 1;
      while ($row = fgetcsv($file_data)) {
        // Data
        $remark = [];
        $question = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[0])), "UTF-8");
        $option1 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[1])), "UTF-8");
        $option2 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[2])), "UTF-8");
        $option3 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[3])), "UTF-8");
        $option4 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[4])), "UTF-8");
        $answer = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[5])), "UTF-8");
        $marks = intval($row[6]);

        $row = array($question, $option1, $option2, $option3, $option4, $answer, $marks);

        if (empty($question)) {
          $export_data[] = array_merge($row, ['Question cannot be empty!']);
          continue;
        }

        if (empty($answer)) {
          $export_data[] = array_merge($row, ['Answer cannot be empty!']);
          continue;
        }

        if (empty($marks)) {
          $export_data[] = array_merge($row, ['Marks cannot be empty!']);
          continue;
        }

        $options = array($option1, $option2, $option3, $option4);
        $options = array_filter($options);
        $options = mysqli_real_escape_string($conn, json_encode($options));
        $assessment_id = $conn->query("SELECT ID FROM Video_Assessments WHERE video_id = $video_id AND Subject_ID = $syllabus_id  AND Unit_ID = $unit_id ORDER BY ID DESC LIMIT 1");
        $assessment_id = mysqli_fetch_assoc($assessment_id);
        $assessment_id= $assessment_id['ID'];
        $add = $conn->query("INSERT INTO `Video_Assessment_Questions` (`Assessment_ID`, `Unit_ID`, `Video_id`, `Syllabus_ID`, `Question_No`, `Question`, `Options`, `Answer`, `Marks`, `Created_at`) VALUES ($assessment_id, $unit_id, $video_id, $syllabus_id, '$counter', '$question', '$options', '$answer', '$marks', now())");
        if ($add) {
          $export_data[] = array_merge($row, ['Question updated successfully!']);
        } else {
          $export_data[] = array_merge($row, ['Something went wrong!']);
        }
        $counter++;
      }
    }
    
    move_uploaded_file($_FILES["file"]["tmp_name"], $path);
    // $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('Question Status.xlsx');
    if($add){
      echo json_encode(['status'=>200, 'message'=> "Stored in: " . "/uploads/assessment" . $_FILES["file"]["name"]]);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
    }
  }
} else {
    echo json_encode(['status'=>400, 'message'=>'Invalid file!!']);
}

