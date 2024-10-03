<?php
## Database configuration
include '../../includes/db-config.php';
session_start();

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY e_books.id ASC";
}




// Admin Query
$query = "";
//$query = " AND Courses.University_ID = ".$_SESSION['university_id'];

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Subjects.Name like '%".$searchValue."%' OR Courses.Name like '%".$searchValue."%' OR Courses.Short_Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(id) as allcount FROM e_books WHERE e_books.status!=2 $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(e_books.id) as filtered FROM e_books LEFT JOIN Courses ON Courses.ID = e_books.course_id LEFT JOIN Subjects ON Subjects.ID = e_books.subject_id WHERE e_books.status!=2 $searchQuery $query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT e_books.`id`, e_books.`file_type`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name, e_books.`status` FROM e_books LEFT JOIN Courses ON Courses.ID = e_books.course_id LEFT JOIN Subjects ON Subjects.ID = e_books.subject_id WHERE e_books.status !=2  $searchQuery $query $orderby LIMIT ".$row.",".$rowperpage;


$results = mysqli_query($conn, $result_record);
$data = array();
while ($row = mysqli_fetch_assoc($results)) {

    $data[] = array( 
      "course_name" => $row["course_short_name"],
      "subject_name" => $row["subject_name"],
      "file_type" => $row["file_type"],
      "status" => $row["status"],
      "ID" => $row["id"],
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
