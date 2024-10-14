<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['student_id'])) {
  require '../../includes/db-config.php';
  session_start();

  if ($_SESSION['university_id'] == 48) {
    $id = mysqli_real_escape_string($conn, $_GET['student_id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

    $student = $conn->query("SELECT Students.*, Users.Name as Center, Users.Code as Center_Code, Users.ID as Center_ID, Users.Photo as Center_Seal, Users.City as Center_City, UPPER(Courses.Name) as Course, Sub_Courses.Name as Sub_Course, Admission_Sessions.Name as `Session`, Admission_Types.Name as Type FROM Students LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.ID = $id");
    $student = mysqli_fetch_assoc($student);
    $address = json_decode($student['Address'], true);
    if (strlen($student['Center_Code']) > 4) {
      $subcenter_code = $student['Center_ID'];
      $center = $conn->query("SELECT Users.Name as CName, Users.Code as CCode, Users.Photo as CPhoto, Users.City as CCity FROM Users JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE Center_SubCenter.Sub_Center = $subcenter_code");
      $center = mysqli_fetch_assoc($center);
      $student['Center_Seal'] = $center['CPhoto'];
      $student['Center_Code'] = $center['CCode'];
    }

    require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');
    
    class PDF_Clipping extends Fpdi
    {
        function ClippingText($x, $y, $txt, $outline=false)
        {
            $op=$outline ? 5 : 7;
            $this->_out(sprintf('q BT %.2F %.2F Td %d Tr (%s) Tj ET',
                $x*$this->k,
                ($this->h-$y)*$this->k,
                $op,
                $this->_escape($txt)));
        }
    
        function ClippingRect($x, $y, $w, $h, $outline=false)
        {
            $op=$outline ? 'S' : 'n';
            $this->_out(sprintf('q %.2F %.2F %.2F %.2F re W %s',
                $x*$this->k,
                ($this->h-$y)*$this->k,
                $w*$this->k,-$h*$this->k,
                $op));
        }
    
        function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
        {
            $h = $this->h;
            $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
                $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
        }
    
        function ClippingRoundedRect($x, $y, $w, $h, $r, $outline=false)
        {
            $k = $this->k;
            $hp = $this->h;
            $op=$outline ? 'S' : 'n';
            $MyArc = 4/3 * (sqrt(2) - 1);
    
            $this->_out(sprintf('q %.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
            $xc = $x+$w-$r ;
            $yc = $y+$r;
            $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
    
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
            $xc = $x+$w-$r ;
            $yc = $y+$h-$r;
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
            $xc = $x+$r ;
            $yc = $y+$h-$r;
            $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
            $xc = $x+$r ;
            $yc = $y+$r;
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
            $this->_out(' W '.$op);
        }
    
        function ClippingEllipse($x, $y, $rx, $ry, $outline=false)
        {
            $op=$outline ? 'S' : 'n';
            $lx=4/3*(M_SQRT2-1)*$rx;
            $ly=4/3*(M_SQRT2-1)*$ry;
            $k=$this->k;
            $h=$this->h;
            $this->_out(sprintf('q %.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
                ($x+$rx)*$k,($h-$y)*$k,
                ($x+$rx)*$k,($h-($y-$ly))*$k,
                ($x+$lx)*$k,($h-($y-$ry))*$k,
                $x*$k,($h-($y-$ry))*$k));
            $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
                ($x-$lx)*$k,($h-($y-$ry))*$k,
                ($x-$rx)*$k,($h-($y-$ly))*$k,
                ($x-$rx)*$k,($h-$y)*$k));
            $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
                ($x-$rx)*$k,($h-($y+$ly))*$k,
                ($x-$lx)*$k,($h-($y+$ry))*$k,
                $x*$k,($h-($y+$ry))*$k));
            $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c W %s',
                ($x+$lx)*$k,($h-($y+$ry))*$k,
                ($x+$rx)*$k,($h-($y+$ly))*$k,
                ($x+$rx)*$k,($h-$y)*$k,
                $op));
        }
    
        function ClippingCircle($x, $y, $r, $outline=false)
        {
            $this->ClippingEllipse($x, $y, $r, $r, $outline);
        }
    
        function ClippingPolygon($points, $outline=false)
        {
            $op=$outline ? 'S' : 'n';
            $h = $this->h;
            $k = $this->k;
            $points_string = '';
            for($i=0; $i<count($points); $i+=2){
                $points_string .= sprintf('%.2F %.2F', $points[$i]*$k, ($h-$points[$i+1])*$k);
                if($i==0)
                    $points_string .= ' m ';
                else
                    $points_string .= ' l ';
            }
            $this->_out('q '.$points_string . 'h W '.$op);
        }
    
        function UnsetClipping()
        {
            $this->_out('Q');
        }
    
        function ClippedCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
        {
            if($border || $fill || $this->y+$h>$this->PageBreakTrigger)
            {
                $this->Cell($w,$h,'',$border,0,'',$fill);
                $this->x-=$w;
            }
            $this->ClippingRect($this->x,$this->y,$w,$h);
            $this->Cell($w,$h,$txt,'',$ln,$align,false,$link);
            $this->UnsetClipping();
        }
    }

    
    // Extensions
    $file_extensions = array('.png', '.jpg', '.jpeg');

    //this folder will have there images.
    $path = "photos/";

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
        file_put_contents($path.$filename1, $data1); //we save our new images to the path above
        $i++;
      }
    } else {
      $photo = "";
    }
    
    $centerPhoto = "";
    if(!empty($student['Center_Seal'])){
        $centerPhoto = base64_encode(file_get_contents('../..'.$student['Center_Seal']));
        $centerPhotoData = base64_decode($centerPhoto);
        foreach($file_extensions as $ext){
            $sign = $id . "_Center_Photo" . $ext;
            file_put_contents($path.$sign, $centerPhotoData);
        }
    }
    
    $pdf = new PDF_Clipping();
    $pdf->SetTitle('Jamia I-Card');
    $pageCount = $pdf->setSourceFile('icard.pdf');
    

    // Page 1
    $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 0, 0, 210);
    
    $pdf->SetFont('Arial', 'B', 8);
    $studentName = implode(" ", array_filter(array($student['First_Name'],$student['Middle_Name'],$student['Last_Name'])));
    $pdf->SetXY(47.5, 63);
    $pdf->Cell(45,3,$studentName,0,0,'C');
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->SetXY(47.5, 66);
    $pdf->Cell(45,3,$student['Course'],0,0,'C');
    
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetXY(47.5, 79);
    $pdf->Write(1, 'Enrollment No.');
    $pdf->SetXY(47.5, 82);
    $pdf->Write(1, $student['Enrollment_No']);
    
    $pdf->SetFont('Arial', '', 5);
    $pdf->SetXY(102, 42);
    $addressData = explode(",", $address['present_address']);
    foreach($addressData as $ad){
        $newAddress[] = trim($ad);
    }
    $newAddress[] = $address['present_city'];
    $newAddress[] = $address['present_district'];
    $newAddress[] = $address['present_state'];
    $newAddress[] = $address['present_pincode']; 
    
    
    $pdf->Multicell(46.5,3,'Student Address : '.implode(", ", $newAddress),0, 'L', false);

    $pdf->SetFont('Arial', 'B', 5);
    $yAxis = $pdf->GetY();
    $pdf->SetXY(102, $yAxis+2);
    $pdf->Write(1, 'Contact No. : '.$student['Contact']);
    
    $yAxis = $pdf->GetY();
    $pdf->SetXY(102, $yAxis+3);
    $pdf->Write(1, 'Center Code : '.$student['Center_Code']);
    
    $pdf->SetFont('Arial', '', 5);
    $pdf->SetXY(105.5, 66);
    $pdf->Write(1, 'JAMIA URDU ALIGARH, Dodhpur, Civil Lines');
    $pdf->SetXY(111.5, 68.5);
    $pdf->Write(1, 'Aligarh, Uttar Pradesh - 202002');
    
    try {
     $pdf->Image($path.$id . "_Center_Photo" . $file_extensions[0],77,78,15,4);
    } catch(Exception $e){
        try{
            $pdf->Image($path.$id . "_Center_Photo" . $file_extensions[1],58.3,41.2,23,24);
        } catch(Exception $e){
            try{
                $pdf->Image($path.$id . "_Center_Photo" . $file_extensions[2],58.3,41.2,23,24);
            } catch(Exception $e){
                //echo 'Message: ' . $e->getMessage();
            }
        } 
        
    }
    
    try {
     $pdf->ClippingCircle(70,51.5,9.4,true);
     $pdf->Image($path.$id . "_Photo" . $file_extensions[0],58.3,41.2,23,24);
     $pdf->UnsetClipping();    
    } catch(Exception $e){
        try{
            $pdf->ClippingCircle(70,51.5,9.4,true);
            $pdf->Image($path.$id . "_Photo" . $file_extensions[1],58.3,41.2,23,24);
            $pdf->UnsetClipping(); 
        } catch(Exception $e){
            try{
                $pdf->ClippingCircle(70,51.5,9.4,true);
                $pdf->Image($path.$id . "_Photo" . $file_extensions[2],58.3,41.2,23,24);
                $pdf->UnsetClipping(); 
            } catch(Exception $e){
               // echo 'Message: ' . $e->getMessage();
            }
        } 
        
    }

    $i = 0;
    $end = 3;
    while ($i < $end) {
      // Delete Photos
      if (!empty($student_photo)) {
        $filename = $id . "_Photo" . $file_extensions[$i];
        $filename2 = $id . "_Center_Photo" . $file_extensions[$i];
        //$file_extensions loops through the file extensions
        unlink($path.$filename);
        unlink($path.$filename2);
      }
      $i++;
    }

    $pdf->Output('I', $student['Unique_ID'].' I-Card.pdf');
  }
} else {
  header('Location: /');
}
