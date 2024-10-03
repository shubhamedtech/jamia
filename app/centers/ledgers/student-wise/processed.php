<?php
ini_set('display_errors', 1);

if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);
  $user_id = intval($_GET['id']);


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
    $students_wallets = $conn->query("SELECT Students.Added_For, Students.ID,Users.Name AS added_by_users, Users.Code, Students.First_Name, Students.Course_ID, Students.Sub_Course_ID, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration,Students.Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At, Wallet_Invoices.ID, Wallet_Payments.Transaction_ID, Wallet_Payments.Gateway_ID, Wallet_Invoices.Amount, Wallet_Invoices.Duration FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Wallet_Invoices ON Students.ID= Wallet_Invoices.Student_ID LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID WHERE Students.University_ID = " . $_SESSION['university_id'] . "  AND Students.Added_by IN($id) AND Step = 4 AND  Users.ID='$sub_center_id' AND Wallet_Payments.Status = 1 $sessionQuery ORDER BY Wallet_Payments.Created_At DESC");
  } else {
    $students_wallets = $conn->query("SELECT Students.Added_For,Wallet_Invoices.ID, Wallet_Payments.Transaction_ID, Wallet_Payments.Gateway_ID, Wallet_Invoices.Amount, Wallet_Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID, Wallet_Invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Wallet_Invoices LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID LEFT JOIN Students ON Wallet_Invoices.Student_ID = Students.ID WHERE Wallet_Invoices.`User_ID` IN ($id) AND Wallet_Invoices.University_ID = " . $_SESSION['university_id'] . " AND Wallet_Payments.Status = 1 $sessionQuery ORDER BY Wallet_Payments.Created_At DESC");
    if ($_SESSION['Role'] == "Administrator") {
      $students_wallets = $conn->query("SELECT Wallet_Invoices.ID,Students.Added_For, Wallet_Payments.Transaction_ID, Wallet_Payments.Gateway_ID, Wallet_Invoices.Amount, Wallet_Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID, Wallet_Invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Wallet_Invoices LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID LEFT JOIN Students ON Wallet_Invoices.Student_ID = Students.ID WHERE Wallet_Payments.Added_By IN ($id) AND Wallet_Invoices.University_ID = " . $_SESSION['university_id'] . " AND Wallet_Payments.Status = 1 $sessionQuery ORDER BY Wallet_Payments.Created_At DESC");
    }
  }


  $studentsArrData = array();

  while ($studentsArr = $students_wallets->fetch_assoc()) {
    $studentsArrData[] = $studentsArr;
  }

  if (isset($_GET['sub_center_id']) && !empty($_GET['sub_center_id'])) {
    $sub_center_id = $_GET['sub_center_id'];
    $students = $conn->query("SELECT Students.ID,Students.Added_For,Users.Name AS added_by_users, Users.Code, Students.First_Name, Students.Course_ID, Students.Sub_Course_ID, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration,Students.Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At, Invoices.ID, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount, Invoices.Duration FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Invoices ON Students.ID= Invoices.Student_ID LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID WHERE Students.University_ID = " . $_SESSION['university_id'] . "  AND Students.Added_By IN($id) AND Step = 4 AND  Users.ID='$sub_center_id' AND Payments.Status = 1 $sessionQuery ORDER BY Payments.Created_At DESC");
  } else {
    $students = $conn->query("SELECT Invoices.ID,Students.Added_For, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount, Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID,  Invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Invoices.`User_ID` IN ($id) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 1 $sessionQuery ORDER BY Payments.Created_At DESC");
    if ($students->num_rows == 0) {
      $students = $conn->query("SELECT Invoices.ID,Students.Added_For, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount, Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID,  Invoices.Created_At, Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Payments.Added_By IN ($id) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 1 $sessionQuery ORDER BY Payments.Created_At DESC");
    }
  }
  while ($students_offline_Arr = $students->fetch_assoc()) {
    $studentsArrData[] = $students_offline_Arr;
  }
  // echo "<pre>"; print_r($studentsArrData);die;  
 count($studentsArrData); 
?> <?php if (count($studentsArrData) == 0) { ?>
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
                <th>Processed On</th>
                <th>Particular</th>
                <th>Transaction ID</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <?php if ($_SESSION['Role'] != "Sub-Center") { ?>
                  <th>Added By</th>
                <?php } ?>
                <th>Duration</th>
                <th>Paid</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($studentsArrData as $student) {
                // while ($student = $students->fetch_assoc()) {
                //echo "<pre>"; print_r($student);
                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name']));
                
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
                } ?>
                <tr>
                  <td><?= date("d-m-Y", strtotime($student['Created_At'])) ?></td>
                  <td><?= $student['Gateway_ID'] ?></td>
                  <td><?= $student['Transaction_ID'] ?></td>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : sprintf("%'.06d\n", $student['ID']) ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <?php if ($_SESSION['Role'] != "Sub-Center") { ?>
                    <td>
                      <?= isset($added_by) ? $added_by : ''; ?>
                    </td>
                  <?php } ?>

                  <td><?= $student['Duration'] ?></td>
                  <td><?= (-1) * $student['Amount'] ?></td>
                  <td>
                    <center><span class="cursor-pointer text-danger font-weight-bold" onclick="cancelStudent('<?= $student['ID'] ?>', '<?= $id ?>')">Cancel</span></center>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>

  <script>
    function cancelStudent(id, center) {
      $.ajax({
        url: '/app/centers/ledgers/cancel/create?id=' + id + '&center=' + center,
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $("#mdmodal").modal('show');
        }
      })
    }
  </script>

<?php } ?>