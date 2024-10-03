
<?php
require '../../includes/db-config.php';
session_start();
if (!isset($_SESSION['ID'])) {
    echo json_encode(['status' => 400, 'message' => 'You are not logged in.']);
    exit;
}
if (isset($_POST['upload_assignment'])) {
    $assignment_id = $conn->real_escape_string($_POST['assignment_id']);
    $subject_id = $conn->real_escape_string($_POST['subject_id']);
    $student_id = $conn->real_escape_string($_SESSION['ID']);
    $uploaded_type = $conn->real_escape_string($_POST['uploaded_type']);

    $targetDir = '../../uploads/assignments/';
    $allowedTypes = ['pdf', 'jpeg', 'jpg', 'png', 'gif'];
    $maxFileSize = 20 * 1024 * 1024;
    $filePaths = [];
    foreach ($_FILES["assignment_files"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $fileName = basename($_FILES["assignment_files"]["name"][$key]);
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(['status' => 400, 'message' => 'Only PDF, JPEG, PNG, GIF, or JPG files are allowed.']);
                exit;
            }
            if ($_FILES["assignment_files"]["size"][$key] > $maxFileSize) {
                echo json_encode(['status' => 400, 'message' => 'Each file size should be less than 20 MB.']);
                exit;
            }
            $fileNameNew = uniqid() . '.' . $fileType;
            $uploadFile = $targetDir . $fileNameNew;
            if (move_uploaded_file($_FILES["assignment_files"]["tmp_name"][$key], $uploadFile)) {
                $filePaths[] = $uploadFile;
            } else {
                echo json_encode(['status' => 400, 'message' => 'Error moving uploaded file.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 400, 'message' => 'File upload error.']);
            exit;
        }
    }

    if (!empty($filePaths)) {
        $created_date = date('Y-m-d H:i:s');
        // $filesSerialized = serialize($filePaths);
        $filesSerialized = implode(',', $filePaths);

        $stmt = $conn->prepare("SELECT * FROM Student_Submitted_Assignment WHERE assignment_id=? AND student_id=? AND subject_id=?");
        $stmt->bind_param('iii', $assignment_id, $student_id, $subject_id);
        $stmt->execute();
        $existingData = $stmt->get_result();

        if ($existingData->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE Student_Submitted_Assignment SET as_solutions_file=?, created_date=?, reuploaded=1 WHERE assignment_id=? AND subject_id=? AND student_id=?");
            $stmt->bind_param('ssiii', $filesSerialized, $created_date, $assignment_id, $subject_id, $student_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO Student_Submitted_Assignment (assignment_id, subject_id, student_id, uploaded_type, as_solutions_file, created_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iiisss', $assignment_id, $subject_id, $student_id, $uploaded_type, $filesSerialized, $created_date);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 200, 'message' => 'Assignment Submitted Successfully!']);
            header('location:/../student/lms/assignments');
            exit;
        } else {
            echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
            exit;
        }
    } else {
        echo json_encode(['status' => 400, 'message' => 'No files uploaded.']);
        exit;
    }
} else {
    echo json_encode(['status' => 400, 'message' => 'Invalid request.']);
    header('location:/../student/lms/assignments');
    exit;
}
?>
