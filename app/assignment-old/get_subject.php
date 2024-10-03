<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';
ini_set('display_errors', 1);
$courseId = intval($_POST['courseId']);
$sub = "SELECT ID,Name from Subjects WHERE Program_Grade_ID = $courseId";
$sub = mysqli_query($conn, $sub);
$html = '<option value="">Select Subjects</option>';
while ($row = mysqli_fetch_assoc($sub)) {
  $html = $html . '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
}
echo $html;
die;
