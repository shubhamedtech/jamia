<?php

ini_set('display_errors', 1);
session_start();
    
if ($_SESSION['Role'] == 'Center') {
    require '../../includes/db-config.php';
    
    $delimiter = ",";
    $filename = "Date-Sheet.csv";


    // Create a file pointer 
    $f = fopen('php://memory', 'w');

    $fields = array('Exam Session', 'Course Name', 'Paper Name', 'Exam Date (dd-mm-yyyy)', 'Start Time', 'End Time');
    fputcsv($f, $fields, $delimiter);

    $allowed_extensions = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );


    $where_cnd = "";
    if (isset($_REQUEST['course_id']) && !empty($_REQUEST['course_id'])) {
        $course_id = intval($_REQUEST['course_id']);
        $where_cnd = "WHERE Courses.ID = $course_id";
    }
    
    if ( isset($_REQUEST['session_id']) && !empty($_REQUEST['session_id'])) {
        $session_id = intval(($_REQUEST['session_id']));
        $where_cnd .= (empty($where_cnd)) ? "WHERE Date_Sheets.Exam_Session_ID = $session_id" : " And Date_Sheets.Exam_Session_ID = $session_id";
    } 
    
    $searchQuery = "";
    if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
        $likeSearchValue = "%".$_REQUEST['search']."%";
        $searchQuery = (empty($where_cnd)) ? "WHERE Subjects.Name LIKE '$likeSearchValue' " : " AND Subjects.Name LIKE '$likeSearchValue' ";
    }

    // Total record with filter 
    $date_sheets = $conn->query("SELECT Exam_Sessions.Name as `Exam session` , Courses.Name as `Course Name` , Subjects.Name as `Paper Name`,Date_Sheets.Exam_Date as `Exam Date` , Date_Sheets.Start_Time as `Start Time`, Date_Sheets.End_Time as `End Time` FROM `Date_Sheets` LEFT JOIN Exam_Sessions ON Exam_Sessions.ID = Date_Sheets.Exam_Session_ID LEFT JOIN Subjects ON Subjects.id = Date_Sheets.Syllabus_ID LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID $where_cnd $searchQuery");


    $totalRecordwithFilter = $date_sheets->num_rows;
  
    if ($totalRecordwithFilter > 0 ) {
        while( $date_sheet = $date_sheets->fetch_assoc()) {
            $row = [];
            $row[] = $date_sheet['Exam session'];
            $row[] = $date_sheet['Course Name'];
            $row[] = $date_sheet['Paper Name'];
            $row[] = $date_sheet['Exam Date'];
            $row[] = $date_sheet['Start Time'];
            $row[] = $date_sheet['End Time'];

            fputcsv($f,$row,$delimiter);
        }
    } else {
        $row[] = "Empty record";
        fputcsv($f,$row,$delimiter);
    }

    fseek($f, 0);
    // Set headers to download file rather than displayed 
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');


    //output all remaining data on a file pointer 
    fpassthru($f);
}
?>