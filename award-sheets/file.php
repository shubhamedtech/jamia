<?php
//ini_set('display_errors',1);
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

function intToWords($number) {
  	if(in_array($number, ['Absent', 'AB'])){
    	return $number;
    }
  	
    $units = array('', 'one', 'two', 'three', 'four',
                   'five', 'six', 'seven', 'eight', 'nine');

    $tens = array('', 'ten', 'twenty', 'thirty', 'forty',
                  'fifty', 'sixty', 'seventy', 'eighty', 
                  'ninety');

    $special = array('eleven', 'twelve', 'thirteen',
                     'fourteen', 'fifteen', 'sixteen',
                     'seventeen', 'eighteen', 'nineteen');

    $words = '';
    if ($number < 10) {
        $words .= $units[$number];
    } elseif ($number < 20) {
        $words .= $special[$number - 11];
    } else {
        $words .= $tens[(int)($number / 10)] . ' '
                  . $units[$number % 10];
    }

    return ucwords($words);
}

if(isset($_POST['path'], $_POST['fileName'], $_POST['center'], $_POST['course'], $_POST['subject'])){
  require '../includes/db-config.php';
  require_once('../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../extras/vendor/setasign/fpdi/src/autoload.php');

  $center = $_POST['center'];
  $course = $_POST['course'];
  $subject = $_POST['subject'];
  $path = $_POST['path'];
  $fileName = $_POST['fileName'];
  
  $pdf = new Fpdi();
  $pdf->SetTitle('Jamia | Award Sheet');
  
  $pageCount = $pdf->setSourceFile('sheet.pdf');
  
  $allStudents = $conn->query("SELECT ID, Roll_No, Obtained_Marks_Theory FROM Award_Sheet_Records WHERE Center_Code = '$center' AND Course = '$course' AND Subject = '$subject' AND Obtained_Marks_Theory != 'Absent' ");
  $allStudentCount = $allStudents->num_rows;
  
  $records = array();
  while($allStudent = $allStudents->fetch_assoc()){
  	$records[] = $allStudent;
  }
  
  $noOfPages = ceil($allStudentCount/23);
  
  $startFrom = 0;
  for($i = 1; $i<= $noOfPages; $i++){
    $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 0, 0, 210);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(162, 11);
    $pdf->Write(1, $center);
    
    $pdf->SetXY(90, 55.5);
    $pdf->Write(1, $course);

    $pdf->SetXY(47, 66);
    $pdf->Write(1, $subject);
  
    $y = 92;
    for($j = $startFrom; $j<= ($startFrom+22); $j++){
        if(!array_key_exists($j, $records)){
            continue;
        }
        
    	//Roll No
      	$pdf->SetXY(28, $y);
    	$pdf->Write(1, $records[$j]['Roll_No']);
      
      	//In Words
        $pdf->SetXY(65, $y);
    	$pdf->Write(1, intToWords($records[$j]['Obtained_Marks_Theory']));
      
      	// Numbers
        $pdf->SetXY(153, $y);
    	$pdf->Write(1, $records[$j]['Obtained_Marks_Theory']);
      	
      $y += 7.5;
    }
    $startFrom += 23;
  }	

  $pdf->Output('F', $path.'/'.$fileName);
}