<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
    require '../../includes/db-config.php';
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $files_check_query = "SELECT id FROM Download_Centers WHERE id = '$id'";
    $result = $conn->query($files_check_query);

    if ($result->num_rows > 0) {
        $delete_query = "DELETE FROM Download_Centers WHERE id = '$id'";
        if ($conn->query($delete_query) === TRUE) {
            echo json_encode(['status' => 200, 'message' => 'Files deleted successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Error deleting file: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 404, 'message' => 'Files not found!']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 400, 'message' => 'Invalid request!']);
}
