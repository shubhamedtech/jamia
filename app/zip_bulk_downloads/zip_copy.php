<?php
ini_set('max_execution_time', '1440');
require '../../includes/db-config.php';
session_start();
$coursetype = isset($_POST['coursetype']) ? $_POST['coursetype'] : '';
$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
$centerid = isset($_POST['center']) ? intval($_POST['center']) : '';



// echo "<pre>";
// echo "center name => $centerid \n";
// echo "course type => $coursetype \n";
// echo "subject => $subject \n";

$sqlQuery = "SELECT Students.*, 
                    CONCAT_WS(' ', Students.First_Name, Students.Middle_Name, Students.Last_Name) AS student_name, 
                    Students.Unique_ID AS unique_id,
                    Student_Submitted_Assignment.as_solutions_file as submitted_file,
                    Courses.Name as course_name,
                    Subjects.Name as subject_name,
                    Users.Name as center_name,
                    Users.ID as centerid,Users.Code as center_code
             FROM Students
             LEFT JOIN Users ON Students.Added_For = Users.ID
             INNER JOIN Student_Submitted_Assignment ON Student_Submitted_Assignment.Student_ID = Students.ID
             INNER JOIN Courses ON Courses.ID = Students.Course_ID
             INNER JOIN Subjects ON Subjects.ID = Student_Submitted_Assignment.subject_id
             WHERE Users.Role IN ('Center', 'Sub-Center') and Users.ID = $centerid";

//  and Users.Role='Center'
// print_r($sqlQuery);


if (!empty($coursetype)) {
    $sqlQuery .= " AND Students.Course_ID = '" . $conn->real_escape_string($coursetype) . "'";
}



if (!empty($subject)) {
    $subjects_id_array = explode(',', $subject);
    $subjects_id_string = implode(',', array_map('intval', $subjects_id_array));
    $sqlQuery .= " AND Student_Submitted_Assignment.subject_id IN ($subjects_id_string)";
}





if (empty($centerid)) {
    $sqlQuery .= " AND Students.Added_For = '" . $conn->real_escape_string($centerid) . "'";
}


// echo "query =>". $sqlQuery;

error_log("Executing query: $sqlQuery");
$students_sql = $conn->query($sqlQuery);
if ($students_sql === false) {
    die("Error executing query: " . $conn->error);
}


if ($students_sql->num_rows > 0) {

    $row = $students_sql->fetch_assoc();
    $center_name = trim($row['center_name']);
    $center_code = trim($row['center_code']);
    // $centerid = trim($row['centerid']);
    $zip_file = $center_name.'('.$center_code.')' . '_assignments.zip';
    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE) !== TRUE) {
        error_log("Cannot create zip file.");
        http_response_code(500);
        exit("Cannot create zip file.");
    }
    $students_sql->data_seek(0);


    // while ($row = $students_sql->fetch_assoc()) {
    //     $file = explode(',', trim($row['submitted_file']));
    //     // $file = trim($row['submitted_file']);
    //     $course_name = trim($row['course_name']);
    //     $student_name = trim($row['student_name']);
    //     $subject_name = trim($row['subject_name']);
    //     $unique_id = trim($row['unique_id']);
    //     if (!empty($course_name) && !empty($subject_name)) {
    //         $dir = $center_name . '/' . $course_name . '/' . $subject_name;
    //         $file_path_in_zip = $dir . '/' . $unique_id . '_' . $student_name . ($file);
    //         error_log("Adding file to zip: $file_path_in_zip");
    //         if (file_exists($file)) {
    //             $zip->addFile($file, $file_path_in_zip);
    //         } else {
    //             error_log("File not found: $file");
    //             $zip->addFromString($dir . '/error.txt', "File not found: $file\n");
    //         }
    //     }
    // }


    while ($row = $students_sql->fetch_assoc()) {
        $files = explode(',', trim($row['submitted_file']));
        $course_name = trim($row['course_name']);
        $student_name = trim($row['student_name']);
        $subject_name = trim($row['subject_name']);
        $unique_id = trim($row['unique_id']);
        if (!empty($course_name) && !empty($subject_name)) {
            $dir = $center_name . '/' . $course_name . '/' . $subject_name;
            foreach ($files as $file) {
                $file = trim($file);
                $file_path_in_zip = $dir . '/' . $unique_id . '_' . $student_name . '_' . basename($file);
                error_log("Adding file to zip: $file_path_in_zip");
                if (file_exists($file)) {
                    $zip->addFile($file, $file_path_in_zip);
                } else {
                    error_log("File not found: $file");
                    $zip->addFromString($dir . '/error.txt', "File not found: $file\n");
                }
            }
        }
    }
    $zip->close();
    if (file_exists($zip_file)) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zip_file) . '"');
        header('Content-Length: ' . filesize($zip_file));
        readfile($zip_file);
        unlink($zip_file);
        exit;
    } else {
        error_log("Error creating zip file.");
        http_response_code(500);
        exit("Error creating zip file.");
    }
} else {
    http_response_code(404);
    header('location:/../../lms-settings/assignments-review');
    exit("No records found.");
}
