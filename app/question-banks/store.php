<?php
ini_set('display_errors', 1); 
require '../../includes/db-config.php';

$allowedExts = array("pdf", "jpeg", "gif", "png");
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

$course_id = intval($_POST['course_id']);
$subject_id = intval($_POST['subject_id']);
$file_path="";
$file_type=$extension;
$created_by = 1;
$created_at = date("Y-m-d:H:i:s");
$title = $_POST['title'];

if(isset($_FILES["file"]["name"]) && $_FILES["file"]["name"]!=''){
    $temp = explode(".", $_FILES["file"]["name"]);
    $filename  =  $temp[0].'_'.time().'.' . end($temp);
    $path = "../../uploads/question_banks/" . $filename;
    $file_path = "uploads/question_banks/". $filename;
    move_uploaded_file($_FILES["file"]["tmp_name"],
      $path);
}else{
  echo json_encode(['status'=>403, 'message'=>'File is mandatory.']);
  exit();
}

  $add = $conn->query("INSERT INTO `question_banks`(`course_id`, `subject_id`, `file_path`, `file_type`,`title`, `created_by`, `created_at`) VALUES ('".$course_id."', '".$subject_id."', '".$file_path."', '".$file_type."','".$title."', '".$created_by."', '".$created_at."' ) ");

  if($add){
    echo json_encode(['status'=>200, 'message'=> "E-book uploaded succefully!!"]);
  }else{
    echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
  }


// if (($_FILES["file"]["type"] == "application/pdf") && ($_FILES["file"]["size"] < 1073741824) && in_array($extension, $allowedExts)) {

//   if ($_FILES["file"]["error"] > 0) {
//     echo json_encode(['status'=>400, 'message'=> "Return Code: " . $_FILES["file"]["error"] . "<br />"]);
//   } else {

//     $temp = explode(".", $_FILES["file"]["name"]);
//     $filename  =  $temp[0].'_'.time().'.' . end($temp);
//     $path = "../../uploads/e-books/" . $filename;
//     $file_path = "uploads/e-books/". $filename;
//     move_uploaded_file($_FILES["file"]["tmp_name"],
//       $path);

//     $add = $conn->query("INSERT INTO `e_books`(`course_id`, `subject_id`, `file_path`, `file_type`, `created_by`, `created_at`) VALUES ('".$course_id."', '".$subject_id."', '".$file_path."', '".$file_type."', '".$created_by."', '".$created_at."' ) ");

//     if($add){
//       echo json_encode(['status'=>200, 'message'=> "E-book uploaded succefully!!"]);
//     }else{
//       echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
//     }
  
//   }
// } else {
//   echo json_encode(['status'=>400, 'message'=>'Invalid file!!']);
// }
