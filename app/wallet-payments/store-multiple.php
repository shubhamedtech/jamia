<?php
if (isset($_POST['amount']) && isset($_POST['ids'])) {

  require '../../includes/db-config.php';
  include '../../includes/helpers.php';
  session_start();

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG", "pdf", "PDF");
  $file_folder = '../../uploads/offline-payments/';

  $ids = mysqli_real_escape_string($conn, $_POST['ids']);
  $ids = explode("|", $ids);
  $center_wallet = [];

  //$bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
  //$payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
  //$gateway_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);

  $transaction_id = strtoupper(strtolower(uniqid()));
  $file = $transaction_id;
  $payment_type = "Wallet";
  $bank_name = "Wallet";
  $gateway_id = $transaction_id;
  $amount = 0;
  $amount = mysqli_real_escape_string($conn, $_POST['amount']);

  $transaction_date = $transaction_date = date("Y-m-d");
  $student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';

  $check = $conn->query("SELECT ID FROM Wallet_Payments WHERE Transaction_ID = '$gateway_id' AND Type = 3 AND Payment_Mode != 'Cash'");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => 'Transaction ID already exists!']);
    exit();
  }

  $amount_update = 0;
  $paid_amount = 0;
  $amount_data = 0;
  $amount_check = $conn->query("SELECT sum(Amount) as total_amount FROM Wallets WHERE Added_By = " . $_SESSION['ID'] . " AND University_ID = " . $_SESSION['university_id'] . " ");
  if ($amount_check->num_rows > 0) {
    $amount_check = $amount_check->fetch_assoc();
    $amount_data = $amount_check['total_amount'];

    $amountQuery = $conn->query("SELECT sum(Amount) as paid_amount FROM Wallet_Payments  WHERE Added_By = " . $_SESSION['ID'] . " AND University_ID = " . $_SESSION['university_id'] . " ");
    if ($amountQuery->num_rows > 0) {
      $paid_amount_Arr = $amountQuery->fetch_assoc();
      $paid_amount = $paid_amount_Arr['paid_amount'];
    }
    $amount_update = $amount_data - $paid_amount;

  } else {
    echo json_encode(['status' => 400, 'message' => 'Please recharge wallet first!']);
    exit();
  }

  // echo $amount_update;die;

  if ($amount_update == 0 || $amount_update == '' || $amount_update == NULL) {
    echo json_encode(['status' => 400, 'message' => 'Please recharge wallet first!']);
    exit();
  }

  if ($amount_update < $amount) {
    echo json_encode(['status' => 400, 'message' => 'Please recharge wallet first!']);
    exit();
  }

  // GET center id
  if ($_SESSION['Role'] == 'Sub-Center') {
    $subcenterId = $_SESSION['ID'];
    $center_id = getCenterIdFunc($conn, $subcenterId);
    $center_sub_coursesArr = $conn->query("SELECT Fee, Course_ID, Sub_Course_ID FROM Center_Sub_Courses WHERE User_ID = $center_id AND University_ID=" . $_SESSION['university_id'] . "");
    while ($centerCourseFee = $center_sub_coursesArr->fetch_assoc()) {
      $feeArr[] = $centerCourseFee;
    }
  }

  $centerCourseFee = array();
  foreach ($ids as $id) {
    $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
    $duration = $duration->fetch_assoc();
    $duration = $duration['Duration'];
    $balance = balanceAmount($conn, $id, $duration);


    if ($_SESSION['Role'] == 'Sub-Center') {

      $added_for_column = ", `Added_For`";
      $student_id = base64_decode($id);
      $student_ids = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $student_id));
      $added_for_value = "," . $id;

      $studentCoursQuery = $conn->query("SELECT Added_For, Course_ID,Sub_Course_ID, University_ID FROM Students WHERE ID = $id");
      $studentCourseArr = $studentCoursQuery->fetch_assoc();

      $center_id = getCenterIdFunc($conn, $studentCourseArr['Added_For']);
      $center_sub_coursesArr = $conn->query("SELECT Fee, Course_ID, Sub_Course_ID FROM Center_Sub_Courses WHERE User_ID = $center_id AND Course_ID =" . $studentCourseArr['Course_ID'] . " AND Sub_Course_ID ='" . $studentCourseArr['Sub_Course_ID'] . "' AND University_ID=" . $studentCourseArr['University_ID'] . "");
      $centerCourseFee = $center_sub_coursesArr->fetch_assoc();
      // $centerFee = $centerCourseFee['Fee'];

      if ($center_sub_coursesArr) {
        $centerCourseFee = $center_sub_coursesArr->fetch_assoc();
        if ($centerCourseFee) {
          $centerFee = $centerCourseFee['Fee'];
        } else {
          $centerFee = null;
        }
      } else {
        $centerFee = null;
      }

      $center_wallet_amount = $balance - $centerFee; // center wallet amount 
      $payment_type = "Settelment By Sub-Center";

      // start kp
      $check_role_query = $conn->query("SELECT ID,CanCreateSubCenter,Role FROM Users Where ID = $center_id AND Role='Center' AND CanCreateSubCenter=1");
      if ($check_role_query->num_rows > 0) {
        $course_name_query = $conn->query("SELECT Courses.Name as courseName, Students.Created_At,Students.Duration FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.ID= $id AND Students.University_ID = " . $_SESSION['university_id'] . " ");
        $courseArr = $course_name_query->fetch_assoc();

        $credited = 2000;
        if (date("Y-m-d", strtotime($courseArr['Created_At'])) >= "2024-03-30") {
          $center_wallet_amount = strpos($courseArr['courseName'], 'adeeb-e-mahir') !== false ? 1800 : 1600;
        }
      }
      // end kp
      $add_wallet = $conn->query("INSERT INTO Wallets (Type, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, File, University_ID $added_for_column, Status) VALUES (1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$center_wallet_amount', '$payment_type',  " . $center_id . ", '$file', " . $_SESSION['university_id'] . " $added_for_value, 1)");
    }
    $add = $conn->query("INSERT INTO Wallet_Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES (" . $_SESSION['ID'] . ", $id, '$duration', " . $_SESSION['university_id'] . ", '$transaction_id', $balance)");
    $conn->query("UPDATE Students SET Process_By_Center = now() WHERE ID = $id ");
  }

  if ($add) {
    $add = $conn->query("INSERT INTO Wallet_Payments (Type, Status, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, File, University_ID) VALUES (3, 1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$amount', '$payment_type', " . $_SESSION['ID'] . ", '$file', " . $_SESSION['university_id'] . ")");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Payment added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
