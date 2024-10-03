<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
    require '../../includes/db-config.php';
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $assignment_check_query = "SELECT id FROM Student_Assignment WHERE id = $id";
    $result = $conn->query($assignment_check_query);
    if ($result->num_rows > 0) {
        $delete_query = "DELETE FROM Student_Assignment WHERE id = $id";
        if ($conn->query($delete_query)) {
            echo json_encode(['status' => 200, 'message' => 'Assignment deleted Successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Error deleting assignment: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 404, 'message' => 'Assignment not found!']);
    }
    $conn->close();
} else {
    echo json_encode(['status' => 400, 'message' => 'Invalid request!']);
}
