<?php
if (isset($_POST['id']) && $_POST['formstatus']) {
    require '../../../includes/db-config.php';

    $id = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['formstatus']);

    if (empty($status)) {
        echo json_encode(['status' => 400, 'message' => 'Form Status Value is required.']);
        exit();
    }

    $update = $conn->query("UPDATE Students SET form_status = '$status' WHERE ID = $id");
    if ($update) {
        echo json_encode(['status' => 200, 'message' => 'Form Status is  Updated Successfully!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
}
