<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../../includes/db-config.php';
session_start();

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
$searchParams = [];
if ($searchValue != '') {
    $searchQuery = " AND (Download_Centers.course_id LIKE ? OR Download_Centers.subjects_id LIKE ?)";
    $likeSearchValue = "%$searchValue%";
    $searchParams = [$likeSearchValue, $likeSearchValue];
}

// Get Total Record Count Without Filtering
$allCountQuery = "SELECT COUNT(Download_Centers.ID) as allcount FROM Download_Centers
     LEFT JOIN Courses ON Courses.ID = Download_Centers.course_id 
    LEFT JOIN Subjects ON Subjects.ID = Download_Centers.subjects_id";
$allCountResult = $conn->query($allCountQuery);
if (!$allCountResult) {
    echo json_encode(["error" => $conn->error]);
    exit;
}
$records = $allCountResult->fetch_assoc();
$totalRecords = $records['allcount'];

// Get Total Record Count With Filtering
$filterCountQuery = "SELECT COUNT(Download_Centers.ID) as allcount FROM Download_Centers
     LEFT JOIN Courses ON Courses.ID = Download_Centers.course_id 
    LEFT JOIN Subjects ON Subjects.ID = Download_Centers.subjects_id WHERE 1=1 $searchQuery";
$stmt = $conn->prepare($filterCountQuery);
if ($searchValue != '') {
    $stmt->bind_param(str_repeat("s", count($searchParams)), ...$searchParams);
}
$stmt->execute();
$filterCountResult = $stmt->get_result();
$records = $filterCountResult->fetch_assoc();
$totalRecordwithFilter = $records['allcount'];
$filesql = '';
if ($_SESSION['Role'] == 'Student') {
    $filesql = " AND Download_Centers.course_id = " . $_SESSION['Course_ID'];
}

// Fetch Records
$fetchRecordsQuery = "SELECT Download_Centers.ID, Download_Centers.file_reason as reason, Courses.Name as course_name, Download_Centers.file_name as files, Download_Centers.id
    FROM Download_Centers
    LEFT JOIN Courses ON Courses.ID = Download_Centers.course_id 
    LEFT JOIN Subjects ON Subjects.ID = Download_Centers.subjects_id
    WHERE 1=1 $searchQuery $filesql
    ORDER BY $orderBy 
    LIMIT ?, ?";
$stmt = $conn->prepare($fetchRecordsQuery);
$params = array_merge($searchParams, [$row, $rowperpage]);
$stmt->bind_param(str_repeat("s", count($searchParams)) . "ii", ...$params);
$stmt->execute();
$results = $stmt->get_result();

$data = [];
while ($row = $results->fetch_assoc()) {
    $data[] = array(
        "reason" => $row["reason"],
        "course_name" => $row["course_name"],
        "files" => $row["files"],
        "id" => $row["id"]
    );
}

// JSON Response
$response = array(
    "draw" => $draw,
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);
echo json_encode($response);
