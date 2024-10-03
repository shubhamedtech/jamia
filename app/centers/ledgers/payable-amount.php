<?php
if (isset($_POST['ids']) && isset($_POST['center'])) {
  require '../../../includes/db-config.php';
  require '../../../includes/helpers.php';
  session_start();

  $center = intval($_POST['center']);
  $ids = is_array($_POST['ids']) ? array_filter($_POST['ids']) : [];

  if (empty($ids)) {
    exit(json_encode(['status' => false, 'message' => 'Please select student!']));
  }

  $invoice_no = strtoupper(uniqid('IN'));
  $balance = array();

  // foreach ($ids as $id) {
  //   $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
  //   $duration = $duration->fetch_assoc();
  //   $duration = $duration['Duration'];
  //   $balance[] = balanceAmount($conn, $id, $duration);
  // }
  // $amount = array_sum($balance);
  // $amount = $amount < 0 ? (-1) * $amount : $amount;
  // if($_SESSION['Role'] == "Center" && $_SESSION['CanCreateSubCenter'] == 1){
  //  	 $amount =  $amount - 2000;
  // 	}

  // start kp 
  foreach ($ids as $key => $id) {
    $durationQuery = $conn->query("SELECT Students.Duration, Courses.Name AS courseName FROM Students LEFT JOIN Courses ON Courses.ID = Students.Course_ID WHERE Students.ID = $id");
    $durationArr = $durationQuery->fetch_assoc();
    $duration = $durationArr['Duration'];
    $amounts1[] = balanceAmount($conn, $id, $duration);

    if ($_SESSION['Role'] == "Center" && $_SESSION['CanCreateSubCenter'] == 1) {
      $deductableAmount = 2000;
      $deductableAmount =  strpos($durationArr['courseName'], 'adeeb-e-mahir') !== false ? 1800 : 1600;
      foreach ($amounts1 as $amount) {
        $amounttt = $amount < 0 ? (-1) * $amount : $amount;
        $amounts2 = $amounttt - $deductableAmount;
      }
      $amounts[] = $amounts2;
    } else {
      $amounts = $amounts1;
    }
  }

  $amounts = array_sum($amounts);

  echo json_encode(['status' => true, 'amount' => $amounts]);
}
