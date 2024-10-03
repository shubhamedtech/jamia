<?php
ini_set('display_errors',1);
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

$filterBySubCourses = "";
if (isset($_SESSION['filterBySubCourses'])) {
    $filterBySubCourses = $_SESSION['filterBySubCourses'];
}
$filterByCourses = "";
if (isset($_SESSION['filterByCourses'])) {
    $filterByCourses = $_SESSION['filterByCourses'];
}
$filterByUser = "";
if (isset($_SESSION['filterByUser'])) {
    $filterByUser = $_SESSION['filterByUser'];
}
$filterBySubjects = "";
if (isset($_SESSION['filterBySubjects'])) {
    $filterBySubjects = $_SESSION['filterBySubjects'];
}
$filterByAssignment_status = "";
if (isset($_SESSION['filterByAssignment_status'])) {
    $filterByAssignment_status = $_SESSION['filterByAssignment_status'];
}
$filterByVerticalType = "";
if (isset($_SESSION['filterByVerticalType'])) {
    $filterByVerticalType = $_SESSION['filterByVerticalType'];
}
$submitted_students = "";
if (isset($_SESSION['submitted_students'])) {
    $submitted_students = $_SESSION['submitted_students'];
}

$pro_filter = "";
$pro_filter .= $filterBySubCourses . $filterByCourses . $filterByUser . $filterByAssignment_status . $filterByVerticalType . $submitted_students . $filterBySubjects;

$searchQuery = "";
$searchParams = [];
if ($searchValue != '') {
    $searchQuery = " AND (Courses.Name LIKE ? OR Students.Enrollment_No LIKE ? OR Students.First_Name LIKE ? OR Students.Last_Name LIKE ? OR Subjects.Name LIKE ?)";
    $likeSearchValue = "%$searchValue%";
    $searchParams = [$likeSearchValue, $likeSearchValue, $likeSearchValue, $likeSearchValue, $likeSearchValue];
}
$allCountQuery = "SELECT COUNT(Students.ID) as allcount FROM Students 
    LEFT JOIN Student_Subjects ON Students.ID = Student_Subjects.Student_Id
    LEFT JOIN Subjects ON Student_Subjects.Subject_Id = Subjects.ID
    LEFT JOIN Student_Submitted_Assignment ON Students.ID = Student_Submitted_Assignment.student_id AND Subjects.ID = Student_Submitted_Assignment.subject_id 
    LEFT JOIN Courses ON Courses.ID = Students.Course_ID
    LEFT JOIN Student_Assignment ON Subjects.ID = Student_Assignment.subject_id
    LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id
    LEFT JOIN Universities ON Students.University_ID = Universities.ID
    LEFT JOIN Users ON Students.Added_For = Users.ID
    WHERE Students.status != 2 AND Student_Assignment.id IS NOT NULL $pro_filter";
$allCountResult = $conn->query($allCountQuery);
if (!$allCountResult) {
    echo json_encode(["error" => $conn->error]);
    exit;
}
$records = $allCountResult->fetch_assoc();
$totalRecords = $records['allcount'];
$filterCountQuery = "SELECT COUNT(Students.ID) as filtered 
    FROM Students 
    LEFT JOIN Student_Subjects ON Students.ID = Student_Subjects.Student_Id
    LEFT JOIN Subjects ON Student_Subjects.Subject_Id = Subjects.ID
    LEFT JOIN Student_Submitted_Assignment ON Students.ID = Student_Submitted_Assignment.student_id AND Subjects.ID = Student_Submitted_Assignment.subject_id 
    LEFT JOIN Courses ON Courses.ID = Students.Course_ID
    LEFT JOIN Student_Assignment ON Subjects.ID = Student_Assignment.subject_id
    LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id
    LEFT JOIN Universities ON Students.University_ID = Universities.ID
    LEFT JOIN Users ON Students.Added_For = Users.ID
    WHERE Students.status != 2 AND Student_Assignment.id IS NOT NULL $searchQuery $pro_filter";
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
       CASE WHEN Users.Vertical = 1 THEN 'IITS'WHEN Users.Vertical = 2 THEN 'Edtech'WHEN Users.Vertical = 3 THEN 'InterNational' ELSE 'Other' END AS verticaltype,
       CASE WHEN Universities.ID = 48 THEN 'Jamia Urdu Aligarh' ELSE 'Unknown University' END AS universityname,
       CASE WHEN Student_Submitted_Assignment.id IS NULL THEN 'NOT SUBMITTED'
    WHEN Student_Submitted_Assignment.id IS NOT NULL AND Student_Assignment_Result.status = 'Rejected' AND Student_Submitted_Assignment.reuploaded = 1 THEN 'RESUBMITTED'
    WHEN Student_Submitted_Assignment.id IS NOT NULL AND Student_Assignment_Result.status = 'Rejected' THEN 'NOT RESUBMITTED'
    WHEN Student_Submitted_Assignment.id IS NOT NULL THEN 'SUBMITTED'
    ELSE 'UNKNOWN STATUS'
    END AS assignment_submission_status,
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
        Student_Assignment.id AS assignment_id,
        Users.Name AS center_name,
        Users.Code AS center_code,
        Students.Unique_ID AS stu_unique_id,
        Students.DOB as dateofbirth,
        Student_Assignment.assignment_name as taskname
    FROM Students
    LEFT JOIN Student_Subjects ON Students.ID = Student_Subjects.Student_Id
    LEFT JOIN Subjects ON Student_Subjects.Subject_Id = Subjects.ID
    LEFT JOIN Student_Submitted_Assignment ON Students.ID = Student_Submitted_Assignment.student_id AND Subjects.ID = Student_Submitted_Assignment.subject_id 
    LEFT JOIN Courses ON Courses.ID = Students.Course_ID
    LEFT JOIN Student_Assignment ON Subjects.ID = Student_Assignment.subject_id
    LEFT JOIN Student_Assignment_Result ON Student_Submitted_Assignment.id = Student_Assignment_Result.assignment_id
    LEFT JOIN Universities ON Students.University_ID = Universities.ID
    LEFT JOIN Users ON Students.Added_For = Users.ID 
    WHERE Students.status != 2 AND Student_Assignment.id IS NOT NULL $pro_filter $searchQuery 
    GROUP BY Students.ID, Subjects.ID
    ORDER BY $orderBy 
    LIMIT ?, ?";

$stmt = $conn->prepare($fetchRecordsQuery);
$params = array_merge($searchParams, [$row, $rowperpage]);
$stmt->bind_param(str_repeat("s", count($searchParams)) . "ii", ...$params);
$stmt->execute();
$results = $stmt->get_result();
$data = array();
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
        "universityname" => $row["universityname"],
        "center_name" => $row["center_name"],
        "center_code" => $row["center_code"],
        "verticaltype" => $row["verticaltype"],
        "stu_unique_id" => $row["stu_unique_id"],
        "dateofbirth" => $row["dateofbirth"],
        "taskname" => $row["taskname"]
    );
}
$response = array(
    "draw" => $draw,
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);
echo json_encode($response);
