<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);

  $added_for[] = $id;
  $sub_centers = $conn->query("SELECT Sub_Center FROM Center_SubCenter WHERE Center = " . $_GET['id'] . "");
  while ($sub_center = $sub_centers->fetch_assoc()) {
    $added_for[] = $sub_center['Sub_Center'];
  }

  $users = implode(",", array_filter($added_for));

  $already = array();
  $already_ids = array();
  $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID AND Payments.Type = 1 WHERE `User_ID` = $id AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2");
  while ($invoice = $invoices->fetch_assoc()) {
    $already[$invoice['Student_ID']] = $invoice['Duration'];
    $already_ids[] = $invoice['Student_ID'];
  }

  $query = empty($already_ids) ? " AND Students.ID IS NULL" : " AND Students.ID IN (" . implode(',', $already_ids) . ")";

  $sessionQuery = "";
  if (isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])) {
    $admission_session_id = intval($_GET['admission_session_id']);
    $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
  }


  if ($_SESSION["Role"] == "Sub-Center") {
    $users = intval($_GET['id']);
  } else {
    $subcenter_id = array();
    $subcenter = $conn->query("SELECT Sub_Center FROM `Center_SubCenter` WHERE Center=$id ");
    while ($subcenterArr = $subcenter->fetch_assoc()) {
      $subcenter_id[] = $subcenterArr['Sub_Center'];
    }

    if (!empty($subcenter_id)) {
      $users .= "," . implode(",", array_filter($subcenter_id));
    }
  }

  if (isset($_GET['sub_center_id']) && !empty($_GET['sub_center_id'])) {
    $sub_center_id = $_GET['sub_center_id'];
    $students = $conn->query("SELECT Students.ID,Users.Name AS added_by_users, Users.Code, Students.First_Name, Students.Course_ID, Students.Sub_Course_ID, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration,Students.Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Invoices ON Students.ID= Invoices.Student_ID LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID WHERE Students.University_ID = " . $_SESSION['university_id'] . "  AND Students.Added_by IN($users) AND Step = 4 AND  Users.ID='$sub_center_id' AND Payments.Status = 0 $sessionQuery ORDER BY Payments.Created_At DESC");
  } else {
    $students = $conn->query("SELECT Students.ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration, Students.Added_By,Students.Added_For, Students.Course_ID, Students.Sub_Course_ID FROM Students LEFT JOIN Invoices ON Students.ID =Invoices.Student_ID LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID  WHERE Payments.Added_By IN ($users) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 0 $sessionQuery ORDER BY Payments.Created_At DESC");
    if ($students->num_rows == 0) {
      $students = $conn->query("SELECT Students.ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Unique_ID , Students.Duration, Students.Added_By,Students.Added_For, Students.Course_ID, Students.Sub_Course_ID FROM Students LEFT JOIN Invoices ON Students.ID =Invoices.Student_ID LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID  WHERE Payments.Added_By IN ($id) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 0 $sessionQuery ORDER BY Payments.Created_At DESC");
    }
  }
  

 if ($students->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No student(s) found!
      </div>
    </div>
  <?php } else {
  ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <?php if ($_SESSION['Role'] != "Sub-Center") { ?>
                <th>Added By</th>
                <?php } ?>
                <th>Payable</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($student = $students->fetch_assoc()) {

                  if (isset($student['Added_For'])) {
                    $roleQuery = $conn->query("SELECT Name, Code,Role FROM Users Where ID =" . $student['Added_For'] . "");
                    $roleArr = $roleQuery->fetch_assoc();
                    $code = isset($roleArr['Code']) ? $roleArr['Code'] : '';

                    if ($roleArr['Role'] == "Center" && ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Administrator")) {
                      $added_by = "Self";
                    } else if ($_SESSION['Role'] == "Administrator" && $roleArr['Role'] == "Administrator") {
                    
                      $added_by = isset($roleArr['Name']) ? $roleArr['Name'] : '';
                      
                    } else {
                      $user_name = isset($roleArr['Name']) ? $roleArr['Name'] : '';
                      $added_by = $user_name . "(" . $code . ")";
                    }
                  }
               

                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) ?>
                <tr>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : sprintf("%'.06d\n", $student['ID']) ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <?php if ($_SESSION['Role'] != "Sub-Center") { ?>
                    <td>
                      <?= isset($added_by) ? $added_by : ''; ?>
                    </td>
                  <?php } ?>

                  <td>
                    <?php
                    $balance = 0;

                    // $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= " . $student['Duration']);
                    // while ($ledger = $ledgers->fetch_assoc()) {
                    //   // $fees = json_decode($ledger['Fee'], true);
                    //   // foreach ($fees as $key => $value) {
                    //   //   $debit = $ledger['Type'] == 1 ? $value : 0;
                    //   //   $credit = $ledger['Type'] == 2 ? $value : 0;
                    //   //   $balance = ($balance + $credit) - $debit;
                    //   // }
                    //   $balance = $ledger['Fee'];
                    // }
                    // echo "&#8377; " . (-1) * $balance;
                    $feeQuery = $conn->query("SELECT Amount FROM Invoices WHERE Student_ID = " . $student['ID'] . " AND Duration <= " . $student['Duration']);
                    $feeArr = $feeQuery->fetch_assoc();
                    $fee_balance = $feeArr['Amount'];
                    echo "&#8377; " . (-1) * $fee_balance;



                    // $user_sub_centers = $conn->query("SELECT Center FROM Center_SubCenter WHERE Sub_Center = " . $student['Added_For'] . " ");
                    // $user_sub_centers = $user_sub_centers->fetch_assoc();
                    // // echo "<pre>"; print_r($user_sub_centers);die;

                    // $user_can_create_sub_centers = $conn->query("SELECT CanCreateSubCenter FROM Users WHERE ID = " . $student['Added_For'] . " AND Role = 'Center' ");
                    // if($user_can_create_sub_centers->num_rows > 0){
                    // 	$can_create_sub_centers = $user_can_create_sub_centers->fetch_assoc();
                    // } else {
                    // 	$user_can_create_sub_centers = $conn->query("SELECT CanCreateSubCenter FROM Users WHERE ID = " . $user_sub_centers['Center'] . " AND Role = 'Center' ");
                    //   	$can_create_sub_centers = $user_can_create_sub_centers->fetch_assoc();
                    // }


                    // if($can_create_sub_centers['CanCreateSubCenter'] == 1){
                    //   $deductableAmount = 2000;
                    //   if(date("Y-m-d", strtotime($student['Created_At']))>= "2024-03-30"){
                    //     $deductableAmount =  strpos($student['courseName'], 'adeeb-e-mahir')!==false ? 1800 : 1600;
                    //   }

                    // 	echo "&#8377; " . (-1) *( $balance - $deductableAmount);
                    // } else {
                    //  	echo "&#8377; " . (-1) * $balance;
                    // }

                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<?php }
}

?>