<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_id = intval($_POST['assignment_id']);
    $marks = $_POST['marks'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];
    $sql = "INSERT INTO Student_Assignment_Result (assignment_id,obtained_mark,remark,status) VALUES (?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iiss", $assignment_id, $marks, $reason, $status);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 200, 'message' => 'Result Proper Uploaded Successfully!']);
        } else {
            echo json_encode(['status' => 400, 'message' => 'Something went wrong while inserting the file path into the database.']);
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
