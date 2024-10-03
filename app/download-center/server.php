<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../../includes/db-config.php';
session_start();
$filesql = '';
$bindParams = [];
$bindTypes = '';

if ($_SESSION['Role'] == 'Student') {
    $filesql = " AND Download_Centers.course_id = ? AND Download_Centers.status = 1";
    $bindParams[] = $_SESSION['Course_ID'];
    $bindTypes .= 'i';
}

// DataTables Parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$row = isset($_POST['start']) ? intval($_POST['start']) : 0;
$rowperpage = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Ordering
$orderBy = "Download_Centers.id ASC";
if (isset($_POST['order'])) {
    $columnIndex = intval($_POST['order'][0]['column']);
    $columnName = $_POST['columns'][$columnIndex]['data'];
    $columnSortOrder = $_POST['order'][0]['dir'];
    $orderBy = "$columnName $columnSortOrder";
}

// Search
$searchQuery = "";
if ($searchValue != '') {
    $searchQuery = " AND (Courses.Name LIKE ? OR Subjects.Name LIKE ?)";
    $likeSearchValue = "%$searchValue%";
    $bindParams[] = $likeSearchValue;
    $bindParams[] = $likeSearchValue;
    $bindTypes .= 'ss';
}

// Get Total Record Count Without Filtering
$allCountQuery = "SELECT COUNT(Download_Centers.ID) as allcount FROM Download_Centers
    LEFT JOIN Courses ON Courses.ID = Download_Centers.course_id 
    LEFT JOIN Subjects ON Subjects.ID = Download_Centers.subjects_id";
if ($filesql) {
    $allCountQuery .= " WHERE 1=1 $filesql";
}
$allCountStmt = $conn->prepare($allCountQuery);
if ($allCountStmt === false) {
    die('Prepare failed: ' . $conn->error);
}
if ($filesql) {
    $allCountStmt->bind_param("i", $_SESSION['Course_ID']);
}
$allCountStmt->execute();
$allCountResult = $allCountStmt->get_result();
if (!$allCountResult) {
    echo json_encode(["error" => $conn->error]);
    exit;
}
$records = $allCountResult->fetch_assoc();
$totalRecords = $records['allcount'];

// Get Total Record Count With Filtering
$filterCountQuery = "SELECT COUNT(Download_Centers.ID) as allcount FROM Download_Centers
    LEFT JOIN Courses ON Courses.ID = Download_Centers.course_id 
    LEFT JOIN Subjects ON Subjects.ID = Download_Centers.subjects_id WHERE 1=1 $filesql $searchQuery";
$filterCountStmt = $conn->prepare($filterCountQuery);
if ($filterCountStmt === false) {
    die('Prepare failed: ' . $conn->error);
}
if ($bindTypes) {
    $filterCountStmt->bind_param($bindTypes, ...$bindParams);
}
$filterCountStmt->execute();
$filterCountResult = $filterCountStmt->get_result();
if (!$filterCountResult) {
    echo json_encode(["error" => $conn->error]);
    exit;
}
$records = $filterCountResult->fetch_assoc();
$totalRecordwithFilter = $records['allcount'];

// Fetch Records
$fetchRecordsQuery = "SELECT 
        Download_Centers.id, 
        Download_Centers.file_reason AS reason, 
        Courses.Name AS course_name, 
        Download_Centers.created_at AS created_date, 
        Download_Centers.updated_at AS updated_date, 
        Download_Centers.file_name AS files, 
        Subjects.Name AS subject_name, 
        Universities.Name AS universityname, 
        Download_Centers.status
    FROM 
        Download_Centers
    LEFT JOIN 
        Courses ON Courses.ID = Download_Centers.course_id 
    LEFT JOIN 
        Subjects ON Subjects.ID = Download_Centers.subjects_id
    LEFT JOIN 
        Universities ON Universities.ID = Courses.University_ID
    WHERE 
        1=1 $searchQuery $filesql
    ORDER BY 
        $orderBy 
    LIMIT 
        ?, ?";

$fetchStmt = $conn->prepare($fetchRecordsQuery);
if ($fetchStmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$bindParams[] = $row;
$bindParams[] = $rowperpage;
$bindTypes .= 'ii';
$fetchStmt->bind_param($bindTypes, ...$bindParams);

$fetchStmt->execute();
$results = $fetchStmt->get_result();
$data = [];
while ($row = $results->fetch_assoc()) {
    $data[] = array(
        "reason" => $row["reason"],
        "course_name" => $row["course_name"],
        "created_date" => $row["created_date"],
        "updated_date" => $row["updated_date"],
        "files" => $row["files"],
        "subject_name" => $row["subject_name"],
        "status" => $row["status"],
        "universityname" => $row["universityname"],
        "id" => $row["id"]
    );
}
$response = array(
    "draw" => $draw,
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);
echo json_encode($response);
