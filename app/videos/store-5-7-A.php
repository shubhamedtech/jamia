
<?php
require '../../includes/db-config.php';
$allowedExtsVid = array("mp4");
$allowedExtsThumb = array("pdf", "jpeg", "gif", "png", "jpg");
$course_id = intval($_POST['course_id']);
$subject_id = intval($_POST['subject_id']);
$unit = $_POST['unit'];
$description = $_POST['description'];
$semester = $_POST['semester'];
$created_by = 1;
$created_at = date("Y-m-d:H:i:s");
$thumbnail_path = "";
$thumbnail_type = "";
$file_path = "";
$file_type = "";

if (isset($_FILES["thumbnail"]["name"]) && $_FILES["thumbnail"]["name"] != '') {
  $extensionThumb = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
  if (in_array($extensionThumb, $allowedExtsThumb)) {
    $temp1 = explode(".", $_FILES["thumbnail"]["name"]);
    $filename1  =  $temp1[0] . '_' . time() . '.' . end($temp1);
    $path1 = "../../uploads/videos/" . $filename1;
    $thumbnail_path = "uploads/videos/" . $filename1;
    move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $path1);
    $thumbnail_type = $extensionThumb;
  } else {
    echo json_encode(['status' => 403, 'message' => 'Invalid thumbnail file type.']);
    exit();
  }
} else {
  echo json_encode(['status' => 403, 'message' => 'Thumbnail is mandatory.']);
  exit();
}
// Check if a YouTube URL is provided
if (isset($_POST["uniform"]) && $_POST["uniform"] != '') {
  $youtube_url = $_POST["uniform"];
  $file_type = "url";
  $file_path = $youtube_url;
} else if (isset($_FILES["video"]["name"]) && $_FILES["video"]["name"] != '') {
  // Check if a video file is uploaded
  $extensionVid = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
  if (($_FILES["video"]["type"] == "video/mp4") && ($_FILES["video"]["size"] < 1073741824) && in_array($extensionVid, $allowedExtsVid) && ($_FILES["video"]["error"] == 0)) {
    $temp = explode(".", $_FILES["video"]["name"]);
    $filename  =  $temp[0] . '_' . time() . '.' . end($temp);
    $path = "../../uploads/videos/" . $filename;
    $file_path = "../../uploads/videos/" . $filename;
    move_uploaded_file($_FILES["video"]["tmp_name"], $path);
    $file_type = $extensionVid;
  } else {
    echo json_encode(['status' => 400, 'message' => 'Invalid video file.']);
    exit();
  }
} else {
  echo json_encode(['status' => 403, 'message' => 'Either a YouTube URL or a video file is mandatory.']);
  exit();
}
// Insert into the database
$add = $conn->query("INSERT INTO `video_lectures`(`unit`,`description`,`semester`,`course_id`, `subject_id`, `thumbnail_url`, `thumbnail_type`,`video_url`, `video_type`, `created_by`, `created_at`) VALUES ('$unit','$description','$semester','$course_id', '$subject_id', '$thumbnail_path', '$thumbnail_type', '$file_path', '$file_type', '$created_by', '$created_at')");
if ($add) {
  echo json_encode(['status' => 200, 'message' => "Upload successful!"]);
} else {
  echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
}
?>