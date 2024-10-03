<?php
## Database configuration
include '../../includes/db-config.php';
session_start();

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if (isset($_POST['order'])) {
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY video_lectures.id ASC";
}




// Admin Query
$query = "";
//$query = " AND Courses.University_ID = ".$_SESSION['university_id'];

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Subjects.Name like '%" . $searchValue . "%' OR Courses.Name like '%" . $searchValue . "%' OR Courses.Short_Name like '%" . $searchValue . "%')";
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(id) as allcount FROM video_lectures WHERE video_lectures.status!=2 $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(video_lectures.id) as filtered FROM video_lectures LEFT JOIN Courses ON Courses.ID = video_lectures.course_id LEFT JOIN Subjects ON Subjects.ID = video_lectures.subject_id WHERE video_lectures.status!=2 $searchQuery $query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];



// $abc="select Videos_Categories	from video_lectures where "
## Fetch records
$result_record = "SELECT video_lectures.`id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name,video_lectures.Videos_Categories AS video_cat,video_lectures.Languages_Categories AS language_cat, video_lectures.`status` FROM video_lectures LEFT JOIN Courses ON Courses.ID = video_lectures.course_id LEFT JOIN Subjects ON Subjects.ID = video_lectures.subject_id WHERE video_lectures.status !=2  $searchQuery $query $orderby LIMIT " . $row . "," . $rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();
while ($row = mysqli_fetch_assoc($results)) {
  $data[] = array(
    "unit" => ucwords($row["unit"]),
    "semester" => $row["semester"],
    "description" => ucwords($row["description"]),
    "course_name" => ucwords($row["course_name"]) . '( ' . $row["course_short_name"] . ' )',
    "subject_name" => ucwords($row["subject_name"]),
    "video_type" => $row["video_type"],
    "video_url" => $row["video_url"],
    "thumnail_url" => $row["thumbnail_url"],
    "thumnail_type" => $row["thumbnail_type"],
    "status" => $row["status"],
    "video_cat" => $row["video_cat"],
    "language_cat" => $row["language_cat"],
    "ID" => $row["id"],
    // "unit" => $row["unit"],
    // "semester" => $row["semester"],
    // "description" => $row["description"],
    // "course_name" => $row["course_short_name"],
    // "subject_name" => $row["subject_name"],
    // "video_type" => $row["video_type"],
    // "video_url" => $row["video_url"],
    // "thumnail_url" => $row["thumbnail_url"],
    // "thumnail_type" => $row["thumbnail_type"],
    // "status" => $row["status"],
    // "ID" => $row["id"],
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
