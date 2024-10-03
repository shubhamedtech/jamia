<?php
ini_set('max_execution_time', '300'); // 5 minutes  
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../includes/db-config.php';
session_start();

$cenid = isset($_POST['center']) ? $_POST['center'] : '';
$coursetype = isset($_POST['coursetype']) ? $_POST['coursetype'] : '';
$subject = isset($_POST['subject']) ? $_POST['subject'] : '';

// Start building the SQL query
$sqlQuery = "SELECT Students.*, 
                    CONCAT_WS(' ', Students.First_Name, Students.Middle_Name, Students.Last_Name) AS student_name, 
                    Students.Unique_ID AS unique_id,
                    Student_Submitted_Assignment.as_solutions_file as submitted_file,
                    Courses.Name as course_name,
                    Subjects.Name as subject_name,
                    Users.Name as center_name,
                    Users.ID as centerid,
                    Users.Code as center_code
             FROM Students
             LEFT JOIN Users ON Students.Added_For = Users.ID
             INNER JOIN Student_Submitted_Assignment ON Student_Submitted_Assignment.Student_ID = Students.ID
             INNER JOIN Courses ON Courses.ID = Students.Course_ID
             INNER JOIN Subjects ON Subjects.ID = Student_Submitted_Assignment.subject_id
             WHERE Users.Role IN ('Center', 'Sub-Center')";

// Array to hold the parameter types and values
$params = [];
$types = '';

if (!empty($cenid)) {
    $sqlQuery .= " AND Users.ID = ?";
    $types .= 'i';
    $params[] = $cenid;
}

if (!empty($coursetype)) {
    $sqlQuery .= " AND Students.Course_ID = ?";
    $types .= 'i';
    $params[] = $coursetype;
}

if (!empty($subject)) {
    $subjects_id_array = explode(',', $subject);
    $placeholders = implode(',', array_fill(0, count($subjects_id_array), '?'));
    $sqlQuery .= " AND Student_Submitted_Assignment.subject_id IN ($placeholders)";
    $types .= str_repeat('i', count($subjects_id_array));
    $params = array_merge($params, $subjects_id_array);
}
// Prepare the SQL statement
$stmt = $conn->prepare($sqlQuery);

if (!empty($types)) {
    // Bind parameters dynamically if there are any
    $stmt->bind_param($types, ...$params);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Debugging
error_log("Executing query: $sqlQuery");

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// $queueFile = 'queue.json';
// $queue = file_exists($queueFile) ? json_decode(file_get_contents($queueFile), true) : [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $files = explode(',', trim($row['submitted_file']));
        $course_name = trim($row['course_name']);
        $student_name = trim($row['student_name']);
        $subject_name = trim($row['subject_name']);
        $unique_id = trim($row['unique_id']);
        $center_name = trim($row['center_name']);

        if (!empty($course_name) && !empty($subject_name)) {
            $dir = $center_name . '/' . $course_name . '/' . $subject_name;
            foreach ($files as $file) {
                $file = trim($file);
                if (file_exists($file)) {
                    $file_path_in_zip = $dir . '/' . $unique_id . '_' . $student_name . '_' . basename($file);
                    $queue[] = ['file' => $file, 'path_in_zip' => $file_path_in_zip];
                } else {
                    error_log("File not found: $file");
                }
            }
        }
    }

    // file_put_contents($queueFile, json_encode($queue));
    processQueue(json_encode($queue));
} else {
    http_response_code(404);
    echo '<script type="text/javascript">
            alert("No records found.");
            window.location.href = "../../lms-settings/assignments-review";
          </script>';
}

function processQueue($data)
{
    // $queueFile = 'queue.json';
    $zipFileName = time().'.zip';

    // if (!file_exists($queueFile)) {
    //     exit("Queue file does not exist.\n");
    // }

    $queue = json_decode($data, true);
    if (empty($queue)) {
        exit("No files to zip.\n");
    }

    $zip = new ZipArchive();
    if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) {
        exit("Cannot open <$zipFileName>\n");
    }

    foreach ($queue as $entry) {
        $file = $entry['file'];
        $path_in_zip = $entry['path_in_zip'];

        // Ensure directory structure inside the ZIP
        $dir_in_zip = dirname($path_in_zip);
        if (!empty($dir_in_zip)) {
            $zip->addEmptyDir($dir_in_zip);
        }

        if (file_exists($file)) {
            $zip->addFile($file, $path_in_zip);
        } else {
            error_log("File $file does not exist.");
            $zip->addFromString($dir_in_zip . '/error.txt', "File not found: $file\n");
        }
    }

    if (!$zip->close()) {
        exit("Failed to close the zip file properly.\n");
    }
    if (file_exists($zipFileName)) {
        if (ob_get_length()) {
            ob_end_clean();
        }

        $fileSize = filesize($zipFileName);

// Set headers for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $fileSize);

// Clear the output buffer
ob_clean();
flush();

// Open the file for reading
$handle = fopen($zipFileName, 'rb');

// Check if the file could be opened
if ($handle === false) {
    http_response_code(500);
    exit('Error opening file.');
}

// Read and output the file in chunks
while (!feof($handle)) {
    // Read a chunk of the file
    $chunk = fread($handle, 8192);
    
    // Output the chunk
    echo $chunk;
    
    // Flush the output buffer
    ob_flush();
    flush();
}

// Close the file handle
fclose($handle);

        readfile($zipFileName);

        unlink($zipFileName);

    } else {
        exit("Failed to create the zip file.\n");
    }
}