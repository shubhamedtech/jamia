<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["student_id"], $_POST['assignment_id'], $_POST['subject_id'], $_FILES["teacher_upload_assignment"])) {
        require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
        $file = $_FILES["teacher_upload_assignment"];
        $assignment_id = intval($_POST['assignment_id']);
        $subject_id = intval($_POST['subject_id']);
        $student_id = intval($_POST['student_id']);
        $uploaded_type = 'Manual';

        if (empty($assignment_id) || empty($subject_id) || empty($student_id)) {
            $conn->close();
            exit(json_encode(['status' => 400, 'message' => 'Required fields are missing!']));
        }

        $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $allowedExtensions = array("pdf", "doc", "docx", "txt", "jpeg", "jpg", "png", "gif");
        $fileNameNew = uniqid('', true) . '.' . $fileExt;

        if (in_array($fileExt, $allowedExtensions)) {
            if ($file["error"] === 0) {
                $uploadDir =  '../../uploads/assignments/';
                $uploadPath = $uploadDir . $fileNameNew;

                if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
                    $sql = "INSERT INTO Student_Submitted_Assignment (assignment_id, subject_id, student_id, uploaded_type, as_solutions_file) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);

                    if ($stmt) {
                        $stmt->bind_param("sssss", $assignment_id, $subject_id, $student_id, $uploaded_type, $uploadPath);

                        if ($stmt->execute()) {
                            echo json_encode(['status' => 200, 'message' => 'File Uploaded Successfully!']);
                        } else {
                            echo json_encode(['status' => 400, 'message' => 'Error executing SQL statement.']);
                        }

                        $stmt->close();
                    } else {
                        echo json_encode(['status' => 400, 'message' => 'Error preparing SQL statement.']);
                    }
                } else {
                    echo json_encode(['status' => 400, 'message' => 'Error moving uploaded file to destination directory.']);
                }
            } else {
                echo json_encode(['status' => 400, 'message' => 'Error uploading file.']);
            }
        } else {
            echo json_encode(['status' => 400, 'message' => 'Invalid file type. Allowed extensions: pdf, doc, docx, txt, jpeg, jpg, png, gif']);
        }

        $conn->close();
    } else {
        echo json_encode(['status' => 400, 'message' => 'Please select a file to upload and ensure all required fields are filled.']);
    }
} else {
    echo json_encode(['status' => 400, 'message' => 'Invalid request method.']);
}
