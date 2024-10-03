<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../../includes/db-config.php';
session_start();

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$row = isset($_POST['start']) ? intval($_POST['start']) : 0;
$rowperpage = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';


$orderBy = "Students.ID ASC";
if (isset($_POST['order'])) {
    $columnIndex = intval($_POST['order'][0]['column']);
    $columnName = $_POST['columns'][$columnIndex]['data'];
    $columnSortOrder = $_POST['order'][0]['dir'];
    $orderBy = "$columnName $columnSortOrder";
}


$searchQuery = "";
$searchParams = [];
if ($searchValue != '') {
    $searchQuery = " AND (Students.Enrollment_No LIKE ? OR Students.First_Name LIKE ? OR Students.Last_Name LIKE ? OR Subjects.Name LIKE ?)";
    $likeSearchValue = "%$searchValue%";
    $searchParams = [$likeSearchValue, $likeSearchValue, $likeSearchValue, $likeSearchValue];
}


$allCountQuery = "SELECT COUNT(Students.ID) as allcount FROM Students 
    LEFT JOIN Student_Submitted_Assignment ON Students.ID = Student_Submitted_Assignment.student_id
    LEFT JOIN Subjects ON Student_Submitted_Assignment.subject_id = Subjects.ID
    LEFT JOIN Courses ON Courses.ID = Students.Course_ID
    LEFT JOIN Student_Assignment ON Subjects.ID = Student_Assignment.subject_id
    LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id
    LEFT JOIN Universities ON Students.University_ID = Universities.ID
    WHERE Students.status != 2";
$allCountResult = $conn->query($allCountQuery);
if (!$allCountResult) {
    echo json_encode(["error" => $conn->error]);
    exit;
}
$records = $allCountResult->fetch_assoc();
$totalRecords = $records['allcount'];


$filterCountQuery = "SELECT COUNT(DISTINCT Students.ID) as filtered 
    FROM Students 
    LEFT JOIN Student_Submitted_Assignment ON Students.ID = Student_Submitted_Assignment.student_id
    LEFT JOIN Subjects ON Student_Submitted_Assignment.subject_id = Subjects.ID
    LEFT JOIN Courses ON Courses.ID = Students.Course_ID
    LEFT JOIN Student_Assignment ON Subjects.ID = Student_Assignment.subject_id
    LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id
    LEFT JOIN Universities ON Students.University_ID = Universities.ID
    WHERE Students.status != 2 $searchQuery";

$stmt = $conn->prepare($filterCountQuery);
if ($searchValue != '') {
    $stmt->bind_param(str_repeat("s", count($searchParams)), ...$searchParams);
}
$stmt->execute();
$filterCountResult = $stmt->get_result();
$records = $filterCountResult->fetch_assoc();
$totalRecordwithFilter = $records['filtered'];


$fetchRecordsQuery = "SELECT Students.ID as student_id, 
        CONCAT_WS(' ', Students.First_Name, Students.Middle_Name, Students.Last_Name) AS student_name,
        Students.Enrollment_No,
        Subjects.Name as subject_name,
        CASE WHEN Universities.ID = 48 THEN 'Jamia Urdu Aligarh' ELSE 'Unknown University' END AS universityname,
        CASE WHEN Student_Submitted_Assignment.id IS NULL THEN 'NOT SUBMITTED' ELSE 'SUBMITTED' END AS assignment_submission_status,
        CASE WHEN Student_Assignment_Result.status IS NULL THEN 'NOT EVALUATED' ELSE Student_Assignment_Result.status END AS eva_status,
        CASE WHEN Student_Assignment.id IS NULL THEN 'NOT CREATED' ELSE 'CREATED' END AS assignment_status,
        COALESCE(Student_Submitted_Assignment.uploaded_type, 'NULL') AS uploaded_type,
        Student_Submitted_Assignment.created_date,
        Student_Submitted_Assignment.as_solutions_file AS pdffile,
        Courses.Name AS course_name,
        Courses.Short_Name as Short_Name,
        Student_Assignment_Result.remark AS reason,
        Student_Assignment_Result.obtained_mark AS obt_mark,
        Student_Submitted_Assignment.id,
        Subjects.ID AS subject_id,
        Student_Assignment.id AS assignment_id
    FROM Students
    LEFT JOIN Student_Submitted_Assignment ON Students.ID = Student_Submitted_Assignment.student_id
    LEFT JOIN Subjects ON Student_Submitted_Assignment.subject_id = Subjects.ID
    LEFT JOIN Courses ON Courses.ID = Students.Course_ID
    LEFT JOIN Student_Assignment ON Subjects.ID = Student_Assignment.subject_id
    LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id
    LEFT JOIN Universities ON Students.University_ID = Universities.ID
    WHERE Students.status != 2 $searchQuery
    GROUP BY Students.ID, Subjects.ID
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
        "student_name" => $row["student_name"],
        "Enrollment_No" => $row["Enrollment_No"],
        "subject_name" => $row["subject_name"],
        "created_date" => $row["created_date"],
        "course_name" => $row["course_name"],
        "pdffile" => $row["pdffile"],
        "uploaded_type" => $row["uploaded_type"],
        "Short_Name" => $row["Short_Name"],
        "reason" => $row["reason"],
        "obt_mark" => $row["obt_mark"],
        "id" => $row["id"],
        "student_id" => $row["student_id"],
        "subject_id" => $row["subject_id"],
        "assignment_id" => $row["assignment_id"],
        "assignment_submission_status" => $row["assignment_submission_status"],
        "eva_status" => $row["eva_status"],
        "assignment_status" => $row["assignment_status"],
        "universityname" => $row["universityname"]
    );
}
$response = array(
    "draw" => $draw,
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);
echo json_encode($response);
