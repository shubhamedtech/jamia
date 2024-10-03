<?php
ini_set('display_errors', 1);

// String Encryption
function stringToSecret(string $string = NULL)
{
  if (!$string) {
    return NULL;
  }
  $length = strlen($string);
  $visibleCount = (int) round($length / 6);
  $hiddenCount = $length - ($visibleCount * 2);
  return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
}

function uuidGenerator($table, $conn)
{
  $all_key = array();
  $get_key = $conn->query("SELECT Api_Key FROM $table");
  while ($gk = $get_key->fetch_assoc()) {
    $all_key[] = $gk['Api_Key'];
  }

  $data = $data ?? random_bytes(16);
  assert(strlen($data) == 16);
  // Set version to 0100
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  // Set bits 6-7 to 10
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  // Output the 36 character UUID.
  $generated_key = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  if (in_array($generated_key, $all_key)) {
    uuidGenerator($table, $conn);
  } else {
    return $generated_key;
  }
}

function generateStudentLedger($conn, $student_id)
{
  $check = $conn->query("SELECT ID FROM Student_Ledgers WHERE Student_ID = $student_id");
  if ($check->num_rows > 0) {
    $conn->query("DELETE FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  }

  $student_fee = array();
  $student_fee_without_sharing = array();
  $added_role = '';
  $sub_center = 0;
  $student = $conn->query("SELECT Admission_Type_ID, Students.University_ID, Students.Duration, Students.Course_ID, Sub_Course_ID, Courses.Name as courseName, Sub_Courses.Min_Duration, Added_For, Students.Created_At, Universities.Is_Vocational, Students.Is_Toc FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Universities ON Students.University_ID = Universities.ID WHERE Students.ID = $student_id");
  $student = mysqli_fetch_assoc($student);
  $is_toc = $student['Is_Toc'];
  $reporting = $conn->query("SELECT Center_SubCenter.Sub_Center, Center_SubCenter.Center, Users.Role, Users.Vertical FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Sub_Center = Users.ID WHERE Users.Role = 'Sub-Center' AND Sub_Center = " . $student['Added_For'] . "");
  if ($reporting->num_rows > 0) {
    $reporting = mysqli_fetch_assoc($reporting);
    $student['Added_For'] = $reporting['Center'];
    $sub_center = $reporting['Sub_Center'];
    $added_role = $reporting['Role'];
  }
  
    $vertical = $conn->query("SELECT Vertical FROM Users WHERE ID = ".$student['Added_For']);
    $vertical = $vertical->fetch_assoc();
    $vertical = $vertical['Vertical'];

  $structures = array();
  $fee_structures = $conn->query("SELECT ID, Fee_Applicable_ID FROM Fee_Structures WHERE University_ID = " . $student['University_ID'] . " AND Status = 1 ORDER BY Fee_Applicable_ID");
  while ($fee_structure = $fee_structures->fetch_assoc()) {
    $structures[$fee_structure['ID']] = $fee_structure['Fee_Applicable_ID'];
  }

/*   if ($added_role) {
    $fee = $conn->query("SELECT Fee FROM Sub_Center_Sub_Courses WHERE User_ID = " . $sub_center . " AND Course_ID = " . $student['Course_ID'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND University_ID = " . $student['University_ID'] . "");
    $fee = $fee->fetch_assoc();
    $center_course_fee = $fee['Fee'];
  } else {
    $fee = $conn->query("SELECT Fee FROM Center_Sub_Courses WHERE User_ID = " . $student['Added_For'] . " AND Course_ID = " . $student['Course_ID'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND University_ID = " . $student['University_ID'] . " AND Duration = " . $student['Duration'] . " ");
    $fee = $fee->fetch_assoc();
    $center_course_fee = $fee['Fee'];
  } */



  $center_course_fee = 0;
  $exam_fee = 1000;
  $registration_fee = 1000;
  if(strpos($student['courseName'], 'adeeb-e-mahir')!==false && date("Y-m-d", strtotime($student['Created_At']))>= "2024-03-30"){
    $exam_fee = 1500;
    $registration_fee = 1500;
  }
  if($vertical==3){
    $exam_fee = 2000;
    $registration_fee = 2000;
  }
  if($vertical==4){
    $exam_fee = 1500;
    $registration_fee = 1500;
  }
  
  $total_fee_cols = $vertical==3 ? "Sb.International_Subject_Fee + Sb.Practical_Fee" : ($vertical==4 ? "Sb.International_Subject_Fee_Exception + Sb.Practical_Fee" : "Sb.Subject_Fee + Sb.Practical_Fee");
  if($is_toc){
    $total_fee_cols .= "+ Sb.Toc_Fee";
  }
  
  $sql_query = "SELECT SUM($total_fee_cols) AS Total_Fee FROM Student_Subjects St INNER JOIN Subjects Sb ON St.Subject_Id = Sb.ID WHERE St.Student_Id = $student_id";
  $result = $conn->query($sql_query);
  while ($res = $result->fetch_assoc()) {
    $center_course_fee = $res['Total_Fee'];
  }
  
  $center_course_fee = $center_course_fee + $exam_fee + $registration_fee;
  $date = date('Y-m-d', strtotime($student['Created_At']));
  $add = $conn->query("INSERT INTO Student_Ledgers (Date, Student_ID, Duration, University_ID, Type, Fee, Fee_Without_Sharing, Status) VALUES ('$date', $student_id, 1, " . $student['University_ID'] . ", 1, $center_course_fee, $center_course_fee, 1)");
}

function activityLogs($conn, $message, $user_id)
{
}

function generateLeadHistory($conn, $lead_id, $user_id, $old, $new)
{
  $result = array_diff($old, $new);
  if (!empty($result)) {
    $update = $conn->query("INSERT INTO Lead_Histories (Lead_ID, `User_ID`, Data, Created_By) VALUES ($lead_id, $user_id, '" . json_encode($result) . "', " . $_SESSION['ID'] . ")");
  }
}


function generateStudentID($conn, $suffix, $length, $university_id)
{
  $student_ids = array();
  $ids = $conn->query("SELECT Unique_ID FROM Students WHERE University_ID = " . $university_id . " AND Unique_ID IS NOT NULL");
  while ($id = $ids->fetch_assoc()) {
    $student_ids[] = $id['Unique_ID'];
  }

  $ids = $conn->query("SELECT Unique_ID FROM Lead_Status WHERE University_ID = " . $university_id . " AND Unique_ID IS NOT NULL");
  while ($id = $ids->fetch_assoc()) {
    $student_ids[] = $id['Unique_ID'];
  }

  $student_ids = array_filter($student_ids);

  $result = '';
  for ($i = 0; $i < $length; $i++) {
    $result .= mt_rand(0, 9);
  }

  $new_id = $suffix . $result;
  if (in_array($new_id, $student_ids)) {
    return generateStudentID($conn, $suffix, $length, $university_id);
  } else {
    return $new_id;
  }
}

function clean($string)
{
  $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  return preg_replace('/[^A-Za-z0-9\-.|,]/', '', $string); // Removes special chars.
}

function balanceAmount($conn, $student_id, $duration)
{
  $balance = 0;
  $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = $student_id AND Status = 1 AND Duration <= " . $duration);
  while ($ledger = $ledgers->fetch_assoc()) {
    // $fees = json_decode($ledger['Fee'], true);
    // foreach ($fees as $key => $value) {
    //   $debit = $ledger['Type'] == 1 ? $value : 0;
    //   $credit = $ledger['Type'] == 2 ? $value : 0;
      // $balance = ($balance + $credit) - $debit;
    // }
    $balance = $ledger['Fee'];
  }

  return (int)$balance ;
}

function numberTowords($number)
{
  $decimal = round($number - ($no = floor($number)), 2) * 100;
  $hundred = null;
  $digits_length = strlen($no);
  $i = 0;
  $str = array();
  $words = array(
    0 => '', 1 => 'one', 2 => 'two',
    3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
    7 => 'seven', 8 => 'eight', 9 => 'nine',
    10 => 'ten', 11 => 'eleven', 12 => 'twelve',
    13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
    16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
    19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
    40 => 'forty', 50 => 'fifty', 60 => 'sixty',
    70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
  );
  $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
  while ($i < $digits_length) {
    $divider = ($i == 2) ? 10 : 100;
    $number = floor($no % $divider);
    $no = floor($no / $divider);
    $i += $divider == 10 ? 1 : 2;
    if ($number) {
      $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
      $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
      $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
    } else $str[] = null;
  }
  $Rupees = implode('', array_reverse($str));
  $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
  return ($Rupees ? $Rupees . 'Rs. Only ' : '') . $paise;
}

function getLedgerSummary($conn, $student_id)
{
  // Total Fee
  $totalFee = array();
  $remittedFee = array();
  $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  if ($debits->num_rows == 0) {
    generateStudentLedger($conn, $student_id);
    $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  }

  if ($debits->num_rows > 0) {
    while ($debit = $debits->fetch_assoc()) {
      if (empty($debit['Fee_Without_Sharing'])) {
        generateStudentLedger($conn, $student_id);
        $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
        while ($debit = $debits->fetch_assoc()) {
          $fees = json_decode($debit['Fee_Without_Sharing'], true);
          $totalFee[] = array_sum($fees);
        }
      } else {
        $fees = json_decode($debit['Fee_Without_Sharing'], true);
        $totalFee = $fees;
      }
    }
  }

  $credits = $conn->query("SELECT Fee FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 2");
  if ($credits->num_rows > 0) {
    while ($credit = $credits->fetch_assoc()) {
      $paid = $credit['Fee'];
      $remittedFee = $paid;
    }
  }

  return json_encode(['totalFee' => $totalFee, 'totalRemitted' => $remittedFee, 'totalBalance' => $totalFee - (int)$remittedFee]);
}


//kp
function getCenterIdFunc($conn, $subcenter_id = null)
{
  $subcenterQuery = $conn->query("SELECT Code, ID,Role FROM Users WHERE ID=$subcenter_id AND Role='Sub-Center'");
  $subcenterArr = $subcenterQuery->fetch_assoc();
  $subcentercode = explode('.', $subcenterArr["Code"]);
  $centerCode = $subcentercode[0];
  $centerQuery = $conn->query("SELECT  ID, Code, Role FROM Users WHERE Code='$centerCode' AND Role='Center'");
  $centerArr = $centerQuery->fetch_assoc();
  $center_id =  $centerArr['ID'];
  return $center_id;
}

