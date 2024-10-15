<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
// Required files
require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');
// Database configuration
include '../../includes/db-config.php';
// Start session and get student data
session_start();
//  $_GET['student_id'];
if (isset($_GET['student_id'])) {
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';
     $id = intval(base64_decode(mysqli_real_escape_string($conn, $_GET['student_id'])));
    $student = $conn->query("SELECT Students.*, Users.Name as Center, Users.Code as Center_Code, Users.ID as Center_ID, Users.Photo as Center_Seal, Users.City as Center_City, UPPER(Courses.Name) as Course, Sub_Courses.Name as Sub_Course, Admission_Sessions.Name as `Session`, Admission_Sessions.Exam_Session, Admission_Types.Name as Type FROM Students 
    LEFT JOIN Users ON Students.Added_For = Users.ID 
    LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
    LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID 
    LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID 
    LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.ID = $id");
    $student = mysqli_fetch_assoc($student);
    $address = json_decode($student['Address'], true);
    if (strlen($student['Center_Code']) > 4) {
        $subcenter_code = $student['Center_ID'];
        $center = $conn->query("SELECT Users.Name as CName, Users.Code as CCode, Users.Photo as CPhoto, Users.City as CCity FROM Users JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE Center_SubCenter.Sub_Center = $subcenter_code");
        $center = mysqli_fetch_assoc($center);
    }

    require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

    $check = '../../assets/img/form/checked.png';

    $pdf = new Fpdi();
    $pdf->SetTitle('Jamia Admit Card');
    $pdf->setSourceFile('admit-card_1.pdf');

    $pdf->SetFont('Arial', 'B', 11);

    // Tick Image
    $check = '../../assets/img/form/checked.png';

    // Extensions
    $file_extensions = array('.png', '.jpg', '.jpeg');

    //this folder will have there images.
    $path = "photos/";

    // Photo
    $student_photo = "";
    $photo = "";
    $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
    if ($photo->num_rows > 0) {
        //   $photo = mysqli_fetch_assoc($photo);
        //   $photo = "../.." . $photo['Location'];
        //   $student_photo = $photo?base64_encode(file_get_contents($photo??"")):"";
        //   $i = 0;
        //   $end = 3;
        //   while ($i < $end) {
        //     $data1 = base64_decode($student_photo);

        //     $filename1 = $id . "_Photo" . $file_extensions[$i];
        //     file_put_contents($filename1, $data1); //we save our new images to the path above
        //     $i++;
        //   }
    } else {
        $photo = "";
    }

    // Signature
    $student_signature = "";
    $signature = "";
    $signature = $conn->query("SELECT Location FROM Student_Documents
    WHERE Student_ID = $id AND Type = 'Student Signature'");
    if ($signature->num_rows > 0) {
        $signature = mysqli_fetch_assoc($signature);
        $signature = "../.." . $signature['Location'];
        //   $student_signature = base64_encode(file_get_contents($signature));
        $i = 0;
        $end = 3;
        while ($i < $end) {
            $data2 = base64_decode($student_signature);
            $filename2 = $id . "_Student_Signature" . $file_extensions[$i];
            //$file_extensions loops through the file extensions
            file_put_contents($filename2, $data2); //we save our new images to the path above
            $i++;
        }
    } else {
        $signature = "";
    }

    // Page 1
    $pdf->AddPage();
    $tplId = $pdf->importPage(1);
    // use the imported page and place it at point 10,10 with a width of 100 mm
    $pdf->useTemplate($tplId, 0, 0, 210);

    $pdf->SetFont('Arial', '', 11);

    // Session
    $pdf->SetXY(154, 13.5);
    $pdf->Write(1, $student['Session']);

    $pdf->SetXY(72, 28);
    $pdf->Write(1, $student['Course']);

    // Photo
    $photo = "";
    if ($photo != '' && file_exists($photo) && filetype($photo) === 'file') {
        try {
            $filename = $id . "_Photo" . $file_extensions[0];
            $image = $filename;
            $pdf->Image($image, 12, 41, 36, 38.5);
            $photo = $image;
        } catch (Exception $e) {
            try {
                $filename = $id . "_Photo" . $file_extensions[1];
                $image = $filename;
                $pdf->Image($image, 12, 41, 36, 38.5);
                $photo = $image;
            } catch (Exception $e) {
                try {
                    $filename = $id . "_Photo" . $file_extensions[2];
                    $image = $filename;
                    $pdf->Image($image, 12, 41, 36, 38.5);
                    $photo = $image;
                } catch (Exception $e) {
                    //echo 'Message: ' . $e->getMessage();
                }
            }
        }
    }

    // $pdf->Image('sign.png', 19, 71, 20, 20);

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetXY(78, 39.5);
    $pdf->Multicell(71, 3, $student['Center'], 0, 'L', false);

    // Student Name
    $student_name = array($student['First_Name'], $student['Middle_Name'], $student['Last_Name']);
    $pdf->SetXY(78, 56);
    $pdf->Write(1, implode(" ", array_filter($student_name)));

    // Father Name
    $father_name = $student['Father_Name'];
    $pdf->SetXY(78, 70);
    $pdf->Write(1, $father_name);

    // Roll No
    $pdf->SetXY(78, 83);
    $pdf->Write(1, $student['Roll_No']);

    // Enrollment No
    $pdf->SetXY(78, 97.3);
    $pdf->Write(1, $student['Enrollment_No']);

    // Subjects
    $allSubject = array();
    $subjects = $conn->query("SELECT Subjects.Name FROM `Student_Subjects` LEFT JOIN Subjects ON Student_Subjects.Subject_id = Subjects.ID WHERE Student_Id = $id");
    while ($subject = $subjects->fetch_assoc()) {
        $allSubject[] = $subject['Name'];
    }

    $pdf->SetXY(78, 108);
    $pdf->Multicell(84, 7.7, implode(", ", $allSubject), 0, 'L', false);



    // Signature
    $signature = "";
    if (filetype($signature) === 'file' && file_exists($signature)) {
        try {
            $filename = $id . "_Student_Signature" . $file_extensions[0];
            $image = $filename;
            $pdf->Image($image, 78, 124, 32, 10);
            $student_signature = $image;
        } catch (Exception $e) {
            try {
                $filename = $id . "_Student_Signature" . $file_extensions[1];
                $image = $filename;
                $pdf->Image($image, 78, 124, 32, 10);
                $student_signature = $image;
            } catch (Exception $e) {
                try {
                    $filename = $id . "_Signature" . $file_extensions[2];
                    $image = $filename;
                    $pdf->Image($image, 78, 124, 32, 10);
                    $student_signature = $image;
                } catch (Exception $e) {
                    // echo 'Message: ' . $e->getMessage();
                }
            }
        }
    }

    $i = 0;
    $end = 3;
    while ($i < $end) {
        // Delete Photos
        if (!empty($student_photo)) {
            $filename = $id . "_Photo" . $file_extensions[$i];
            //$file_extensions loops through the file extensions
            unlink($filename);
        }

        // Delete Signatures
        if (!empty($student_signature)) {
            $filename = $id . "_Student_Signature" . $file_extensions[$i];
            //$file_extensions loops through the file extensions
            unlink($filename);
        }
        $i++;
    }

    $pdf->Output('I', 'JamiaAdmitCard.pdf');
}
