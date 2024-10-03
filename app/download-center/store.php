<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseId = $_POST['coursetype'];
    $filereason = $_POST['filename'];
    $subject_id = $_POST['subject_id'];

    if (isset($_FILES['files']) && $_FILES['files']['error'] == 0) {
        $file = $_FILES['files'];
        $fileName = basename($file['name']);
        $uploadDir = '../../uploads/download_centers/';
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = array('pdf', 'jpeg', 'jpg', 'png', 'gif', 'mp4', 'avi', 'mov', 'xls', 'xlsx');

        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['status' => 400, 'message' => 'Only PDF, JPEG, JPG, PNG, GIF, MP4, AVI, MOV, XLS, and XLSX files are allowed.']);
            exit;
        }
        $fileNameNew = uniqid() . '.' . str_replace(' ', '_', $fileType);
        $uploadFile = $uploadDir . $fileNameNew;
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            $sql = "INSERT INTO Download_Centers (course_id, subjects_id, file_reason, file_name) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $courseId, $subject_id, $filereason, $uploadFile);

            if ($stmt->execute()) {
                echo json_encode(['status' => 200, 'message' => 'Files Created Successfully!']);
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
