<?php
require '../../includes/db-config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseId = $_POST['coursetype'];
    $subjectId = $_POST['subject'];
    $assignmentName = $_POST['assignmentname'];
    $marks = $_POST['marks'];
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];
    $created_by = $_POST['created'];
    // Handle file upload

    if (isset($_FILES['files']) && $_FILES['files']['error'] == 0) {
        $file = $_FILES['files'];
        $fileName = basename($file['name']);
        $uploadDir = '../../uploads/assignments/';
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = array('pdf', 'jpeg', 'jpg', 'png', 'gif');
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['status' => 400, 'message' => 'Only PDF, JPEG, JPG, PNG, and GIF files are allowed.']);
            exit;
        }
        $fileNameNew = uniqid() . '.' . str_replace(' ', '_', $fileType);
        $uploadFile = $uploadDir . $fileNameNew;
        $creationDate = date("Y-m-d H:i:s");

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            $sql = "INSERT INTO Student_Assignment (course_id, subject_id, assignment_name, marks, assignment_file, start_date, end_date, created_by, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisisssss", $courseId, $subjectId, $assignmentName, $marks, $uploadFile, $startDate, $endDate, $created_by, $creationDate);
            if ($stmt->execute()) {
                echo json_encode(['status' => 200, 'message' => 'Assignment Created Successfully!']);
            } else {
                echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "File upload error.";
    }
    $conn->close();
}
