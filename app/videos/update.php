<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
$course_id = intval($_POST['course_id']);
$subject_id = intval($_POST['subject_id']);
$unit = $_POST['unit'];
$video_categories = $_POST['videos_categories'];
$description = $_POST['description'];
$semester = $_POST['semester'];
$language_categories = $_POST['language_categories'];
$id = intval($_POST['id']);
$updated_by = 1;
$updated_at = date("Y-m-d:H:i:s");
$allowedExtsVid = array("mp4");
$allowedExtsThumb = array("pdf", "jpeg", "gif", "png", "jpg");

$file_path = "";
$file_type = "";
$thumbnail_path = "";
$thumbnail_type = "";

// Handle thumbnail upload
if (isset($_FILES["thumbnail"]["name"]) && $_FILES["thumbnail"]["name"] != '') {
  $temp1 = explode(".", $_FILES["thumbnail"]["name"]);
  $extensionThumb = end($temp1);
  if (in_array($extensionThumb, $allowedExtsThumb)) {
    $filename1 = $temp1[0] . '_' . time() . '.' . $extensionThumb;
    $path1 = "../../uploads/videos/" . $filename1;
    $thumbnail_path = str_replace("../..", "", $path1);
    if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $path1)) {
      $thumbnail_type = $extensionThumb;
    } else {
      echo json_encode(['status' => 400, 'message' => 'Thumbnail upload failed']);
      exit();
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Invalid thumbnail file type']);
    exit();
  }
}

// Handle YouTube URL update
if (isset($_POST["uniform"]) && $_POST["uniform"] != '') {
  $youtube_url = $_POST["uniform"];
  $file_path = str_replace("../..", "", $youtube_url);
  $file_type = 'url';
}

// Handle video file upload
if (isset($_FILES["video_file"]["name"]) && $_FILES["video_file"]["name"] != '') {
  $temp = explode(".", $_FILES["video_file"]["name"]);
  $extensionVid = end($temp);
  if ($_FILES["video_file"]["type"] == "video/mp4" && $_FILES["video_file"]["size"] < 1073741824 && in_array($extensionVid, $allowedExtsVid) && $_FILES["video_file"]["error"] == 0) {
    $filename = $temp[0] . '_' . time() . '.' . $extensionVid;
    $path = "../../uploads/videos/" . $filename;
    $file_path = str_replace("../..", "", $path);
    $file_type = 'mp4';

    if (!move_uploaded_file($_FILES["video_file"]["tmp_name"], $path)) {
      echo json_encode(['status' => 400, 'message' => 'Video Upload failed']);
      exit();
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Invalid video file!']);
    exit();
  }
}

// Update all columns in one query
$updateQuery = "UPDATE `video_lectures` SET 
    `Videos_Categories`='$video_categories',
    `Languages_Categories`='$language_categories',
    `unit`='$unit', 
    `description`='$description', 
    `semester`='$semester', 
    `course_id`='$course_id', 
    `subject_id`='$subject_id', 
    `updated_by`='$updated_by', 
    `updated_at`='$updated_at'";
if (!empty($thumbnail_path)) {
  $updateQuery .= ", `thumbnail_url`='$thumbnail_path', `thumbnail_type`='$thumbnail_type'";
}
if (!empty($file_path)) {
  $updateQuery .= ", `video_url`='$file_path', `video_type`='$file_type'";
}
$updateQuery .= " WHERE id = '$id'";
$update = $conn->query($updateQuery);
if ($update) {
  echo json_encode(['status' => 200, 'message' => "Video Updated Successfully"]);
  // header('location:/../../lms-settings/videos');
} else {
  echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
}
