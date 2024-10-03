<?php
require '../../includes/db-config.php';

$allowedExtsVid = array("mp4");
$allowedExtsThumb = array("pdf", "jpeg", "gif", "png", "jpg");

$course_id = intval($_POST['course_id']);
$subject_id = intval($_POST['subject_id']);
$unit = $_POST['unit'];
$videos_categories = $_POST['videos_categories'];
$description = $_POST['description'];
$semester = $_POST['semester'];
$language_categories = $_POST['language_categories'];
$created_by = 1; // Assuming this is a fixed value
$created_at = date("Y-m-d H:i:s");
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
        $thumbnail_path = "/uploads/videos/" . $filename1;
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

if (isset($_POST["uniform"]) && $_POST["uniform"] != '') {
    $youtube_url = $_POST["uniform"];
    $file_type = "url";
    $file_path = $youtube_url;
} else if (isset($_FILES["video"]["name"]) && $_FILES["video"]["name"] != '') {
    $extensionVid = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
    if (($_FILES["video"]["type"] == "video/mp4") && ($_FILES["video"]["size"] < 1073741824) && in_array($extensionVid, $allowedExtsVid) && ($_FILES["video"]["error"] == 0)) {
        $temp = explode(".", $_FILES["video"]["name"]);
        $filename  =  $temp[0] . '_' . time() . '.' . end($temp);
        $path = "../../uploads/videos/" . $filename;
        $file_path = "/uploads/videos/" . $filename;
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

// Prepare and execute the SQL statement
$stmt = $conn->prepare("INSERT INTO `video_lectures` (`unit`, `description`, `semester`, `course_id`, `subject_id`, `thumbnail_url`, `thumbnail_type`, `video_url`, `video_type`, `videos_categories`, `Languages_Categories`, `created_by`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo json_encode(['status' => 400, 'message' => 'Failed to prepare the SQL statement.']);
    exit();
}

$stmt->bind_param('sssiisssssiss', $unit, $description, $semester, $course_id, $subject_id, $thumbnail_path, $thumbnail_type, $file_path, $file_type, $videos_categories, $language_categories ,$created_by, $created_at);

if ($stmt->execute()) {
    echo json_encode(['status' => 200, 'message' => "Upload Successful Video!"]);
} else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
}

$stmt->close();
$conn->close();
