<?php
require '../includes/db-config.php';
require '../includes/helpers.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$query = "
  SELECT DISTINCT Students.ID,
    CONCAT(Students.First_Name, ' ', Students.Last_Name) AS Full_Name, 
    Students.Father_Name, 
    Students.Mother_Name, 
    Students.DOB, 
    Students.Gender, 
    Students.Enrollment_No, 
    Students.Roll_No, 
    Students.OA_Number,
    Students.Unique_ID,
    Courses.Name AS Stream,
    Admission_Sessions.Name AS Admission_Session,
    Users.ID AS Center_ID,
    Users.Name AS CenterName,
	Users.Short_Name AS ShortName,
     CASE
     WHEN Users.Code= '0010.4' OR Users.Code='0010' THEN 'JUA-922'
     WHEN Users.Code='0008' THEN 'JUA-921'
     WHEN Users.Code IN ('0009', '0011', '0013', '0014', '0015', '0016', '0017', '0018', '0004', '0020', '0021', '0022', '0023', '0024', '0025', '0026', '0027', '0028', '0029', '0030', '0031', '0032', '0033', '0034', '0035', '0036', '0038', '0039', '0049') 
        THEN 'JUA-951'
     ELSE Users.Code
     END  AS CenterCode,    
    GROUP_CONCAT(CASE WHEN Subjects.Category = 'Language' THEN Subjects.Name END) AS RegionalLanguage,  
    GROUP_CONCAT(CASE WHEN Subjects.Category = 'Others' THEN Subjects.Name END) AS other_subjects
    
FROM Students 
LEFT JOIN Users ON Students.Added_For = Users.ID 
LEFT JOIN new_exam_form 
    ON CONCAT(new_exam_form.Center_Name, ' ', '(', new_exam_form.Center_Code, ')') = Users.Name 
LEFT JOIN Admission_Sessions 
    ON Students.Admission_Session_ID = Admission_Sessions.ID 
LEFT JOIN Student_Subjects ON Students.ID = Student_Subjects.Student_Id
LEFT JOIN Courses ON Students.Course_ID= Courses.ID
LEFT JOIN Subjects ON Student_Subjects.Subject_id = Subjects.ID
WHERE Students.Exam= '1' 
  AND Admission_Sessions.Name = 'JUL-2024'
GROUP BY Students.ID ";
$path = 'examform';
$fileName = 'exam_one';

// Execute the query
$result = $conn->query($query);

// Fetch the result
$checke_image = './../assets/img/form/checked.png';

use setasign\Fpdi\PdfReader;
use setasign\Fpdi\Fpdi;

require '../includes/db-config.php';
require_once('../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../extras/vendor/setasign/fpdi/src/autoload.php');
require '../extras/vendor/autoload.php';
//echo $result->num_rows;die;

