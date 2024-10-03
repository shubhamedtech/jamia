<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $courseId = intval($_POST['coursetype']);
    $subjectId = intval($_POST['subject']);
    $assignmentName = htmlspecialchars($_POST['assignmentname']);
    $marks = intval($_POST['marks']);
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];
    $uploadDir = '../../uploads/assignments/';
    $assignmentFile = '';

    // Handle file upload
    if (isset($_FILES['files']) && $_FILES['files']['size'] > 0) {
        $fileName = basename($_FILES['files']['name']);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = array('pdf', 'jpeg', 'jpg', 'png', 'gif');
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['status' => 400, 'message' => 'Only PDF, JPEG, JPG, PNG, and GIF files are allowed.']);
            exit;
        }
        $fileNameNew = uniqid() . '.' . str_replace(' ', '_', $fileType);
        $targetPath = $uploadDir . $fileNameNew;
        if (move_uploaded_file($_FILES['files']['tmp_name'], $targetPath)) {
            $assignmentFile = $targetPath;
        } else {
            echo json_encode(['status' => 400, 'message' => 'Error: File upload failed.']);
            exit();
        }
    }
    $updatedDate = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE `Student_Assignment` SET `course_id` = ?, `subject_id` = ?, `assignment_name` = ?, `marks` = ?, `start_date` = ?, `end_date` = ?, `assignment_file` = ?, `updated_date` = ? WHERE `id` = ?");
    $stmt->bind_param("iisissssi", $courseId, $subjectId, $assignmentName, $marks, $startDate, $endDate, $assignmentFile, $updatedDate, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 200, 'message' => 'Assignment Updated Successfully!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Error: Assignment not Updated.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 400, 'message' => 'Invalid Request.']);
}
$conn->close();
