<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $students = $conn->query("SELECT * FROM University_User WHERE University_ID = $id");
  if ($students->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => 'Please delete exiting users first!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Universities WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM Universities WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'University deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'University not exists!']);
  }
}
