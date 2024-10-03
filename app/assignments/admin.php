<?php
require '../../includes/db-config.php';

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$row = isset($_POST['start']) ? intval($_POST['start']) : 0;
$rowperpage = isset($_POST['length']) ? intval($_POST['length']) : 10;

## Column ordering
$columnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$columnName = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'id';
$columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

## Search value
$searchValue = isset($_POST['search']['value']) ? mysqli_real_escape_string($conn, $_POST['search']['value']) : '';

## Ordering query
$orderby = "ORDER BY $columnName $columnSortOrder";

## Search query
$searchQuery = "";
if ($searchValue != '') {
  $searchQuery = " AND (Subjects.Name LIKE '%$searchValue%' 
                          OR Courses.Name LIKE '%$searchValue%'
                          OR Student_Assignment.assignment_name LIKE '%$searchValue%'
                          OR Student_Assignment.start_date LIKE '%$searchValue%'
                          OR Student_Assignment.end_date LIKE '%$searchValue%'
                          OR Student_Assignment.created_date LIKE '%$searchValue%'
                          OR Student_Assignment.marks LIKE '%$searchValue%'
                          OR Student_Assignment.assignment_file LIKE '%$searchValue%')";
}

## Total number of records without filtering
$sql_total = "SELECT COUNT(*) AS allcount FROM Student_Assignment 
LEFT JOIN Subjects ON Student_Assignment.subject_id = Subjects.ID
LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID 
WHERE 1 $searchQuery";
$totalRecordsResult = mysqli_query($conn, $sql_total);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['allcount'];

## Total number of records with filtering
$sql_total_filter = "SELECT COUNT(*) AS allcount FROM Student_Assignment 
                     LEFT JOIN Subjects ON Student_Assignment.subject_id = Subjects.ID
                     LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID 
                     WHERE 1 $searchQuery";
$totalRecordwithFilterResult = mysqli_query($conn, $sql_total_filter);
$totalRecordwithFilter = mysqli_fetch_assoc($totalRecordwithFilterResult)['allcount'];

## Fetch records
$sql_query = "SELECT Subjects.Name AS subject_name,  
                     Courses.Name AS Grade,
                     Courses.Short_Name AS short_name,
                     Student_Assignment.id,
                     Student_Assignment.assignment_name,
                     Student_Assignment.start_date,
                     Student_Assignment.end_date,
                     Student_Assignment.created_date,
                     Student_Assignment.updated_date,
                     Student_Assignment.marks,
                     Student_Assignment.created_by AS created,
                     Student_Assignment.assignment_file
              FROM Student_Assignment
              LEFT JOIN Subjects ON Student_Assignment.subject_id = Subjects.ID
              LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID 
              WHERE 1 $searchQuery $orderby 
              LIMIT $row, $rowperpage";
$results = mysqli_query($conn, $sql_query);
$data = [];
while ($row = mysqli_fetch_assoc($results)) {
  $data[] = array(
    "subject_name" => $row["subject_name"],
    "Grade" => $row["Grade"],
    "assignment_name" => $row["assignment_name"],
    "start_date" => $row["start_date"],
    "end_date" => $row["end_date"],
    "created_date" => $row["created_date"],
    "marks" => $row["marks"],
    "assignment_file" => $row["assignment_file"],
    "updated_date" => $row["updated_date"],
    "short_name" => $row["short_name"],
    "created" => $row["created"],
    "id" => $row["id"],
  );
}
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
