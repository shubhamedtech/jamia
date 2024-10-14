<?php
ini_set('memory_limit', '-1');
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['student_ids'])) {
  require '../../includes/db-config.php';
  session_start();

  if ($_SESSION['university_id'] == 48) {

    require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

    $check = '../../assets/img/form/checked.png';

    $pdf = new Fpdi();
    $pdf->SetTitle('Jamiya Application Form');
    $pageCount = $pdf->setSourceFile('jamia-form.pdf');

    $pdf->SetFont('Arial', 'B', 11);

    $ids = mysqli_real_escape_string($conn, $_GET['student_ids']);
    $ids = explode(",", $ids);

    // Extensions
    $file_extensions = array('.png', '.jpg', '.jpeg');

    //this folder will have there images.
    $path = "photos/";

    $students = $conn->query("SELECT Students.*, Users.Name as Center, Users.Code as Center_Code, Users.ID as Center_ID, Users.Photo as Center_Seal, Users.City as Center_City, Courses.Name as Course, Sub_Courses.Name as Sub_Course, Admission_Sessions.Name as `Session`, Admission_Types.Name as Type FROM Students LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.Unique_ID IN ('" . implode("','", $ids) . "')");
    while ($student = $students->fetch_assoc()) {
        $id = $student['ID'];
      $address = json_decode($student['Address'], true);
      if (strlen($student['Center_Code']) > 4) {
        $subcenter_code = $student['Center_ID'];
        $center = $conn->query("SELECT Users.Name as CName, Users.Code as CCode, Users.Photo as CPhoto, Users.City as CCity FROM Users JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE Center_SubCenter.Sub_Center = $subcenter_code");
        $center = mysqli_fetch_assoc($center);
      }

      // Photo
      $student_photo = "";
      $photo = "";
      $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
      if ($photo->num_rows > 0) {
        $photo = mysqli_fetch_assoc($photo);
        $photo = "../.." . $photo['Location'];
        $student_photo = base64_encode(file_get_contents($photo));
        $i = 0;
        $end = 3;
        while ($i < $end) {
          $data1 = base64_decode($student_photo);

          $filename1 = $id . "_Photo" . $file_extensions[$i];
          file_put_contents($filename1, $data1); //we save our new images to the path above
          $i++;
        }
      } else {
        $photo = "";
      }

      // Signature
      $student_signature = "";
      $signature = "";
      $signature = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Student Signature'");
      if ($signature->num_rows > 0) {
        $signature = mysqli_fetch_assoc($signature);
        $signature = "../.." . $signature['Location'];
        $student_signature = base64_encode(file_get_contents($signature));
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
      $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
      $pdf->addPage();
      $pdf->useImportedPage($pageId, 0, 0, 210);

      $pdf->SetFont('Arial', '', 11);

      // Session
      $pdf->SetXY(153, 46.5);
      $pdf->Write(1, $student['Session']);

      // Enrollment No.
      $enrollment_no = str_split($student['Enrollment_No']);
      $pdf->SetXY(67, 170.5);
      $pdf->Write(1, $student['Enrollment_No']);

      // Programme
      //$pdf->SetXY(30, 98);
      //if($student['Course']  == 'adeeb'){
      //$pdf->Image($check, 40, 97.5, 2, 3);
      //}else{
      //$pdf->Image($check, 90, 97.5, 2, 3);
      //}
      //$pdf->SetXY(15, 102);
      //if ($student['Course']  = 'adeeb') {
      // }

      if ($student['Course']  == 'adeeb-e-mahir (Commerce)') {
        $pdf->SetXY(15, 102);
        $pdf->Write(1, strtoupper('adeeb-e-mahir-2 (Commerce)'));
        $pdf->Image($check, 67, 110, 4, 4);
      } else if ($student['Course']  == 'adeeb-e-mahir (Science)') {
        $pdf->SetXY(15, 102);
        $pdf->Write(1, strtoupper('adeeb-e-mahir-2 (Science)'));
        $pdf->Image($check, 90, 110, 4, 4);
      } else if ($student['Course']  == 'adeeb-e-mahir (Arts)') {
        $pdf->SetXY(15, 102);
        $pdf->Write(1, strtoupper('adeeb-e-mahir-2 (Arts)'));
        $pdf->Image($check, 38, 110, 4, 4);
      } else {
        $pdf->SetXY(15, 102);
        $pdf->Write(1, strtoupper($student['Course']));
      }

      // Medium of Instruction
      $pdf->Image($check, 130, 156, 4, 4);
      // Subjects
      $st_subjects = $conn->query("SELECT Subjects.Name, Subjects.Category FROM Student_Subjects LEFT JOIN Subjects ON Student_Subjects.Subject_id = Subjects.ID  WHERE Student_Subjects.Student_Id = $id");
      $x = 15;
      $y = 121.5;
      while ($st_subject = mysqli_fetch_assoc($st_subjects)) {
        if ($st_subject['Category'] == 'Language') {
          if ($st_subject['Name'] == 'Urdu') {
            //$pdf->Image($check, 140, 156, 4, 4);
          } else if ($st_subject['Name'] == 'English') {
            //$pdf->Image($check, 130, 156, 4, 4);
          } else {
            $pdf->SetXY(42, 157.6);
            $pdf->Write(1, strtoupper($st_subject['Name']));
          }
        }
        if ($student['Course']  != 'adeeb') {

          if ($st_subject['Name'] == 'Biology') {
            $pdf->Image($check, 88, 147, 3, 3);
          } else if ($st_subject['Name'] == 'Mathematics') {
            $pdf->Image($check, 88, 140, 3, 3);
          } else if ($st_subject['Name'] == 'Political Science') {
            $pdf->Image($check, 68.5, 135, 3, 3);
          } else if ($st_subject['Name'] == 'History') {
            $pdf->Image($check, 68.5, 130, 3, 3);
          } else if ($st_subject['Name'] == 'Geography') {
            $pdf->Image($check, 88, 130, 3, 3);
          } else if ($st_subject['Name'] == 'Sociology') {
            $pdf->Image($check, 88, 140, 3, 3);
          }
        } else {

          if ($st_subject['Name'] == 'Mathematics') {
            $pdf->Image($check, 33.5, 132, 3, 3);
          } else if ($st_subject['Name'] == 'Home Science') {
            $pdf->Image($check, 33.5, 144, 3, 3);
          }
        }


        //if($y > 151){
        // $x = 130;
        // $y = 121.5;
        //}
        //$pdf->SetXY($x, $y);
        //$pdf->Write(1, $st_subject['Name']);
        //$y = $y + 6;
        //}
        //if($student['Course']  == 'adeeb'){
        // $pdf->Image($check, 40, 97.5, 2, 3);
        //}else{
        // $pdf->Image($check, 90, 97.5, 2, 3);
      }

      // Photo
      if (filetype($photo) === 'file' && file_exists($photo)) {
        try {
          $filename = $id . "_Photo" . $file_extensions[0];
          $image = $filename;
          $pdf->Image($image, 13, 22.3, 30.5, 35.9);
          $photo = $image;
        } catch (Exception $e) {
          try {
            $filename = $id . "_Photo" . $file_extensions[1];
            $image = $filename;
            $pdf->Image($image, 13.5, 23, 30.5, 35.9);
            $photo = $image;
          } catch (Exception $e) {
            try {
              $filename = $id . "_Photo" . $file_extensions[2];
              $image = $filename;
              $pdf->Image($image, 13, 22.3, 30.5, 35.9);
              $photo = $image;
            } catch (Exception $e) {
              echo 'Message: ' . $e->getMessage();
            }
          }
        }
      }

      // Student Name
      $student_name = str_split(str_replace('  ', ' ', $student['First_Name'] . " " . $student['Middle_Name'] . " " . $student['Last_Name']));

      $x = 51;
      $y = 194.7;
      foreach ($student_name as $name) {
        if ($x > 190) {
          $y = $y + 5;
          $x = 51;
          $pdf->SetXY($x, $y);
        } else {
          $pdf->SetXY($x, $y);
        }
        $pdf->Write(1, $name);
        $x += 7.7;
      }

      // Father Name
      $father_name = str_split($student['Father_Name']);
      $x = 51;

      foreach ($father_name as $name) {
        $pdf->SetXY($x, 218);
        $pdf->Write(1, $name);
        $x += 7.7;
      }

      // Mother Name
      $mother_name = str_split($student['Mother_Name']);
      $x = 51;
      foreach ($mother_name as $name) {
        $pdf->SetXY($x, 235.4);
        $pdf->Write(1, $name);
        $x += 7.7;
      }

      // DOB
      $dob = str_split($student['DOB']);
      // Day
      $pdf->SetXY(54.5, 245);
      $pdf->Write(1, $dob[8]);
      $pdf->SetXY(61.5, 245);
      $pdf->Write(1, $dob[9]);
      // Month
      $pdf->SetXY(73, 245);
      $pdf->Write(1, $dob[5]);
      $pdf->SetXY(80, 245);
      $pdf->Write(1, $dob[6]);
      // Year
      $pdf->SetXY(88, 245);
      $pdf->Write(1, $dob[0]);
      $pdf->SetXY(96, 245);
      $pdf->Write(1, $dob[1]);
      $pdf->SetXY(103, 245);
      $pdf->Write(1, $dob[2]);
      $pdf->SetXY(110, 245);
      $pdf->Write(1, $dob[3]);

      // Gender
      $gender = $student['Gender'] == "Male" ? "Male" : "Female";
      if ($gender == "Male") {
        $pdf->Image($check, 151, 244, 2, 3);
      } else if ($gender == "Female") {
        $pdf->Image($check, 172, 243, 2, 3);
      } else {
        $pdf->Image($check, 192, 243, 2, 3);
      }

      //Center
      $pdf->SetXY(15, 258);
      if (!empty($center) && !empty($center['CName'])) {
        $pdf->Write(1, strtoupper($center['CName']));
      } else {
        $pdf->Write(1, strtoupper($student['Center']));
      }
      // Seal
      //if ($student['Center_Seal']) {
      // $seal = '../../' . $student['Center_Seal'];
      //$pdf->Image($seal, 150, 253, 20, 20);
      //} else {
      //$seal = '../../' . $center['CPhoto'];
      //$pdf->Image($seal, 150, 253, 20, 20);
      //}

      // Page 2
      $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);
      $pdf->addPage();
      $pdf->useImportedPage($pageId, 0, 0, 210);

      // Category
      if ($student['Category'] == 'General') {
        $pdf->Image($check, 55, 25, 2, 3);
      } else if ($student['Category'] == 'ST') {
        $pdf->Image($check, 100, 25, 2, 3);
      } else if ($student['Category'] == 'SC') {
        $pdf->Image($check, 78, 25, 2, 3);
      } else if ($student['Category'] == 'OBC') {
        $pdf->Image($check, 125, 25, 2, 3);
      }

      if ($student['Employement_Status'] == 'Govt Employed') {
        $pdf->Image($check, 78, 37, 2, 3);
      } else if ($student['Employement_Status'] == 'Employed') {
        $pdf->Image($check, 125, 37, 2, 3);
      } else if ($student['Employement_Status'] == 'Unemployed') {
        $pdf->Image($check, 161, 37, 2, 3);
      } else if ($student['Employement_Status'] == 'Others') {
        $pdf->Image($check, 190, 37, 2, 3);
      }

      if ($student['Nationality'] == 'Indian') {
        $pdf->Image($check, 42, 97, 2, 3);
      } else if ($student['Nationality'] == 'NRI') {
        $pdf->Image($check, 60, 97, 2, 3);
      }
      //print_r($student['Employement_Status']);die;
      // // Adhar
      // $pdf->SetXY(154, 110);
      // $pdf->Write(1, $student['Aadhar_Number']);

      // // Email
      // $pdf->SetXY(37, 122.5);
      // $pdf->Write(1, $student['Email']);

      // Permanent Address
      $pdf->SetXY(45, 53);
      $pdf->Write(1, substr($address['present_address'], 0, 63));
      $pdf->SetXY(11, 63);
      $pdf->Write(1, substr($address['present_address'], 64));
      // City
      $pdf->SetFont('Arial', '', 10);
      $pdf->SetXY(11, 72);
      $pdf->Write(1, strtoupper(substr($address['present_city'], 0, 15) . ', ' . $address['present_district'] . ', ' . $address['present_state'] . ', ' . $student['Nationality']));

      // City
      //$pdf->SetFont('Arial', '', 10);
      //$pdf->SetXY(110, 62.5);
      //$pdf->Write(1, substr($address['present_state'], 0, 18));

      // Country
      //$pdf->SetXY(11, 72);
      //$pdf->Write(1, $student['Nationality']);

      // Pincode
      $permanent_pincode = str_split($address['present_pincode']);
      $x = 151;
      for ($i = 0; $i < count($permanent_pincode); $i++) {
        $pdf->SetXY($x, 73.4);
        $pdf->Write(1, $permanent_pincode[$i]);
        $x += 8.5;
      }

      // Adhaar
      $pdf->SetXY(85, 86);
      $pdf->Write(1, 'Aadhar Card : ' . $student['Aadhar_Number']);

      // Mobile
      $contact = str_split($student['Contact']);
      $x = 132;
      for ($i = 0; $i < count($contact); $i++) {
        $pdf->SetXY($x, 99);
        $pdf->Write(1, $contact[$i]);
        $x += 7;
      }

      // Signature
      if (filetype($signature) === 'file' && file_exists($signature)) {
        try {
          $filename = $id . "_Student_Signature" . $file_extensions[0];
          $image = $filename;
          $pdf->Image($image, 18, 220, 30.2, 15);
          $student_signature = $image;
        } catch (Exception $e) {
          try {
            $filename = $id . "_Student_Signature" . $file_extensions[1];
            $image = $filename;
            $pdf->Image($image, 18, 220, 30.2, 15);
            $student_signature = $image;
          } catch (Exception $e) {
            try {
              $filename = $id . "_Signature" . $file_extensions[2];
              $image = $filename;
              $pdf->Image($image, 18, 220, 30.2, 15);
              $student_signature = $image;
            } catch (Exception $e) {
              echo 'Message: ' . $e->getMessage();
            }
          }
        }
      }
      //Place
      $pdf->SetFont('Arial', '', 10);
      $pdf->SetXY(160, 232);
      if (!empty($center) && !empty($center['CCity'])) {
        $pdf->Write(1, strtoupper($center['CCity']));
      } else {
        $pdf->Write(1, strtoupper($student['Center_City']));
      }

      // $pdf->SetFont('Arial', '', 10);
      // $pdf->SetXY(52, 164.3);
      // $pdf->Write(1, $student['Contact']);

      // Academics
      $academis = array(
        'High School', 'Intermediate', 'Under Graduation',
        'Post Graduation', 'Other'
      );
      $y = '165';
      foreach ($academis as $academic) {
        $x = '11';

        // Details
        $type = $academic == 'Under Graduation' ? 'UG' : ($academic ==
          'Post Graduation' ? 'PG' : $academic);
        $data = $conn->query("SELECT * FROM Student_Academics WHERE
       Student_ID = $id AND Type = '$type'");
        if ($data->num_rows > 0) {

          $data = mysqli_fetch_assoc($data);

          // $pdf->SetXY($x, $y);
          // $pdf->Write(1, $academic);
          $x += 3;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Total_Marks']) ? $data['Total_Marks'] : '');

          $x += 52;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Subject']) ? $data['Subject'] : '');

          $x += 50;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Year']) ? $data['Year'] : '');

          $x += 20;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Board/Institute']) ?
            substr($data['Board/Institute'], 0, 28) : '');

          $x += 30;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Type']) ? strtoupper(substr($data['Type'], 0, 28)) : '');

          // Roll No
          $x += 0;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Marks_Obtained']) ? $data['Marks_Obtained'] : '');
        }
        $y += 10;
      }

      // Page 3
      //$pageId = $pdf->importPage(3, PdfReader\PageBoundaries::MEDIA_BOX);
      //$pdf->addPage();
      //$pdf->useImportedPage($pageId, 0, 0, 210);

      // Page 4
      //$pageId = $pdf->importPage(4, PdfReader\PageBoundaries::MEDIA_BOX);
      //$pdf->addPage();
      //$pdf->useImportedPage($pageId, 0, 0, 210);

      // // Page 5
      // $pageId = $pdf->importPage(5, PdfReader\PageBoundaries::MEDIA_BOX);
      // $pdf->addPage();
      // $pdf->useImportedPage($pageId, 0, 0, 210);

      //   // // Date
      //   // $pdf->SetXY(100.5, 190.5);
      //   // $pdf->Write(1, date('d-m-Y'));


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
      
      $documents = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type NOT IN ('Photo', 'Student Signature', 'Parent Signature')");
    while ($document = $documents->fetch_assoc()) {
        $files = explode("|", $document['Location']);
        foreach ($files as $file) {
            $pdf->AddPage();
            $pdf->SetMargins(10, 10, 10);
            // Width & Height
            list($file_width, $file_height) = getimagesize("../.." . $file);

            $encoded_file = base64_encode(file_get_contents("../.." . $file));

            // Recreate
            $i = 0;
            $end = 3;
            $new_file = $id . uniqid();
            while ($i < $end) {
                $decoded_file = base64_decode($encoded_file);
                $file_with_extension[] = $new_file . $file_extensions[$i];
                file_put_contents($new_file . $file_extensions[$i], $decoded_file);
                $i++;
            }

            $width = ($file_width / 2.02) > 190 ? 190 : $file_width / 2.02;
            $height = ($file_height / 2.02) > 270 ? 270 : $file_height / 2.02;

            try {
                $filename = $new_file . $file_extensions[0];
                $pdf->Image($filename, 10, 10, $width, $height);
            } catch (Exception $e) {
                try {
                    $filename = $new_file . $file_extensions[1];
                    $pdf->Image($filename, 10, 10, $width, $height);
                } catch (Exception $e) {
                    try {
                        $filename = $new_file . $file_extensions[2];
                        $pdf->Image($filename, 10, 10, $width, $height);
                    } catch (Exception $e) {
                    }
                }
            }

            foreach ($file_with_extension as $file_ext) {
                if (file_exists($file_ext)) {
                    unlink($file_ext);
                }
            }
            $file_with_extension = array();
        }
    }
    }

    $pdf->Output('I', 'Jamia Application Form.pdf');
  }
} else {
  header('Location: /');
}
