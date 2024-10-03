<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['id'])) {
  session_start();
  require '../../includes/db-config.php';
  require '../../includes/helpers.php';

  $id = intval($_GET['id']);
  $student = $conn->query("SELECT Students.ID AS Stu_ID,Universities.Short_Name as University, Sub_Courses.Name as Sub_Cour_name, Students.Duration as no_semester, Courses.Short_Name as Course, DATE_FORMAT(Students.DOB, '%d-%m-%Y') as DOB, RIGHT(CONCAT('000000', Payments.ID), 6) as ID, RIGHT(CONCAT('000000', Students.ID), 6) as Student_Table_ID, Students.Unique_ID as Student_ID, Payments.Bank, Payments.Added_By,Payments.Amount,Payments.Payment_Mode,JSON_UNQUOTE(JSON_EXTRACT(Student_Ledgers.Fee,'$.Paid'))AS Invoiced_Amount,Payments.Transaction_ID,Payments.Gateway_ID,Payments.Type,Student_Ledgers.Duration,CONCAT(IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), '  .  ', TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) as Unique_ID,DATE_FORMAT(Student_Ledgers.Created_At, '%d-%m-%Y') as Created_At,Payments.Transaction_Date FROM Student_Ledgers LEFT JOIN Payments ON Student_Ledgers.Transaction_ID=Payments.Transaction_ID LEFT JOIN Students ON Student_Ledgers.Student_ID=Students.ID LEFT JOIN Sub_Courses ON
  Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Universities ON Students.University_ID = Universities.ID WHERE Student_Ledgers.ID= $id");
  $details = $student->fetch_assoc();

  $student_id = !empty($details['Student_ID']) ? $details['Student_ID'] : $details['Student_Table_ID'];
  // KP 17APRIL
  $studentsQuery = $conn->query("SELECT CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID FROM Invoices LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE `User_ID` = " . $details['Added_By'] . " AND Invoice_No = '" . $details['Transaction_ID'] . "'  AND Invoices.University_ID = " . $_SESSION['university_id'] . " ");
  $studentscount = $studentsQuery->num_rows;
  if ($studentscount != 0) {
    $details['Amounts'] = $details['Amount'] / $studentscount;
  } else {
    $details['Amounts'] = $details['Amount'];
  }



  $check_invoice_query = $conn->query("SELECT User_ID as Invoice_User_ID FROM Invoices Where Student_ID = '" . $details['Stu_ID'] . "' AND Invoice_No = '" . $details['Transaction_ID'] . "'  AND University_ID = " . $_SESSION['university_id'] . " ");
  $courseInvoiceArr = $check_invoice_query->fetch_assoc();

  $credited_val = abs($details['Amounts']);
  
  $check_role_query = $conn->query("SELECT ID,CanCreateSubCenter,Role FROM Users Where ID = '" . $courseInvoiceArr['Invoice_User_ID'] . "' AND Role='Center' AND CanCreateSubCenter=1");
  if ($check_role_query->num_rows > 0) {
    $course_name_query = $conn->query("SELECT Courses.Name as courseName, Students.Created_At,Students.Duration FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.ID= " . $details['Stu_ID'] . " AND Students.University_ID = " . $_SESSION['university_id'] . " ");
    $courseArr = $course_name_query->fetch_assoc();
    // echo "<pre>"; print_r($courseArr); die;
      $credited = 2000;
      if (date("Y-m-d", strtotime($courseArr['Created_At'])) >= "2024-03-30") {
        $discount_amount =  strpos($courseArr['courseName'], 'adeeb-e-mahir') !== false ? 1800 : 1600;
        $credited = $credited_val +  $discount_amount;
      }
      $details['Amounts'] = $credited;
  }

// KP 17APRIL

  $ledgerSummary = getLedgerSummary($conn, (int)$details['Student_Table_ID']);
  $ledgerSummary = !empty($ledgerSummary) ? json_decode($ledgerSummary, true) : array('totalFee' => 0, 'totalRemitted' => 0, 'totalBalance' => 0);

  $details['Course_Fee'] = $ledgerSummary['totalFee'] ;
  // Accountant
  $accountant = $conn->query("SELECT UPPER(`Name`) as Name FROM Users WHERE `Role` = 'Accountant'");
  if ($accountant->num_rows > 0) {
    $accountant = $accountant->fetch_assoc();
    $accountant = $accountant['Name'];
  } else {
    $accountant = 'Accountant';
  }
  $balance = $ledgerSummary['totalFee'] - $details['Amounts'];
  // echo "<pre>"; print_r($details);die;

  require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

  $pdf = new Fpdi();

  $pdf->SetTitle('Fee Receipt');
  $pageCount = $pdf->setSourceFile('receipt.pdf');
  $pdf->SetFont('Times', 'B', 12);

  // Page 1
  $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  $pdf->SetXY(28, 53.5);
  $pdf->Write(1, $details['Unique_ID']);

  $pdf->SetXY(47, 59.5);
  $pdf->Write(1, $details['DOB']);

  $pdf->SetXY(33.5, 65.8);
  $pdf->Write(1, $details['Course'].' '.$details['Sub_Cour_name'].' ( '.$details['no_semester'].' Semester )');

  $pdf->SetXY(148, 53.5);
  $pdf->Write(1, $details['ID']);

  $pdf->SetXY(135, 59.5);
  $pdf->Write(1, $details['Created_At']);

  $pdf->SetXY(148, 65.8);
  $pdf->Write(1, $details['University']);

  $pdf->SetXY(18, 83.5);
  $pdf->Write(1, 'COURSE FEE');

  $pdf->SetXY(172, 83.5);
  $pdf->Write(1, $details['Course_Fee']);

  $pdf->SetXY(70, 106);
  $pdf->Write(1, 'TOTAL AMOUNT');

  $pdf->SetXY(172, 106);
  $pdf->Write(1, $details['Course_Fee']);

  $pdf->SetFont('Times', 'B', 9.5);

  $pdf->SetXY(16, 111.6);
  $pdf->Write(1, 'Total Fee : ' . $ledgerSummary['totalFee']);

  $pdf->SetXY(50, 111.6);
  $pdf->Write(1, 'Total Remitted Fee : ' . $details['Amounts']);

  $pdf->SetXY(100, 111.6);
  $pdf->Write(1, 'Balance Fee : ' . $balance);

  $pdf->SetFont('Times', '', 12);

  $amountInWords = ucwords(strtolower(numberTowords($details['Amounts'])));

  $pdf->SetXY(16, 116.6);
  $pdf->Write(1, $amountInWords);

  $pdf->SetXY(16, 124);
  $pdf->Write(1, 'By ' . $details['Payment_Mode']);

  $pdf->SetXY(110, 124);
  $pdf->Write(1, 'Txn. ID ' . $details['Gateway_ID']);

  $pdf->SetXY(16, 132);
  $pdf->Write(1, 'Pay At ' . strtoupper(strtolower($details['Bank'])));

  $pdf->SetXY(110, 132);
  $pdf->Write(1, 'Txn. No ' . strtoupper(strtolower($details['Transaction_ID'])));

  $pdf->SetFont('Times', 'B', 11);

  $pdf->SetXY(159, 144);
  $pdf->Write(1, $accountant);

  $pdf->SetFont('Times', 'B', 12);

  $pdf->SetXY(28, 171.4);
  $pdf->Write(1, $details['Unique_ID']);

  $pdf->SetXY(47, 177.4);
  $pdf->Write(1, $details['DOB']);

  $pdf->SetXY(33.5, 183.8);
  $pdf->Write(1, $details['Course']);

  $pdf->SetXY(148, 171.4);
  $pdf->Write(1, $details['ID']);

  $pdf->SetXY(135, 177.4);
  $pdf->Write(1, $details['Created_At']);

  $pdf->SetXY(148, 183.8);
  $pdf->Write(1, $details['University']);

  $pdf->SetXY(18, 201.4);
  $pdf->Write(1, 'COURSE FEE');

  $pdf->SetXY(172, 201.4);
  $pdf->Write(1, $details['Amounts']);

  $pdf->SetXY(70, 224.4);
  $pdf->Write(1, 'TOTAL AMOUNT');

  $pdf->SetXY(172, 224.4);
  $pdf->Write(1, $details['Amounts']);

  $pdf->SetFont('Times', '', 12);

  $pdf->SetXY(16, 229.5);
  $pdf->Write(1, $amountInWords);

  $pdf->SetXY(16, 237);
  $pdf->Write(1, 'By ' . $details['Payment_Mode']);

  $pdf->SetXY(110, 237);
  $pdf->Write(1, 'Txn. ID ' . $details['Gateway_ID']);

  $pdf->SetXY(16, 245);
  $pdf->Write(1, 'Pay At ' . strtoupper(strtolower($details['Bank'])));

  $pdf->SetXY(110, 245);
  $pdf->Write(1, 'Txn. No ' . strtoupper(strtolower($details['Transaction_ID'])));

  $pdf->SetFont('Times', 'B', 11);

  $pdf->SetXY(159, 261);
  $pdf->Write(1, $accountant);

  $pdf->Output('D', $student_id . '_' . $details['Transaction_ID'] . '_Fee Receipt.pdf');
}
