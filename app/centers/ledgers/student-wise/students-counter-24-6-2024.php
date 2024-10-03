<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';
  $iddd[] = intval($_GET['id']);
  $ids = implode(',', $iddd);
  $id = intval($_GET['id']);

  $sessionQuery = "";
  if (isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])) {
    $admission_session_id = intval($_GET['admission_session_id']);
    $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
  }

  if ($_SESSION["Role"] == "Sub-Center") {
    $id = intval($_GET['id']);
  } else {
    $subcenter_id = array();
    $subcenter = $conn->query("SELECT Sub_Center FROM `Center_SubCenter` WHERE Center=$id ");
    while ($subcenterArr = $subcenter->fetch_assoc()) {
      $subcenter_id[] = $subcenterArr['Sub_Center'];
    }
    if (!empty($subcenter_id)) {
      $id .= "," . implode(",", array_filter($subcenter_id));
    }
  }

  $id = isset($id) ? $id : '';
  if (isset($_GET['sub_center_id']) && !empty($_GET['sub_center_id'])) {
    $sub_center_id = $_GET['sub_center_id'];
    $students_count = $conn->query("SELECT Students.ID, First_Name, Course_ID, Sub_Course_ID, Middle_Name, Last_Name, Unique_ID, Duration, Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . "  AND Added_by IN($id) AND Step = 4 AND  Users.ID='$sub_center_id' AND Process_By_Center IS NULL $sessionQuery ORDER BY Students.ID DESC");
  } else {
    if ($_SESSION['Role'] == "Administrator") {
      $students_count = $conn->query("SELECT Students.ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration,Added_For, Course_ID, Sub_Course_ID, Admission_Sessions.Name as Admission_Session FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND  Added_For IN ($id) AND Step = 4 AND Process_By_Center IS NULL $sessionQuery ORDER BY Students.ID DESC");
    } else {
      $students_count = $conn->query("SELECT Students.ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration, Added_For, Course_ID, Sub_Course_ID, Admission_Sessions.Name as Admission_Session FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND  Added_For IN ($id) AND Step = 4 AND Process_By_Center IS NULL $sessionQuery ORDER BY Students.ID DESC");
    }
  }

  $pending_counter = array();
  $added_for[] = intval($_GET['id']);
  $downlines = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting =  " . $_GET['id'] . "");
  while ($downline = $downlines->fetch_assoc()) {
    $added_for[] = $downline['User_ID'];
  }

  $users = implode(",", array_filter($added_for));

  $already = array();
  $already_ids = array();
  $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID AND Payments.Type = 1 WHERE `User_ID` = " . $_GET['id'] . " AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2");
  while ($invoice = $invoices->fetch_assoc()) {
    $already[$invoice['Student_ID']] = $invoice['Duration'];
    $already_ids[] = $invoice['Student_ID'];
  }

  $query = empty($already_ids) ? " AND ID IS NULL" : " AND ID IN (" . implode(',', $already_ids) . ")";

  if (isset($_GET['sub_center_id']) && !empty($_GET['sub_center_id'])) {

    $sub_center_id = $_GET['sub_center_id'];
    $pending_count = $conn->query("SELECT Students.ID,Users.Name AS added_by_users, Users.Code, Students.First_Name, Students.Course_ID, Students.Sub_Course_ID, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration,Students.Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Invoices ON Students.ID= Invoices.Student_ID LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID WHERE Students.University_ID = " . $_SESSION['university_id'] . "  AND Students.Added_by IN($id) AND Step = 4 AND  Users.ID='$sub_center_id' AND Payments.Status = 0 $sessionQuery ORDER BY Students.ID DESC");
  } else {
    $pending_count = $conn->query("SELECT Students.ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration,Admission_Sessions.Name as Admission_Session FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Payments.Added_By IN ($users) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 0 $sessionQuery ORDER BY Students.ID DESC");
    if ($pending_count->num_rows == 0) {
      $pending_count = $conn->query("SELECT Students.ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Unique_ID , Students.Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID  LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Payments.Added_By IN ($users) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 0 $sessionQuery ORDER BY Students.ID DESC");
    }
  }


  while ($student = $pending_count->fetch_assoc()) {
    $pending_counter[] = $pending_counter;
  }

    if (isset($_GET['sub_center_id']) && !empty($_GET['sub_center_id'])) {
      $sub_center_id = $_GET['sub_center_id'];
      $processed_count = $conn->query("SELECT Students.ID,Users.Name AS added_by_users, Users.Code, Students.First_Name, Students.Course_ID, Students.Sub_Course_ID, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration,Students.Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At, Invoices.ID, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount, Invoices.Duration FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Invoices ON Students.ID= Invoices.Student_ID LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID WHERE Students.University_ID = " . $_SESSION['university_id'] . "  AND Students.Added_By IN($id) AND Step = 4 AND  Users.ID='$sub_center_id' AND Payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");
    } else {
      $processed_count = $conn->query("SELECT Invoices.ID, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount, Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID,  Invoices.Created_At FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Invoices.`User_ID` IN ($id) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 1 $sessionQuery");
    }
    $processed_countrer = array();

    while ($student = $processed_count->fetch_assoc()) {
      $processed_countrer[] = $student;
    }

    if (isset($_GET['sub_center_id']) && !empty($_GET['sub_center_id'])) {
      $sub_center_id = $_GET['sub_center_id'];
      $processed_wallets = $conn->query("SELECT Students.ID,Users.Name AS added_by_users, Users.Code, Students.First_Name, Students.Course_ID, Students.Sub_Course_ID, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration,Students.Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At, Wallet_Invoices.ID, Wallet_Payments.Transaction_ID, Wallet_Payments.Gateway_ID, Wallet_Invoices.Amount, Wallet_Invoices.Duration FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Wallet_Invoices ON Students.ID= Wallet_Invoices.Student_ID LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID WHERE Students.University_ID = " . $_SESSION['university_id'] . "  AND Students.Added_By IN($id) AND Step = 4 AND  Users.ID='$sub_center_id' AND Wallet_Payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");

      } else {
      $processed_wallets = $conn->query("SELECT Wallet_Invoices.ID, Wallet_Payments.Transaction_ID, Wallet_Payments.Gateway_ID, Wallet_Invoices.Amount, Wallet_Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID, Wallet_Invoices.Created_At FROM Wallet_Invoices LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID LEFT JOIN Students ON Wallet_Invoices.Student_ID = Students.ID WHERE Wallet_Invoices.`User_ID` IN ($id) AND Wallet_Invoices.University_ID = " . $_SESSION['university_id'] . " AND Wallet_Payments.Status = 1 $sessionQuery");
    }
    
    while ($student = $processed_wallets->fetch_assoc()) {
      $processed_countrer[] = $student;
    }
} ?>

<?php
if ($_SESSION['Role'] == 'Center') {

  $active_class = "active";
} else {
  $active_class = '';
} ?>

<ul class="nav nav-tabs" id="all-counter" data-init-reponsive-tabs="dropdownfx">
  <li class="nav-item" id="counter_student">
    <a class="nav-link <?= $active_class ?>" data-toggle="tab" data-target="#students" href="#"><span>Students</span>-<span id="applied_student_count"><?= $students_count->num_rows == 0 ? 0 : $students_count->num_rows ?></span></a>
  </li>
  <li class="nav-item" id="counter_pending">
    <a class="nav-link " data-toggle="tab" data-target="#pending" href="#"><span>Pending</span>-<span id="pending_student_count"><?= count($pending_counter) ?></span></a>
  </li>
  <li class="nav-item" id="counter_processed">
    <a class="nav-link" data-toggle="tab" data-target="#processed" href="#"><span>Processed</span>-<span id="processed_student_count"><?= count($processed_countrer) ?></span></a>
  </li>
</ul>