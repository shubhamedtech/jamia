<?php
require '../../includes/db-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sub_courses'])) {
    $courseId = intval($_POST['sub_courses']);
    $query = "SELECT ID, Name FROM Subjects WHERE Program_Grade_ID = $courseId";
    $result = mysqli_query($conn, $query);
    $html = '<option value="">Choose Subjects</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
    }
    echo $html;
    die;
}