$pdf_dir = '../uploads/examform/';
if ($result->num_rows > 0) {
    $i = 0;
  	
    while ($students = $result->fetch_assoc()) {
        // echo $result->num_rows;
         if(empty($students['ShortName']))
         {
           	$centerID = getCenterIdFunc($conn,$students['Center_ID']);
           	$centerQuery = "SELECT Short_Name FROM Users WHERE ID = ".$centerID;
  			$centerData	 = 	$conn->query($centerQuery)->fetch_assoc();
  			$students['ShortName'] = $centerData['Short_Name'];
         }
      //echo "<pre>";print_r($students);die;
        $pdf_dir = '../uploads/examform/' . preg_replace('/[^A-Za-z0-9\-]/', '',$students['ShortName']) . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $students['Stream']) . '/';
        if (is_dir($pdf_dir) === false) {
            mkdir($pdf_dir, 0777, true);
        }
        $pdf = new Fpdi();
        $pdf->SetTitle('Jamia | Award Sheet');
        $pageCount = $pdf->setSourceFile('examform.pdf');
        $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
        $pdf->addPage();
        $pdf->useImportedPage($pageId);
        $pdf->SetFont('Arial', 'I', 12);
		$pdf->SetXY(66, 56);
        $pdf->Write(0, $students['Admission_Session']);
        // Subject categorization
        $science_stream = explode(',', $students['other_subjects']);

        // Stream and subjects logic
        if (trim(strtolower($students['Stream'])) == 'adeeb-e-mahir (arts)') {
            $pdf->Image($checke_image, 33, 61, 3, 3);
            $pdf->SetXY(114, 66.5);
            $pdf->Write(0, "(" . $students['other_subjects'] . ")");
        } else if (trim(strtolower($students['Stream'])) == 'adeeb-e-mahir (commerce)') {
            $pdf->Image($checke_image, 45, 61, 3, 3);
            $pdf->SetXY(114, 66.5);
            $pdf->Write(0, "(" . $students['other_subjects'] . ")");
        } else if (trim(strtolower($students['Stream'])) == 'adeeb-e-mahir (science)') {
            if (in_array('Mathematics', $science_stream)) {
                $pdf->Image($checke_image, 105, 61, 3, 3);
                $pdf->SetXY(114, 66.5);
                $pdf->Write(0, 'PCM');
            } else if (in_array('Biology', $science_stream)) {
                $pdf->Image($checke_image, 92, 61, 3, 3);
                $pdf->SetXY(114, 66.5);
                $pdf->Write(0, 'PCB');
            }
        }
      else
      {
            $pdf->SetXY(114, 66.5);
            $pdf->Write(0, "(" . $students['other_subjects'] . ")");
      }

        // Writing student information
        $dob_parts = str_split(str_replace('-', '', $students['DOB']));
        $x_positions = [80, 88, 96, 103, 62, 68, 43, 50];
        foreach ($dob_parts as $index => $dob_student) {
            $pdf->SetXY($x_positions[$index], 126);
            $pdf->Write(1, $dob_student);
        }
		
        $pdf->SetXY(64, 90);
        $pdf->Write(0, $students['Full_Name']);
        $pdf->SetXY(56, 80);
        $pdf->Write(0, $students['RegionalLanguage']);
        $pdf->SetXY(45, 106);
        $pdf->Write(0, $students['Father_Name']);
        $pdf->SetXY(46, 116);
        $pdf->Write(0, $students['Mother_Name']);
        if ($students['Gender'] == 'Male') {
            $pdf->Image($checke_image, 156, 124, 3, 3);
        } else if ($students['Gender'] == 'Female') {
            $pdf->Image($checke_image, 186, 124, 3, 3);
        }
        $pdf->SetXY(42, 140.4);
        $pdf->Write(0, $students['Enrollment_No']);
        $pdf->SetXY(144, 140.4);
        $pdf->Write(0, $students['OA_Number']??"");
        $pdf->SetXY(57, 175);
        $pdf->Write(0, $students['ShortName']);
       // $pdf->SetXY(175, 170.4);
       // $pdf->Write(0, $students['CenterCode']);

        // Generate unique PDF filename
        $filename = $pdf_dir . $students['Unique_ID'] . '_' . str_replace(' ', '_', $students['Full_Name']) . ".pdf";
        $file_path_in_zip = preg_replace('/[^A-Za-z0-9\-]/', '_',$students['ShortName']) . '/' . preg_replace('/[^A-Za-z0-9\-]/', '_', $students['Stream']) . '/'. $students['Unique_ID'] . '_' . str_replace(' ', '_', $students['Full_Name']) . ".pdf";
        $pdf->Output($filename, 'F');
        $allFiles[$i]['file'] = $filename;
        $allFiles[$i]['path_in_zip'] = $file_path_in_zip;
        $i++;
    }

    processQueue(json_encode($allFiles));
    // $zip = new ZipArchive();
    // $zip_file = $pdf_dir . time() . '.zip';
    // if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    //     $files = $allFiles;
    //    	//$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pdf_dir),RecursiveIteratorIterator::LEAVES_ONLY);
    //     foreach ($files as $file) {
    //         $zip->addFile($file, basename($file));
    //     }
    //     $zip->close();

    //     header('Content-Type: application/zip');
    //     header('Content-disposition: attachment; filename=' . basename($zip_file));
    //     header('Content-Length: ' . filesize($zip_file));
    //     readfile($zip_file);

    //     foreach ($files as $file) {
    //         unlink($file);
    //     }
    //     unlink($zip_file);
    // }
}



function processQueue($data)
{
    // $queueFile = 'queue.json';
    $zipFileName = time() . '.zip';

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
        http_response_code(500);
    }
}
