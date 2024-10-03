<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../includes/db-config.php';

// Check if student ID is set in the session
if (!isset($_SESSION['ID'])) {
    die(json_encode(["error" => "Student ID not found in session."]));
}

$studentId = intval($_SESSION['ID']);
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

// Fetch subject IDs for the student
$qry = $conn->prepare("SELECT Subject_id FROM Student_Subjects WHERE Student_Id = ?");
$qry->bind_param("i", $studentId);
$qry->execute();
$qryResult = $qry->get_result();
$subjectIds = array();
while ($row = $qryResult->fetch_assoc()) {
    $subjectIds[] = $row['Subject_id'];
}
$qry->close();
$idString = implode(',', array_map('intval', $subjectIds));
$subjectQuery = $idString ? "AND Student_Assignment.subject_id IN ($idString)" : "";

// Ordering
$orderBy = "ORDER BY Student_Assignment.id ASC";
if (isset($_POST['order'])) {
    $columnIndex = intval($_POST['order'][0]['column']);
    $columnName = $_POST['columns'][$columnIndex]['data'];
    $columnSortOrder = $_POST['order'][0]['dir'];
    $orderBy = "ORDER BY $columnName $columnSortOrder";
}

// Searching
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$searchQuery = "";
if ($searchValue != '') {
    $searchQuery = " AND (Subjects.Name LIKE ? OR Courses.Name LIKE ? OR Student_Assignment.assignment_name LIKE ?)";
}

// Total number of records without filtering
$sql_total = "SELECT COUNT(Student_Assignment.id) as allcount FROM Student_Assignment
LEFT JOIN Student_Submitted_Assignment ON Student_Assignment.id = Student_Submitted_Assignment.assignment_id AND Student_Submitted_Assignment.student_id = $studentId
LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id AND Student_Submitted_Assignment.student_id = $studentId
LEFT JOIN Subjects ON Student_Assignment.subject_id = Subjects.ID
LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID
WHERE 1 $subjectQuery";
$totalRecordsResult = $conn->query($sql_total);
$totalRecords = $totalRecordsResult->fetch_assoc()['allcount'];
$totalRecordsResult->free();

// Total number of records with filtering
$sql_total_filter = "SELECT COUNT(Student_Assignment.id) as allcount FROM Student_Assignment
LEFT JOIN Student_Submitted_Assignment ON Student_Assignment.id = Student_Submitted_Assignment.assignment_id AND Student_Submitted_Assignment.student_id = $studentId
LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id AND Student_Submitted_Assignment.student_id = $studentId
LEFT JOIN Subjects ON Student_Assignment.subject_id = Subjects.ID
LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID
WHERE 1 $subjectQuery $searchQuery";

$stmt = $conn->prepare($sql_total_filter);
if ($searchValue != '') {
    $likeSearchValue = "%{$searchValue}%";
    $stmt->bind_param("sss", $likeSearchValue, $likeSearchValue, $likeSearchValue);
}
$stmt->execute();
$totalRecordwithFilterResult = $stmt->get_result();
$totalRecordwithFilter = $totalRecordwithFilterResult->fetch_assoc()['allcount'];
$totalRecordwithFilterResult->free();
$stmt->close();

// Fetch records
$sql_query = "SELECT CASE 
WHEN Student_Submitted_Assignment.id IS NULL THEN 'NOT SUBMITTED'
ELSE 'SUBMITTED'
END AS assignment_submission_status, 
Student_Assignment.assignment_name,
Student_Assignment.assignment_file,
Student_Assignment.start_date,
Student_Assignment.end_date,
Student_Assignment.id AS assignment_id,
Student_Assignment.marks,
Subjects.Name AS subject_name, 
Subjects.ID AS subject_id,
Courses.Name AS Grade,
Student_Submitted_Assignment.as_solutions_file AS student_file,
Student_Assignment_Result.obtained_mark AS obt_mark,
Student_Assignment_Result.status,
Student_Assignment_Result.remark AS reason
FROM Student_Assignment
LEFT JOIN Student_Submitted_Assignment ON Student_Assignment.id = Student_Submitted_Assignment.assignment_id AND Student_Submitted_Assignment.student_id = $studentId
LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id AND Student_Submitted_Assignment.student_id = $studentId
LEFT JOIN Subjects ON Student_Assignment.subject_id = Subjects.ID 
LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID
WHERE 1 $subjectQuery $searchQuery $orderBy 
LIMIT ?, ?";
$stmt = $conn->prepare($sql_query);
if ($searchValue != '') {
    $stmt->bind_param("sssii", $likeSearchValue, $likeSearchValue, $likeSearchValue, $start, $length);
} else {
    $stmt->bind_param("ii", $start, $length);
}
$stmt->execute();
$results = $stmt->get_result();
$data = [];
while ($row = $results->fetch_assoc()) {
    $data[] = array(
        "Grade" => $row["Grade"],
        "subject_name" => $row["subject_name"],
        "assignment_name" => $row["assignment_name"],
        "start_date" => $row["start_date"],
        "end_date" => $row["end_date"],
        "marks" => $row["marks"],
        "assignment_file" => $row["assignment_file"],
        "subject_id" => $row["subject_id"],
        "obt_mark" => $row["obt_mark"],
        "status" => $row["status"],
        "reason" => $row["reason"],
        "id" => $row["assignment_id"],
        "assignment_submission_status" => $row["assignment_submission_status"],
        "student_file" => $row["student_file"]
    );
}
$results->free();
$stmt->close();

// Prepare response
$response = array(
    "draw" => $draw,
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);

// Close connection
$conn->close();
