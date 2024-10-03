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
  $orderby = "ORDER BY results.ID DESC";
}

// $statusQuery = "";
// if(!in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Student'])){
//  $statusQuery = " AND results.status = 1";
// }

$query="";

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)) like '%".$searchValue."%'  OR Students.Father_Name LIKE '%".$searchValue."%' OR Students.Unique_ID LIKE '%".$searchValue."%'  OR Courses.Short_Name LIKE '%".$searchValue."%'  OR Students.Enrollment_No LIKE '%".$searchValue."%' OR DATE_FORMAT(results.published_on, '%d-%m-%Y') LIKE '%".$searchValue."%' )";
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(results.id) as allcount FROM results WHERE results.status!=2 $query");
//$totalRecords = $all_count->num_rows;

$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(results.id) as filtered FROM results LEFT JOIN Students ON results.student_id = Students.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo'  LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE results.status!=2  $query $searchQuery ");
//$totalRecordwithFilter = $filter_count->num_rows;

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT results.id as ID, TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)) as First_Name,results.status, Students.Father_Name, Students.Unique_ID, Students.Enrollment_No, Courses.Short_Name as Course_ID, Sub_Courses.Name as Sub_Course_ID, Student_Documents.`Location` as Photo, results.remark, results.student_id, Users.Code, Users.`Name`, DATE_FORMAT(results.published_on, '%d-%m-%Y') as published_on FROM results LEFT JOIN Students ON results.student_id = Students.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo' LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Users ON results.published_by = Users.ID WHERE results.status!=2 $query $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);

//print_r($result_record); die;
$data = array();
while ($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array(
    "ID" => $row["ID"],
    "Photo"=> $row['Photo'],
    "student_name" => $row['First_Name'],
    "enrollment_no" => $row['Enrollment_No'],
    "course_name" => $row['Course_ID'],
   
    "Father_Name" => $row['Father_Name'],
    "unique_id" => $row['Unique_ID'],
    // "Student_ID" => base64_encode('W1Ebt1IhGN3ZOLplom9I'.$row['Student_ID'].'W1Ebt1IhGN3ZOLplom9I'),
    "published_on" => !empty($row['published_on']) ? $row['published_on'] : 0,
    "Code" => $row['Code'],
    "published_by" => $row['Name'],
    "status" => $row['status'],
    "remark" => $row['remark'],
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
