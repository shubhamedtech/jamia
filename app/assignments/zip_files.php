<?php
if (isset($_GET['files']) && isset($_GET['stu_unique_id']) && isset($_GET['student_name']) && isset($_GET['taskname'])) {
    $files = explode(',', $_GET['files']);
    $stu_unique_id = $_GET['stu_unique_id'];
    $student_name = $_GET['student_name'];
    $task_name = $_GET['taskname'];
    $zip = new ZipArchive();
    $zipName = tempnam(sys_get_temp_dir(), $stu_unique_id . '_' . $student_name . $task_name . '_assignments_') . '.zip';
    error_log("Creating zip file: $zipName");


    if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
        error_log("Cannot create zip file.");
        http_response_code(500);
        exit("Cannot create zip file.");
    }

    foreach ($files as $file) {
        $file = trim($file);
        error_log("Adding file to zip: $file");
        if (file_exists($file)) {
            $zip->addFile($file, basename($file));
        } else {
            error_log("File not found: $file");
            $zip->addFromString('error.txt', "File not found: $file\n");
        }
    }
    $zip->close();
    if (file_exists($zipName)) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $stu_unique_id . '_' . $student_name . '_' . $task_name . '_' . '_assignments.zip"');
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);
        unlink($zipName);
        exit;
    } else {
        error_log("Error creating zip file.");
        http_response_code(500);
        exit("Error creating zip file.");
    }
} else {
    http_response_code(400);
    exit("No files or subject name specified.");
}
