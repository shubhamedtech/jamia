<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';
$response = ['status' => 400, 'message' => 'Invalid request'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $courseId = intval($_POST['course_type']);
    $subjectId = intval($_POST['subject_name']);
    $fileReason = trim($_POST['filesname']);

    if ($courseId && $subjectId && $fileReason) {
        if (isset($_FILES['files']) && $_FILES['files']['error'] == 0) {
            $file = $_FILES['files'];
            $fileName = basename($file['name']);
            $uploadDir = '../../uploads/download_centers/';
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedTypes = ['pdf', 'jpeg', 'jpg', 'png', 'gif', 'mp4', 'avi', 'mov', 'xls', 'xlsx'];

            if (!in_array($fileType, $allowedTypes)) {
                $response['message'] = 'Only PDF, JPEG, JPG, PNG, GIF, MP4, AVI, MOV, XLS, and XLSX files are allowed.';
            } else {
                $fileNameNew = uniqid() . '.' . $fileType;
                $uploadFile = $uploadDir . $fileNameNew;

                if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    $stmt = $conn->prepare("UPDATE Download_Centers SET course_id = ?, subjects_id = ?, file_reason = ?, file_name = ? WHERE id = ?");
                    $stmt->bind_param("iissi", $courseId, $subjectId, $fileReason, $uploadFile, $id);

                    if ($stmt->execute()) {
                        $response = ['status' => 200, 'message' => 'File Updated Successfully!'];
                    } else {
                        $response['message'] = 'Database update failed.';
                    }
                    $stmt->close();
                } else {
                    $response['message'] = 'Error uploading the file.';
                }
            }
        } else {
            $stmt = $conn->prepare("UPDATE Download_Centers SET course_id = ?, subjects_id = ?, file_reason = ? WHERE id = ?");
            $stmt->bind_param("iiss", $courseId, $subjectId, $fileReason, $id);

            if ($stmt->execute()) {
                $response = ['status' => 200, 'message' => 'File updated successfully!'];
            } else {
                $response['message'] = 'Database update failed.';
            }
            $stmt->close();
        }
    } else {
        $response['message'] = 'Required fields missing.';
    }
    $conn->close();
}

echo json_encode($response);
