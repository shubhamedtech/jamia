<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['id'])) {
    print_r($_POST['id']);
    require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
    $id = intval($_POST['id']);
    $reason = $_POST['reason'];
    $status = $_POST['status'];
    $marks = intval($_POST['marks']);
    $sql = "UPDATE Student_Assignment_Result SET status=?, obtained_mark=?, remark=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['status' => 0, 'message' => 'Database error: unable to prepare statement']);
        exit;
    }
    $stmt->bind_param("sisi", $status, $marks, $reason, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 1, 'message' => 'Result Proper Updated Successfully!']);
        header('location:/../lms-settings/assignments-review');
    } else {
        echo json_encode(['status' => 0, 'message' => 'Something went wrong while updating the result in the database.']);
    }
    $stmt->close();
    $conn->close();
}
