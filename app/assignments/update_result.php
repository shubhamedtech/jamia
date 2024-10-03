<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_ids = intval($_POST['stu_id']);
    $assignment_id = intval($_POST['assignment_id']);
    $obtained_marks = intval($_POST['marks']);
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    // Fetch the allotted marks
    $get_marks_sql = "SELECT sa.marks FROM Student_Assignment AS sa WHERE sa.subject_id = ?";
    if ($stmt = $conn->prepare($get_marks_sql)) {
        $stmt->bind_param("i", $subject_ids);
        $stmt->execute();
        $stmt->bind_result($allotted_marks);
        $stmt->fetch();
        $stmt->close();

        // Check if obtained marks are within the allowed range
        if ($obtained_marks > $allotted_marks) {
            echo json_encode(['status' => 400, 'message' => 'Marks should be less than or equal to allotted marks']);
            exit;
        }

        // Prepare and execute the query to insert result
        $insert_query = "INSERT INTO Student_Assignment_Result (assignment_id, obtained_mark, remark, status) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($insert_query)) {
            $stmt->bind_param("iiss", $assignment_id, $obtained_marks, $reason, $status);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 200, 'message' => 'Result Uploaded Successfully!']);
            } else {
                echo json_encode(['status' => 400, 'message' => 'Something went wrong while inserting the result into the database.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 500, 'message' => 'Error preparing statement for result insertion: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 500, 'message' => 'Error preparing statement for fetching marks: ' . $conn->error]);
    }
}
