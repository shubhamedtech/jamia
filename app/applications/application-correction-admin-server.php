<?php
include '../../includes/db-config.php';
session_start();

$query = "select *, GROUP_CONCAT(subjects.Name) as subjects from correction_form  left join student_subjects on `student_subjects`.`Student_Id`=correction_form.students_id left join subjects on subjects.ID=student_subjects.Subject_id left join students on students.ID = correction_form.students_id group by correction_form.students_id";

$runQuery = $conn->query($query);

while($row = mysqli_fetch_assoc($runQuery))
{
    $name = implode(' ',[$row['First_Name'],$row['Middle_Name'],$row['Last_Name']]);
    $data[] = array(
        "First_Name" =>  $name,
        "Father_Name" => $row['Father_Name'],
        "Mother_Name" => $row['Mother_Name'],
        "Unique_ID" => $row['Unique_ID'],
        "Enrollment_No" => !empty($row['Enrollment_No']) ? $row['Enrollment_No'] : '',
        "DOB" => $row['DOB'],
        "ID" => base64_encode($row['ID'] . 'W1Ebt1IhGN3ZOLplom9I'),
        "form_status" => $row['form_status'],
        'correction_data' => $row['correction_data'],
        'Subjects' => $row['subjects'],
        'correctionFormStatus' => $row['status']
    );
}

$response = array(
    "draw" => "",
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
);

echo json_encode($response);

?>