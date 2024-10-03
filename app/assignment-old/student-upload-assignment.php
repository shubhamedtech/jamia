<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';
session_start();

if (!isset($_SESSION['ID'])) {
    echo json_encode(['status' => 400, 'message' => 'You are not logged in.']);
    exit;
}

if (isset($_POST['upload_assignment'])) {
    // Retrieve and sanitize input
    $assignment_id = $conn->real_escape_string($_POST['assignment_id']);
    // print_r('assignment_id=' . $assignment_id . '</br>');
    $subject_id = $conn->real_escape_string($_POST['subject_id']);
    // print_r('subject_id=' . $subject_id . '</br>');
    $student_id = $conn->real_escape_string($_SESSION['ID']);
    // print_r('student_id=' . $student_id . '</br>');
    $uploaded_type = $conn->real_escape_string($_POST['uploaded_type']);
    // print_r('uploaded_type=' . $uploaded_type . '</br>');

    if ($_FILES["assignment_file"]["error"] == UPLOAD_ERR_OK) {
        $fileName = basename($_FILES["assignment_file"]["name"]);
        $targetDir = '../../uploads/assignments/';
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = array('pdf', 'jpeg', 'jpg', 'png', 'gif');
        // Validate file type
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['status' => 400, 'message' => 'Only PDF, JPEG, PNG, GIF or JPG files are allowed.']);
            exit;
        }
        // Generate a unique file name
        $fileNameNew = uniqid() . '.' . $fileType;
        $uploadFile = $targetDir . $fileNameNew;

        // Move the uploaded file
        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $uploadFile)) {
            $created_date = date('Y-m-d H:i:s');

            // Prepare statement to check existing data
            $stmt = $conn->prepare("SELECT * FROM Student_Submitted_Assignment WHERE assignment_id=? AND student_id=? AND subject_id=?");
            $stmt->bind_param('iii', $assignment_id, $student_id, $subject_id);
            $stmt->execute();
            $existingData = $stmt->get_result();

            if ($existingData->num_rows > 0) {
                // Update existing assignment
                $stmt = $conn->prepare("UPDATE Student_Submitted_Assignment SET as_solutions_file=?, created_date=? WHERE assignment_id=? AND subject_id=? AND student_id=?");
                $stmt->bind_param('ssiii', $uploadFile, $created_date, $assignment_id, $subject_id, $student_id);
            } else {
                // Insert new assignment
                $stmt = $conn->prepare("INSERT INTO Student_Submitted_Assignment (assignment_id, subject_id, student_id, uploaded_type, as_solutions_file, created_date) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('iiisss', $assignment_id, $subject_id, $student_id, $uploaded_type, $uploadFile, $created_date);
            }

            // Execute the query
            if ($stmt->execute()) {
                echo json_encode(['status' => 200, 'message' => 'Assignment Submitted Successfully!']);
                header('location:/../student/lms/assignments');
                exit;
            } else {
                echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
                exit;
            }
        } else {
            echo json_encode(['status' => 400, 'message' => 'Error moving uploaded file.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 400, 'message' => 'File upload error.']);
        exit;
    }
} else {
    echo json_encode(['status' => 400, 'message' => 'Invalid request.']);
    exit;
}
// }
