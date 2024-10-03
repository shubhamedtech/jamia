<?php
ini_set('display_errors', 1);
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $heads = array();
  $fee_heads = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = " . $_SESSION['university_id']);
  while ($fee_head = $fee_heads->fetch_assoc()) {
    $heads[$fee_head['ID']] = $fee_head['Name'];
  }

  $student = $conn->query("SELECT Admission_Sessions.Name as Session, Admission_Types.Name as Admission_Type, Courses.Short_Name as Course, Sub_Courses.Name as Sub_Course, Students.Duration as Duration, Student_Documents.Location, Modes.Name as Mode FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type = 'Photo' LEFT JOIN Modes ON Students.Mode_ID = Modes.ID WHERE Students.ID = $id");
  $student = $student->fetch_assoc();
?>
  <div class="card-body">
    <div class="row d-flex justify-content-center">
      <div class="col-md-3">
        <div class="text-center">
          <img class="profile_img" src="<?= $student['Location'] ?>" alt="">
          <h5><?= $student['Session'] ?> (<?= $student['Admission_Type'] ?>)</h5>
          <h6><?= $student['Course'] ?> (<?= $student['Sub_Course'] ?>)</h6>
        </div>
        <div class="py-3 text-center">
          <?php if (isset($_SESSION['gateway'])) { ?>
            <button type="button" class="btn btn-primary" disabled onclick="add('<?php echo $_SESSION['gateway'] == 1 ? 'easebuzz' : '' ?>', 'md')"> Pay Online</button>
          <?php } ?>
          <?php if (in_array($_SESSION['Role'], ['Administrator', 'Accountant'])) { ?>
            <button class="btn btn-primary btn-sm" disabled  onclick="add('offline-payments', 'lg')">Pay Offline</button>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-9">
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-hover table-borderless">
                <thead>
                  <tr>
                    <th><?= $student['Mode'] ?></th>
                    <th>Date</th>
                    <th>Particular</th>
                    <th>Source</th>
                    <th>Transaction ID</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Balance</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $balance = 0;
                  $credit = 0;
                  // echo "SELECT * FROM Student_Ledgers WHERE Student_Ledgers.Student_ID = $id AND Duration <= " . $student['Duration'] . " AND Status = 1 ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At";die;
                  $ledgers = $conn->query("SELECT Student_Ledgers.*, Payments.Amount as received, Payments.Gateway_ID as art_id,Payments.Added_By FROM Student_Ledgers LEFT JOIN Payments ON Payments.Transaction_ID = Student_Ledgers.Transaction_ID  WHERE Student_Ledgers.Student_ID = $id AND Student_Ledgers.Duration <= " . $student['Duration'] . " AND Student_Ledgers.Status = 1 ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At");
                  //  $ledgers = $conn->query("SELECT Student_Ledgers.*, Payments.Amount as received, Payments.Gateway_ID as art_id,Payments.Added_By,Invoices.User_ID AS Invoice_User_ID FROM Student_Ledgers LEFT JOIN Payments ON Payments.Transaction_ID = Student_Ledgers.Transaction_ID LEFT JOIN Invoices ON Payments.Transaction_ID = Invoices.Invoice_No WHERE Student_Ledgers.Student_ID = $id AND Student_Ledgers.Duration <= '" . $student['Duration'] . "' AND Student_Ledgers.Status = 1 GROUP BY Invoices.Invoice_No ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At");
                   while ($ledger = $ledgers->fetch_assoc()) {
                  // echo "<pre>"; print_r($ledger);
                    $credited = $ledger['Type'] == 2 ? $ledger['Fee'] : 0;
                    if ($credited != 0) {
                      $fees = json_decode($credited, true);
                      foreach ($fees as $feee) {
                        $credit = $feee;
                      };
                    }

                    if ($ledger['Type'] == 2) {
                      $students = $conn->query("SELECT CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID, Students.Created_At FROM Invoices LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE `User_ID` = " . $ledger['Added_By'] . " AND Invoice_No = '" . $ledger['Transaction_ID'] . "'  AND Invoices.University_ID = " . $_SESSION['university_id'] . " ");
                      $student_name = array();
                      while ($student = mysqli_fetch_assoc($students)) {
                        $student_name[] = $student['std_ID'];
                      }
                      $student_count =  count($student_name);
                      $credited_val = $ledger['received'];
                      $courseInvoiceArr=array();
                      $check_invoice_query = $conn->query("SELECT User_ID as Invoice_User_ID FROM Invoices Where Student_ID = '" . $ledger['Student_ID'] . "' AND Invoice_No = '" . $ledger['Transaction_ID'] . "'  AND University_ID = " . $_SESSION['university_id'] . " ");
                      $courseInvoiceArr = $check_invoice_query->fetch_assoc();
                      $check_role_query = $conn->query("SELECT ID,CanCreateSubCenter,Role FROM Users Where ID = '" . $courseInvoiceArr['Invoice_User_ID'] . "' AND Role='Center' AND CanCreateSubCenter=1");
                  
                      if ($check_role_query->num_rows > 0) {
                        // echo "center";die;
                        $course_name_query = $conn->query("SELECT Courses.Name as courseName, Students.Created_At,Students.Duration FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.ID= " . $ledger['Student_ID'] . " AND Students.University_ID = " . $_SESSION['university_id'] . " ");
                        $courseArr = $course_name_query->fetch_assoc();

                        if ($student_count >  0) {
                          $credited = 2000;
                          if (date("Y-m-d", strtotime($courseArr['Created_At'])) >= "2024-03-30") {
                            $discount_amount =  strpos($courseArr['courseName'], 'adeeb-e-mahir') !== false ? 1800 : 1600;
                            $credited = $credited_val +  $student_count * ($discount_amount);
                          }
                          $credited = $credited / $student_count;

                          $ledger['Fee'] = $credited;
                        } else {
                          $credited = 2000;
                          if (date("Y-m-d", strtotime($courseArr['Created_At'])) >= "2024-03-30") {
                            $discount_amount =  strpos($courseArr['courseName'], 'adeeb-e-mahir') !== false ? 1800 : 1600;
                            $credited = $credited_val +  $discount_amount;
                          }
                          $ledger['Fee'] = $credited;
                        }
                      } else {
                        
                        if ($student_count >  0) {
                          $credited = $credited_val / $student_count;
                        } else {
                          $credited = $ledger['received'];
                        }
                      }
                    }


                  ?>
                    <tr>
                      <td><?= $ledger['Duration'] ?></td>
                      <td><?= date("d-m-Y", strtotime($ledger['Date'])) ?></td>
                      <td><?php echo $debit = $ledger['Type'] == 1 ? "Due" : "Paid" ?></td>
                      <td><?= $ledger['Source'] ?></td>
                      <td><a href="/print/receipt/index.php?id=<?= $ledger['ID'] ?>" target="_blank"><u><?= $ledger['Transaction_ID'] ?></a>
                    </td></td>
                      <td class="text-right"><?php //echo $debit = $ledger['Type'] == 1 ? $ledger['Fee'] : 0; ?>
                      <?php if ((int)$ledger['Fee']) {
                        $ledger_fee = $ledger['Fee'];
                      } else {
                        $ledger_fees = json_decode($ledger['Fee'], true);
                        $ledger_fee = reset($ledger_fees);
                      }

                      ?>
                       &#8377; <?= number_format(abs($ledger_fee), 2) ?>
                    </td>
                      <td class="text-right"> &#8377; <?= number_format($credited, 2) ?></td>
                      <td class="text-right">&#8377; <?= number_format(($ledger['Type'] == 1 ? $ledger['Fee'] : $credited), 2) ?> </td>
                    </tr>
                  <?php } ?>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php }
?>